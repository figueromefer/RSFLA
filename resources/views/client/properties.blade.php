<x-layouts.app title="My Properties | RSFLA">
    <div class="mb-6">
        <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
        <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">My properties</h1>
        <p class="mt-2 text-sm text-[#424143]/65">Select a property to view its live report.</p>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($properties as $property)
            <a class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm transition hover:border-[#8DC442] hover:shadow-md" href="{{ route('client.properties.show', $property) }}">
                <span class="inline-flex h-6 items-center rounded-full bg-[#8DC442]/15 px-2.5 text-xs font-semibold text-[#4f7423]">{{ $property->status }}</span>
                <h2 class="mt-4 font-rsfla-heading text-2xl font-bold text-[#424143]">{{ $property->name }}</h2>
                <div class="mt-1 text-sm text-[#424143]/60">{{ $property->city }}, {{ $property->state }}</div>
                <div class="mt-6 text-3xl font-semibold text-[#424143]">{{ $property->prospects_count }}</div>
                <div class="text-sm text-[#424143]/60">prospects tracked</div>
            </a>
        @empty
            <div class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm sm:col-span-2 xl:col-span-3">
                <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">No reports available</h2>
                <p class="mt-2 text-sm leading-6 text-[#424143]/65">Your account does not currently have any active property reports assigned. Please contact RSFLA if you believe this should be available.</p>
            </div>
        @endforelse
    </section>
</x-layouts.app>
