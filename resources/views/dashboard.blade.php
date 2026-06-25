<x-layouts.app title="Internal Dashboard | RSFLA">
    <div class="mb-6">
        <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
        <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Internal dashboard</h1>
        <p class="mt-2 text-sm text-[#424143]/65">Operational snapshot across properties and prospect activity.</p>
    </div>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-[#424143]/60">Properties</div>
            <div class="mt-2 text-3xl font-semibold text-[#424143]">{{ $propertyCount }}</div>
            <div class="mt-1 text-xs font-medium text-[#424143]/55">{{ $activePropertyCount }} active</div>
        </div>
        <div class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-[#424143]/60">Visible prospects</div>
            <div class="mt-2 text-3xl font-semibold text-[#424143]">{{ $visibleProspectCount }}</div>
            <div class="mt-1 text-xs font-medium text-[#424143]/55">{{ $activeProspectCount }} active total</div>
        </div>
        <div class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-[#424143]/60">Leases signed</div>
            <div class="mt-2 text-3xl font-semibold text-[#424143]">{{ $leaseCount }}</div>
        </div>
        <div class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
            <div class="text-sm font-medium text-[#424143]/60">Last activity</div>
            <div class="mt-2 text-lg font-semibold text-[#424143]">{{ $lastActivity?->occurred_at?->format('M j, Y') ?? 'No activity' }}</div>
            <div class="mt-1 text-xs font-medium text-[#424143]/55">{{ $teamMemberCount }} team members</div>
        </div>
    </section>

    <section class="mb-6 rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Properties workspace</h2>
                <p class="mt-1 text-sm text-[#424143]/65">Create property workspaces, update report branding, and manage client report availability.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Open Properties</a>
        </div>
    </section>

    <section class="mb-6 rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Pipeline workspace</h2>
                <p class="mt-1 text-sm text-[#424143]/65">Create, filter, update, and control which opportunities appear in client reports.</p>
            </div>
            <a href="{{ route('pipeline.index') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Open Pipeline</a>
        </div>
    </section>

    <section class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Recent activity</h2>
        <div class="mt-4 divide-y divide-[#424143]/10">
            @forelse ($recentActivities as $activity)
                <div class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                    <div>
                        <strong class="text-sm font-semibold text-[#424143]">{{ $activity->subject }}</strong>
                        <div class="mt-1 text-sm text-[#424143]/60">{{ $activity->property->name }} · {{ $activity->prospect->full_name }}</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex h-6 items-center rounded-full bg-[#8DC442]/15 px-2.5 text-xs font-semibold text-[#4f7423]">{{ str_replace('_', ' ', $activity->type) }}</span>
                        <div class="mt-1 text-xs text-[#424143]/55">{{ $activity->occurred_at->format('M j, Y g:i A') }}</div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-[#424143]/65">No activity yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
