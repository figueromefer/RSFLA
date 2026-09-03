<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentRollEntryRequest;
use App\Models\Property;
use App\Models\RentRollEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RentRollEntryController extends Controller
{
    public function create(Property $property): View
    {
        return view('rent-roll.create', ['property' => $property, 'rentRollEntry' => new RentRollEntry()]);
    }

    public function store(RentRollEntryRequest $request, Property $property): RedirectResponse
    {
        $property->rentRollEntries()->create($request->validated());

        return redirect()->route('properties.edit', $property)->with('status', 'Tenant added to rent roll.');
    }

    public function edit(Property $property, RentRollEntry $rentRollEntry): View
    {
        $this->ensureEntryBelongsToProperty($property, $rentRollEntry);

        return view('rent-roll.edit', compact('property', 'rentRollEntry'));
    }

    public function update(RentRollEntryRequest $request, Property $property, RentRollEntry $rentRollEntry): RedirectResponse
    {
        $this->ensureEntryBelongsToProperty($property, $rentRollEntry);
        $rentRollEntry->update($request->validated());

        return redirect()->route('properties.edit', $property)->with('status', 'Rent roll entry updated.');
    }

    public function destroy(Property $property, RentRollEntry $rentRollEntry): RedirectResponse
    {
        $this->ensureEntryBelongsToProperty($property, $rentRollEntry);
        $rentRollEntry->delete();

        return redirect()->route('properties.edit', $property)->with('status', 'Tenant removed from rent roll.');
    }

    private function ensureEntryBelongsToProperty(Property $property, RentRollEntry $rentRollEntry): void
    {
        abort_unless($rentRollEntry->property_id === $property->id, 404);
    }
}
