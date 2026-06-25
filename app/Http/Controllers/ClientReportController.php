<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ClientReportController extends Controller
{
    public function index(Request $request): View
    {
        $properties = $request->user()
            ->properties()
            ->where('is_active', true)
            ->withCount([
                'prospects' => fn ($query) => $query->where('visible_to_client', true),
            ])
            ->orderBy('name')
            ->get();

        return view('client.properties', [
            'properties' => $properties,
        ]);
    }

    public function show(Request $request, Property $property): View
    {
        if ($request->user()->isClient()) {
            abort_unless(
                $property->is_active && $request->user()->properties()->whereKey($property->id)->exists(),
                403
            );
        }

        $property->load([
            'visibleLinks',
            'visibleMarketingActivities' => fn ($query) => $query
                ->latest('activity_date')
                ->limit(8),
            'teamMembers' => fn ($query) => $query->where('is_active', true)->orderBy('name'),
            'prospects' => fn ($query) => $query
                ->where('visible_to_client', true)
                ->with('assignedTeamMember'),
            'activities' => fn ($query) => $query
                ->whereHas('prospect', fn ($query) => $query->where('visible_to_client', true))
                ->with(['prospect', 'teamMember'])
                ->latest('occurred_at')
                ->limit(12),
        ]);

        $statusCounts = $property->prospects()
            ->where('visible_to_client', true)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $generatedAt = now();
        $lastUpdatedAt = collect([
            $property->updated_at,
            $property->prospects->max('updated_at'),
            $property->activities->max('occurred_at'),
            $property->visibleMarketingActivities->max('activity_date'),
            $property->visibleLinks->max('updated_at'),
        ])
            ->filter()
            ->map(fn ($date) => $date instanceof Carbon ? $date : Carbon::parse($date))
            ->sort()
            ->last();

        return view('client.report', [
            'property' => $property,
            'isInternalReportView' => $request->user()->hasRole('admin', 'staff'),
            'generatedAt' => $generatedAt,
            'lastUpdatedAt' => $lastUpdatedAt ?? $generatedAt,
            'statusCounts' => collect(Prospect::STATUSES)
                ->mapWithKeys(fn (string $status) => [$status => $statusCounts->get($status, 0)]),
            'metrics' => [
                'occupancy' => $this->occupancyFor($property),
                'activeProspects' => $property->prospects->where('is_active', true)->count(),
                'tours' => $property->prospects
                    ->whereIn('status', [Prospect::STATUS_TOUR_SCHEDULED, Prospect::STATUS_TOUR_COMPLETED])
                    ->count(),
                'proposals' => $property->prospects
                    ->whereIn('status', [Prospect::STATUS_PROPOSAL_SENT, Prospect::STATUS_PROPOSAL_ACCEPTED])
                    ->count(),
                'leases' => $property->prospects->where('status', Prospect::STATUS_LEASE_SIGNED)->count(),
                'marketingActivity' => $property->visibleMarketingActivities->count(),
            ],
            'teamMembers' => $property->teamMembers,
            'marketingActivities' => $property->visibleMarketingActivities,
        ]);
    }

    private function occupancyFor(Property $property): int
    {
        if (! $property->unit_count) {
            return 0;
        }

        $leased = $property->prospects->where('status', Prospect::STATUS_LEASE_SIGNED)->count();

        return min(100, (int) round(($leased / $property->unit_count) * 100));
    }
}
