<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use App\Models\Prospect;
use App\Models\PropertyLink;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(): View
    {
        $properties = Property::query()
            ->withCount([
                'prospects',
                'visibleProspects',
            ])
            ->withMax('activities', 'occurred_at')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('properties.index', [
            'properties' => $properties,
        ]);
    }

    public function create(): View
    {
        return view('properties.create', [
            'property' => new Property([
                'is_active' => true,
            ]),
            'teamMembers' => TeamMember::orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function show(Property $property): View
    {
        $property->load([
            'links',
            'teamMembers',
            'marketingActivities' => fn ($query) => $query
                ->with('user')
                ->latest('activity_date')
                ->limit(10),
            'prospects' => fn ($query) => $query
                ->with('assignedTeamMember')
                ->orderBy('sort_order')
                ->latest('updated_at'),
            'activities' => fn ($query) => $query
                ->with(['prospect', 'teamMember', 'user'])
                ->latest('occurred_at')
                ->limit(30),
        ]);

        return view('properties.show', [
            'property' => $property,
            'displayTeamMembers' => $property->teamMembers->isNotEmpty()
                ? $property->teamMembers->where('is_active', true)->values()
                : TeamMember::where('is_active', true)->orderBy('name')->get(),
            'metrics' => [
                'totalProspects' => $property->prospects->count(),
                'visibleProspects' => $property->prospects->where('visible_to_client', true)->count(),
                'activeProspects' => $property->prospects->where('is_active', true)->count(),
                'tours' => $property->prospects
                    ->whereIn('status', [Prospect::STATUS_TOUR_SCHEDULED, Prospect::STATUS_TOUR_COMPLETED])
                    ->count(),
                'proposals' => $property->prospects
                    ->whereIn('status', [Prospect::STATUS_PROPOSAL_SENT, Prospect::STATUS_PROPOSAL_ACCEPTED])
                    ->count(),
                'leases' => $property->prospects->where('status', Prospect::STATUS_LEASE_SIGNED)->count(),
                'inactive' => $property->prospects->where('status', Prospect::STATUS_INACTIVE)->count(),
                'lastActivity' => $property->activities->first()?->occurred_at,
                'visibleLinks' => $property->links->where('is_visible_to_client', true)->count(),
                'broadcastLinks' => $property->links->where('type', PropertyLink::TYPE_BROADCAST_EMAIL)->count(),
                'documentLinks' => $property->links
                    ->whereIn('type', [PropertyLink::TYPE_DROPBOX, PropertyLink::TYPE_BROCHURE, PropertyLink::TYPE_FILE])
                    ->count(),
                'totalMarketingActivities' => $property->marketingActivities()->count(),
                'visibleMarketingActivities' => $property->visibleMarketingActivities()->count(),
                'latestMarketingActivityDate' => $property->marketingActivities()->latest('activity_date')->first()?->activity_date,
            ],
        ]);
    }

    public function store(PropertyRequest $request): RedirectResponse
    {
        $property = new Property($request->propertyData());
        $property->syncStatusFromActiveFlag();
        $property->save();
        $property->teamMembers()->sync($request->teamMemberIds());

        return redirect()
            ->route('properties.index')
            ->with('status', 'Property created.');
    }

    public function edit(Property $property): View
    {
        return view('properties.edit', [
            'property' => $property,
            'teamMembers' => TeamMember::orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function update(PropertyRequest $request, Property $property): RedirectResponse
    {
        $property->fill($request->propertyData());
        $property->syncStatusFromActiveFlag();
        $property->save();
        $property->teamMembers()->sync($request->teamMemberIds());

        return redirect()
            ->route('properties.edit', $property)
            ->with('status', 'Property updated.');
    }
}
