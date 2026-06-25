<?php

namespace App\Http\Controllers;

use App\Http\Requests\PipelineProspectRequest;
use App\Models\Property;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PipelineController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['property_id', 'status', 'search']);

        $prospects = Prospect::query()
            ->with(['property'])
            ->when($filters['property_id'] ?? null, fn ($query, $propertyId) => $query->where('property_id', $propertyId))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('tenant', 'like', "%{$search}%")
                        ->orWhere('broker', 'like', "%{$search}%")
                        ->orWhere('use_type', 'like', "%{$search}%")
                        ->orWhere('suite', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('pipeline.index', [
            'prospects' => $prospects,
            'properties' => Property::orderBy('name')->get(),
            'statuses' => Prospect::STATUSES,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('pipeline.create', [
            'prospect' => new Prospect([
                'property_id' => request('property_id'),
                'status' => Prospect::STATUS_PROSPECT,
                'visible_to_client' => true,
                'sort_order' => 0,
            ]),
            'properties' => Property::orderBy('name')->get(),
            'statuses' => Prospect::STATUSES,
        ]);
    }

    public function store(PipelineProspectRequest $request): RedirectResponse
    {
        $prospect = Prospect::create($request->prospectData());

        $this->recordActivity(
            prospect: $prospect,
            type: ProspectActivity::TYPE_CREATED,
            subject: 'Prospect created',
            body: $prospect->notes,
            statusTo: $prospect->status,
        );

        return redirect()
            ->route('pipeline.index')
            ->with('status', 'Prospect created.');
    }

    public function edit(Prospect $prospect): View
    {
        return view('pipeline.edit', [
            'prospect' => $prospect->load('property'),
            'properties' => Property::orderBy('name')->get(),
            'statuses' => Prospect::STATUSES,
        ]);
    }

    public function update(PipelineProspectRequest $request, Prospect $prospect): RedirectResponse
    {
        $originalStatus = $prospect->status;

        $prospect->fill($request->prospectData());
        $changed = $prospect->isDirty();
        $statusChanged = $prospect->isDirty('status');
        $prospect->save();

        if ($statusChanged) {
            $this->recordActivity(
                prospect: $prospect,
                type: ProspectActivity::TYPE_STATUS_CHANGE,
                subject: 'Status changed to '.Prospect::statusLabel($prospect->status),
                body: $prospect->notes,
                statusFrom: $originalStatus,
                statusTo: $prospect->status,
            );
        } elseif ($changed) {
            $this->recordActivity(
                prospect: $prospect,
                type: ProspectActivity::TYPE_UPDATED,
                subject: 'Prospect details updated',
                body: $prospect->notes,
            );
        }

        return redirect()
            ->route('pipeline.edit', $prospect)
            ->with('status', 'Prospect updated.');
    }

    private function recordActivity(
        Prospect $prospect,
        string $type,
        string $subject,
        ?string $body = null,
        ?string $statusFrom = null,
        ?string $statusTo = null,
    ): void {
        ProspectActivity::create([
            'prospect_id' => $prospect->id,
            'property_id' => $prospect->property_id,
            'user_id' => auth()->id(),
            'team_member_id' => auth()->user()?->teamMember?->id,
            'type' => $type,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'subject' => $subject,
            'body' => $body,
            'occurred_at' => now(),
        ]);
    }
}
