<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $properties = Property::query()
            ->withCount(['visibleProspects', 'visibleMarketingActivities'])
            ->withMax('activities', 'occurred_at')
            ->withMax('visibleMarketingActivities', 'activity_date')
            ->latest('updated_at')
            ->orderBy('name')
            ->get();

        return view('reports.index', [
            'properties' => $properties,
        ]);
    }

    public function show(Request $request, Property $property): View
    {
        return app(ClientReportController::class)->show($request, $property);
    }
}
