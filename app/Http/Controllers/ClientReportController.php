<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Prospect;
use App\Models\MarketingActivity;
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
            'rentRollEntries',
            'visibleMarketingActivities' => fn ($query) => $query
                ->whereIn('type', MarketingActivity::SUPPORTED_TYPES)
                ->latest('activity_date')
                ->limit(8),
            'prospects' => fn ($query) => $query
                ->where('visible_to_client', true),
        ]);
        $range = $request->query('range') === 'all' ? 'all' : '15';
        $rangeStart = now()->subDays(14)->startOfDay();
        $reportProspects = $property->prospects
            ->when($range === '15', fn ($prospects) => $prospects->filter(
                fn (Prospect $prospect) => ($prospect->opportunity_date ?? $prospect->created_at)->gte($rangeStart)
            ))
            ->values();

        $generatedAt = now();
        $lastUpdatedAt = collect([
            $property->updated_at,
            $property->prospects->max('updated_at'),
            $property->visibleMarketingActivities->max('activity_date'),
            $property->visibleLinks->max('updated_at'),
            $property->rentRollEntries->max('updated_at'),
        ])
            ->filter()
            ->map(fn ($date) => $date instanceof Carbon ? $date : Carbon::parse($date))
            ->sort()
            ->last();
        return view('client.report', [
            'property' => $property,
            'isInternalReportView' => $request->user()->hasRole('admin', 'staff'),
            'lastUpdatedAt' => $lastUpdatedAt ?? $generatedAt,
            'range' => $range,
            'reportProspects' => $reportProspects,
            'metrics' => [
                'activeProspects' => $reportProspects->where('status', Prospect::STATUS_PROSPECT)->count(),
                'tours' => $reportProspects
                    ->whereIn('status', [Prospect::STATUS_TOUR_SCHEDULED, Prospect::STATUS_TOUR_COMPLETED])
                    ->count(),
                'proposals' => $reportProspects
                    ->whereIn('status', [Prospect::STATUS_PROPOSAL_SENT, Prospect::STATUS_PROPOSAL_ACCEPTED])
                    ->count(),
                'leases' => $reportProspects->where('status', Prospect::STATUS_LEASE_SIGNED)->count(),
                'marketingActivity' => $property->visibleMarketingActivities->count(),
            ],
            'marketingActivities' => $property->visibleMarketingActivities,
        ]);
    }

}
