<x-layouts.app title="Reports | RSFLA">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Reports</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Client-ready property reporting with live pipeline, marketing, links, and team context.</p>
        </div>
    </div>

    <section class="rounded-lg border border-[#424143]/10 bg-white shadow-sm">
        <div class="grid grid-cols-12 border-b border-[#424143]/10 bg-[#f7f8f5] px-5 py-3 text-xs font-semibold uppercase tracking-wide text-[#424143]/55">
            <div class="col-span-5">Property</div>
            <div class="col-span-2">Status</div>
            <div class="col-span-2 text-right">Visible Prospects</div>
            <div class="col-span-2 text-right">Marketing</div>
            <div class="col-span-1"></div>
        </div>

        <div class="divide-y divide-[#424143]/10">
            @forelse ($properties as $property)
                @php
                    $activityDate = $property->activities_max_occurred_at ? \Illuminate\Support\Carbon::parse($property->activities_max_occurred_at) : null;
                    $marketingDate = $property->visible_marketing_activities_max_activity_date ? \Illuminate\Support\Carbon::parse($property->visible_marketing_activities_max_activity_date) : null;
                    $lastUpdated = collect([$property->updated_at, $activityDate, $marketingDate])->filter()->sort()->last();
                @endphp
                <article class="grid grid-cols-12 items-center gap-3 px-5 py-4">
                    <div class="col-span-12 min-w-0 md:col-span-5">
                        <a href="{{ route('reports.show', $property) }}" class="font-rsfla-heading text-xl font-bold text-[#424143] transition hover:text-[#4f7423]">{{ $property->name }}</a>
                        <p class="mt-1 truncate text-sm text-[#424143]/60">
                            {{ $property->street_address ?: 'No address' }} · {{ $property->city }}, {{ $property->state }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-[#424143]/50">Last updated {{ $lastUpdated ? $lastUpdated->format('M j, Y g:i A') : 'not yet' }}</p>
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $property->is_active ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">
                            {{ $property->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="col-span-3 text-right text-sm font-semibold text-[#424143] md:col-span-2">{{ $property->visible_prospects_count }}</div>
                    <div class="col-span-3 text-right text-sm font-semibold text-[#424143] md:col-span-2">{{ $property->visible_marketing_activities_count }}</div>
                    <div class="col-span-2 flex justify-end md:col-span-1">
                        <a href="{{ route('reports.show', $property) }}" class="inline-flex h-9 items-center rounded-md bg-[#8DC442] px-3 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">View Report</a>
                    </div>
                </article>
            @empty
                <div class="px-5 py-6 text-sm text-[#424143]/65">No properties have been created yet.</div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
