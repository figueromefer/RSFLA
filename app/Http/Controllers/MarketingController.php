<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarketingActivityRequest;
use App\Models\MarketingActivity;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['property_id', 'type', 'visible_to_client', 'search']);

        $activities = MarketingActivity::query()
            ->with(['property', 'user'])
            ->when($filters['property_id'] ?? null, fn ($query, $propertyId) => $query->where('property_id', $propertyId))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when(($filters['visible_to_client'] ?? '') !== '', fn ($query) => $query->where('visible_to_client', (bool) $filters['visible_to_client']))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('activity_date')
            ->paginate(15)
            ->withQueryString();

        return view('marketing.index', [
            'activities' => $activities,
            'properties' => Property::orderBy('name')->get(),
            'types' => MarketingActivity::TYPES,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        return view('marketing.create', [
            'marketingActivity' => new MarketingActivity([
                'property_id' => $request->query('property_id'),
                'type' => MarketingActivity::TYPE_BROADCAST_EMAIL,
                'activity_date' => now(),
                'visible_to_client' => true,
            ]),
            'properties' => Property::orderBy('name')->get(),
            'types' => MarketingActivity::TYPES,
        ]);
    }

    public function store(MarketingActivityRequest $request): RedirectResponse
    {
        MarketingActivity::create([
            ...$request->marketingActivityData(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('marketing.index', ['property_id' => $request->integer('property_id')])
            ->with('status', 'Marketing activity created.');
    }

    public function edit(MarketingActivity $marketingActivity): View
    {
        return view('marketing.edit', [
            'marketingActivity' => $marketingActivity,
            'properties' => Property::orderBy('name')->get(),
            'types' => MarketingActivity::TYPES,
        ]);
    }

    public function update(MarketingActivityRequest $request, MarketingActivity $marketingActivity): RedirectResponse
    {
        $marketingActivity->update([
            ...$request->marketingActivityData(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('marketing.edit', $marketingActivity)
            ->with('status', 'Marketing activity updated.');
    }

    public function destroy(MarketingActivity $marketingActivity): RedirectResponse
    {
        $propertyId = $marketingActivity->property_id;
        $marketingActivity->delete();

        return redirect()
            ->route('marketing.index', ['property_id' => $propertyId])
            ->with('status', 'Marketing activity deleted.');
    }
}
