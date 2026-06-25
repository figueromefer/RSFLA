<x-layouts.app title="Properties | RSFLA">
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-[#8DC442]/30 bg-[#8DC442]/10 p-4 text-sm font-semibold text-[#4f7423]">{{ session('status') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Properties</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Property workspaces for pipeline, reporting, client visibility, and documents.</p>
        </div>
        <a href="{{ route('properties.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Create property</a>
    </div>

    <section class="grid gap-4 xl:grid-cols-2">
        @forelse ($properties as $property)
            <article class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('properties.show', $property) }}" class="font-rsfla-heading text-2xl font-bold text-[#424143] transition hover:text-[#4f7423]">{{ $property->name }}</a>
                            <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $property->is_active ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">{{ $property->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <p class="mt-1 text-sm text-[#424143]/65">{{ $property->street_address ?: 'No address' }} · {{ $property->city }}, {{ $property->state }}</p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <a href="{{ route('properties.report', $property) }}" class="inline-flex h-9 items-center rounded-md border border-[#424143]/15 bg-white px-3 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Report</a>
                        <a href="{{ route('properties.edit', $property) }}" class="inline-flex h-9 items-center rounded-md bg-[#424143] px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#2f2e30]">Edit</a>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-md bg-[#f7f8f5] p-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Total Prospects</div>
                        <div class="mt-1 text-2xl font-semibold text-[#424143]">{{ $property->prospects_count }}</div>
                    </div>
                    <div class="rounded-md bg-[#f7f8f5] p-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Visible Prospects</div>
                        <div class="mt-1 text-2xl font-semibold text-[#424143]">{{ $property->visible_prospects_count }}</div>
                    </div>
                    <div class="rounded-md bg-[#f7f8f5] p-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Last Activity</div>
                        <div class="mt-2 text-sm font-semibold text-[#424143]">
                            {{ $property->activities_max_occurred_at ? \Illuminate\Support\Carbon::parse($property->activities_max_occurred_at)->format('M j, Y') : 'No activity' }}
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-[#424143]/10 bg-white p-6 text-sm text-[#424143]/65 shadow-sm">No properties have been created yet.</div>
        @endforelse
    </section>
</x-layouts.app>
