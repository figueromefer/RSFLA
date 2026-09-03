<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyMarketDataRequest;
use App\Models\Property;
use App\Models\PropertyMarketData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class PropertyMarketDataController extends Controller
{
    public function create(Property $property): View
    {
        return view('property-market-data.create', [
            'property' => $property,
            'marketDataEntry' => new PropertyMarketData(),
        ]);
    }

    public function store(PropertyMarketDataRequest $request, Property $property): RedirectResponse
    {
        $imagePath = $request->file('image')->store('market-data', 'public');

        try {
            $property->marketDataEntries()->create([
                ...$request->safe()->except(['image']),
                'image_path' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($imagePath);
            throw $exception;
        }

        return redirect()->route('properties.edit', $property)->with('status', 'Market Data added.');
    }

    public function edit(Property $property, PropertyMarketData $marketDataEntry): View
    {
        $this->ensureEntryBelongsToProperty($property, $marketDataEntry);

        return view('property-market-data.edit', compact('property', 'marketDataEntry'));
    }

    public function update(PropertyMarketDataRequest $request, Property $property, PropertyMarketData $marketDataEntry): RedirectResponse
    {
        $this->ensureEntryBelongsToProperty($property, $marketDataEntry);
        $previousImage = $marketDataEntry->image_path;
        $previousImageIsLocal = $marketDataEntry->hasLocalImage();
        $imagePath = $request->file('image')?->store('market-data', 'public');

        try {
            $marketDataEntry->update([
                ...$request->safe()->except(['image']),
                'image_path' => $imagePath ?? $previousImage,
            ]);
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }

        if ($imagePath && $previousImageIsLocal && $previousImage !== $imagePath) {
            Storage::disk('public')->delete($previousImage);
        }

        return redirect()->route('properties.edit', $property)->with('status', 'Market Data updated.');
    }

    public function destroy(Property $property, PropertyMarketData $marketDataEntry): RedirectResponse
    {
        $this->ensureEntryBelongsToProperty($property, $marketDataEntry);

        if ($marketDataEntry->hasLocalImage()) {
            Storage::disk('public')->delete($marketDataEntry->image_path);
        }

        $marketDataEntry->delete();

        return redirect()->route('properties.edit', $property)->with('status', 'Market Data deleted.');
    }

    private function ensureEntryBelongsToProperty(Property $property, PropertyMarketData $marketDataEntry): void
    {
        abort_unless($marketDataEntry->property_id === $property->id, 404);
    }
}
