<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyLinkRequest;
use App\Models\Property;
use App\Models\PropertyLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['property_id', 'visible_to_client', 'search']);

        $links = PropertyLink::query()
            ->with('property')
            ->when($filters['property_id'] ?? null, fn ($query, $propertyId) => $query->where('property_id', $propertyId))
            ->when(($filters['visible_to_client'] ?? '') !== '', fn ($query) => $query->where('is_visible_to_client', (bool) $filters['visible_to_client']))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('label', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documents.index', [
            'links' => $links,
            'properties' => Property::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): View
    {
        return view('documents.create', [
            'propertyLink' => new PropertyLink([
                'property_id' => $request->query('property_id'),
                'is_visible_to_client' => true,
            ]),
            'properties' => Property::orderBy('name')->get(),
        ]);
    }

    public function store(PropertyLinkRequest $request): RedirectResponse
    {
        PropertyLink::create($request->propertyLinkData());

        return redirect()
            ->route('documents.index', ['property_id' => $request->integer('property_id')])
            ->with('status', 'Document link created.');
    }

    public function edit(PropertyLink $propertyLink): View
    {
        return view('documents.edit', [
            'propertyLink' => $propertyLink,
            'properties' => Property::orderBy('name')->get(),
        ]);
    }

    public function update(PropertyLinkRequest $request, PropertyLink $propertyLink): RedirectResponse
    {
        $propertyLink->update($request->propertyLinkData());

        return redirect()
            ->route('documents.edit', $propertyLink)
            ->with('status', 'Document link updated.');
    }

    public function destroy(PropertyLink $propertyLink): RedirectResponse
    {
        $propertyId = $propertyLink->property_id;
        $propertyLink->delete();

        return redirect()
            ->route('documents.index', ['property_id' => $propertyId])
            ->with('status', 'Document link deleted.');
    }
}
