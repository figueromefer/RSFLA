<x-layouts.app title="Properties | RSFLA">
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-[#8DC442]/30 bg-[#8DC442]/10 p-4 text-sm font-semibold text-[#4f7423]">{{ session('status') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Properties</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Choose a property to update its client report.</p>
        </div>
        <a href="{{ route('properties.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Create property</a>
    </div>

    <section class="grid gap-4 xl:grid-cols-2">
        @forelse ($properties as $property)
            <article class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('properties.edit', $property) }}" class="font-rsfla-heading text-2xl font-bold text-[#424143] transition hover:text-[#4f7423]">{{ $property->name }}</a>
                            <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $property->is_active ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">{{ $property->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <p class="mt-1 text-sm text-[#424143]/65">{{ $property->street_address ?: 'No address' }} · {{ $property->city }}, {{ $property->state }}</p>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        <a href="{{ route('properties.report', $property) }}" class="inline-flex h-9 items-center rounded-md bg-[#8DC442] px-3 text-sm font-bold uppercase tracking-wide text-[#243018] shadow-sm transition hover:bg-[#7ab336]">View Report</a>
                        <a href="{{ route('properties.edit', $property) }}" class="inline-flex h-9 items-center rounded-md bg-[#424143] px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#2f2e30]">Edit Property</a>
                        <a href="{{ route('pipeline.index', ['property_id' => $property->id]) }}" class="inline-flex h-9 items-center rounded-md border border-[#424143]/15 bg-white px-3 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Leasing Activity</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-[#424143]/10 bg-white p-6 text-sm text-[#424143]/65 shadow-sm">No properties have been created yet.</div>
        @endforelse
    </section>
</x-layouts.app>
