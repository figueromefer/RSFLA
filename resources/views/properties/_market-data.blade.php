<section id="market-data" class="border-t border-[#424143]/10 pt-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-rsfla-heading text-xl font-bold uppercase tracking-wide text-[#424143]">Market Data</h2>
            <p class="mt-1 text-sm text-[#424143]/60">Prepared market report images for this property.</p>
        </div>
        <a href="{{ route('properties.market-data.create', $property) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018]">Add Market Data</a>
    </div>

    <div class="mt-5 space-y-3">
        @forelse ($property->marketDataEntries as $entry)
            <article class="flex flex-col gap-4 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 sm:flex-row sm:items-center">
                <a href="{{ $entry->image_url }}" target="_blank" rel="noopener" class="shrink-0">
                    <img src="{{ $entry->image_url }}" alt="{{ $entry->title }}" class="h-28 w-40 rounded-md border border-[#424143]/10 bg-white object-contain">
                </a>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-[#424143]">{{ $entry->title }}</h3>
                    @if ($entry->report_date)
                        <p class="mt-1 text-sm text-[#424143]/60">{{ $entry->report_date->format('m-d-Y') }}</p>
                    @endif
                </div>
                <div class="flex shrink-0 gap-3">
                    <a href="{{ route('properties.market-data.edit', [$property, $entry]) }}" class="font-semibold text-[#4f7423]">Edit</a>
                    <button type="submit" form="delete-market-data-{{ $entry->id }}" class="font-semibold text-red-600">Delete</button>
                </div>
            </article>
        @empty
            <p class="rounded-md border border-dashed border-[#424143]/20 bg-[#f7f8f5] p-4 text-sm text-[#424143]/60">No market data available.</p>
        @endforelse
    </div>
</section>
