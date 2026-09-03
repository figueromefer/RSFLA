<x-layouts.app title="Edit Property | RSFLA">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Edit Property</h1>
            <p class="mt-2 text-sm text-[#424143]/65">{{ $property->name }} · {{ $property->city }}, {{ $property->state }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('properties.report', $property) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-5 text-sm font-bold uppercase tracking-wide text-[#243018] shadow-sm transition hover:bg-[#7ab336]">View Report</a>
            <a href="{{ route('properties.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Back</a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-lg border border-[#8DC442]/30 bg-[#8DC442]/10 p-4 text-sm font-semibold text-[#4f7423]">{{ session('status') }}</div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ route('properties.update', $property) }}" class="max-w-5xl space-y-6 rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @include('properties._form')
        <div class="flex justify-end border-t border-[#424143]/10 pt-5">
            <button class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-5 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]" type="submit">Save changes</button>
        </div>
    </form>

    @foreach ($property->marketDataEntries as $entry)
        <form id="delete-market-data-{{ $entry->id }}" method="POST" action="{{ route('properties.market-data.destroy', [$property, $entry]) }}" onsubmit="return confirm('Delete this Market Data entry?')" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    @foreach ($property->rentRollEntries as $entry)
        <form id="delete-rent-roll-{{ $entry->id }}" method="POST" action="{{ route('properties.rent-roll.destroy', [$property, $entry]) }}" onsubmit="return confirm('Delete this rent roll entry?')" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</x-layouts.app>
