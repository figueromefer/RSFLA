<x-layouts.app title="Add Market Data | RSFLA">
    <div class="mx-auto max-w-3xl space-y-5">
        <div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143]">Add Market Data</h1>
            <p class="mt-2 text-sm text-[#424143]/65">{{ $property->name }}</p>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ route('properties.market-data.store', $property) }}" class="space-y-6 rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            @csrf
            @include('property-market-data._form')
            <div class="flex justify-end gap-2 border-t border-[#424143]/10 pt-5">
                <a href="{{ route('properties.edit', $property).'#market-data' }}" class="inline-flex h-10 items-center px-4 text-sm font-semibold text-[#424143]/65">Cancel</a>
                <button class="inline-flex h-10 items-center rounded-md bg-[#8DC442] px-5 text-sm font-semibold text-[#243018]" type="submit">Add Market Data</button>
            </div>
        </form>
    </div>
</x-layouts.app>
