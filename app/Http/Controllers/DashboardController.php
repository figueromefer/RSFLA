<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\TeamMember;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'propertyCount' => Property::count(),
            'activePropertyCount' => Property::where('is_active', true)->count(),
            'visibleProspectCount' => Prospect::where('visible_to_client', true)->count(),
            'lastActivity' => ProspectActivity::latest('occurred_at')->first(),
            'activeProspectCount' => Prospect::where('is_active', true)->count(),
            'leaseCount' => Prospect::where('status', Prospect::STATUS_LEASE_SIGNED)->count(),
            'teamMemberCount' => TeamMember::where('is_active', true)->count(),
            'recentActivities' => ProspectActivity::with(['property', 'prospect', 'teamMember'])
                ->latest('occurred_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
