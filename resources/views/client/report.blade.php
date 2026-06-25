@php
    use App\Models\MarketingActivity;
    use App\Models\Prospect;

    $visibleProspects = $property->prospects->sortBy([
        ['sort_order', 'asc'],
        ['tenant', 'asc'],
    ]);
    $documentLinks = $property->visibleLinks;
    $maxStatusCount = max(1, $statusCounts->max());
    $pipelineOverview = [
        Prospect::STATUS_LEASE_SIGNED => 'Leases',
        Prospect::STATUS_PROPOSAL_SENT => 'Proposal Sent',
        Prospect::STATUS_PROPOSAL_ACCEPTED => 'Proposal Accepted',
        Prospect::STATUS_TOUR_SCHEDULED => 'Tour Scheduled',
        Prospect::STATUS_TOUR_COMPLETED => 'Tour Completed',
        Prospect::STATUS_PROSPECT => 'Active Prospects',
        Prospect::STATUS_LEAD => 'New Leads',
        Prospect::STATUS_INACTIVE => 'Inactive',
    ];
    $pipelineGroups = [
        'Lease' => [Prospect::STATUS_LEASE_SIGNED],
        'Proposals' => [Prospect::STATUS_PROPOSAL_SENT, Prospect::STATUS_PROPOSAL_ACCEPTED],
        'Tours' => [Prospect::STATUS_TOUR_SCHEDULED, Prospect::STATUS_TOUR_COMPLETED],
        'Active Prospects' => [Prospect::STATUS_PROSPECT],
        'New Leads' => [Prospect::STATUS_LEAD],
        'Inactive' => [Prospect::STATUS_INACTIVE],
    ];
    $summaryParts = [
        "{$metrics['activeProspects']} active ".str('prospect')->plural($metrics['activeProspects']),
        "{$metrics['tours']} ".str('tour')->plural($metrics['tours']),
        "{$metrics['proposals']} ".str('proposal')->plural($metrics['proposals']),
        "{$metrics['leases']} signed ".str('lease')->plural($metrics['leases']),
        "{$metrics['marketingActivity']} recent marketing ".str('activity')->plural($metrics['marketingActivity']),
    ];
@endphp

<x-layouts.app title="{{ $property->name }} Report | RSFLA">
    <div class="space-y-6 print:space-y-4">
        <section class="print-card overflow-hidden rounded-lg border border-[#424143]/10 bg-white shadow-sm">
            <div class="h-1 bg-[#8DC442]"></div>
            <div class="grid gap-6 px-6 py-6 lg:grid-cols-[1fr_320px] lg:items-start">
                <div class="min-w-0">
                    <div class="mb-5 flex items-center gap-3">
                        <span class="flex size-10 items-center justify-center rounded-sm bg-[#424143] font-rsfla-heading text-base font-bold text-white">
                            <span class="text-[#8DC442]">R</span>S
                        </span>
                        <div>
                            <p class="font-rsfla-heading text-xl font-bold text-[#424143]">RSFLA</p>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#424143]/50">Commercial property report</p>
                        </div>
                    </div>

                    <a href="{{ $isInternalReportView ?? false ? route('reports.index') : route('client.properties') }}" class="print-hidden text-sm font-medium text-[#424143]/60 transition hover:text-[#424143]">Properties</a>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <h1 class="font-rsfla-heading text-4xl font-bold leading-none text-[#424143] sm:text-5xl">{{ $property->report_title ?: $property->name }}</h1>
                        <span class="inline-flex h-6 items-center rounded-full border border-[#8DC442]/35 bg-[#8DC442]/15 px-2.5 text-xs font-semibold text-[#4f7423]">{{ str($property->status)->title() }}</span>
                    </div>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-[#424143]/65">
                        {{ $property->name }} · {{ $property->street_address ?: 'Address pending' }} · {{ $property->city }}, {{ $property->state }}{{ $property->market ? ' · '.$property->market : '' }}
                    </p>
                    <p class="mt-2 text-xs font-medium text-[#424143]/55">Last updated {{ $lastUpdatedAt->format('M j, Y g:i A') }} · Generated at {{ $generatedAt->format('M j, Y g:i A') }}</p>
                </div>

                <div class="flex flex-col gap-3 lg:items-end">
                    <div class="print-hidden flex flex-wrap gap-2 lg:justify-end">
                        <button type="button" onclick="window.print()" class="inline-flex h-9 items-center rounded-md bg-[#8DC442] px-3 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Print / Export</button>
                        <span class="inline-flex h-9 items-center gap-2 rounded-md border border-[#424143]/10 bg-white px-3 text-sm font-medium text-[#424143]">
                            <span class="size-2 rounded-full bg-[#8DC442]"></span>
                            Live report
                        </span>
                    </div>
                    <div class="rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 lg:w-full">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Occupancy</p>
                        <div class="mt-2 flex items-baseline gap-3">
                            <span class="font-rsfla-heading text-5xl font-bold text-[#424143]">{{ $metrics['occupancy'] }}%</span>
                            <span class="text-xs font-medium text-[#424143]/55">{{ $metrics['leases'] }} leases / {{ $property->unit_count ?? 0 }} units</span>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-[#424143]/10">
                            <div class="h-2 rounded-full bg-[#8DC442]" style="width: {{ $metrics['occupancy'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($property->hero_image)
                <div class="h-40 border-t border-[#424143]/10 bg-[#f7f8f5] print:hidden sm:h-52">
                    <img src="{{ $property->hero_image }}" alt="{{ $property->name }}" class="h-full w-full object-cover">
                </div>
            @endif
        </section>

        <section class="print-card rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#8DC442]">Executive Summary</p>
                    <h2 class="mt-2 font-rsfla-heading text-3xl font-bold text-[#424143]">Current leasing position</h2>
                    <p class="mt-3 max-w-4xl text-base leading-7 text-[#424143]/70">
                        {{ $property->name }} currently has {{ implode(', ', $summaryParts) }}. The report reflects client-visible pipeline and marketing updates available as of {{ $lastUpdatedAt->format('M j, Y') }}.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-md border border-[#424143]/10 bg-[#f7f8f5] p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Active Prospects</p>
                        <p class="mt-2 font-rsfla-heading text-3xl font-bold text-[#424143]">{{ $metrics['activeProspects'] }}</p>
                    </div>
                    <div class="rounded-md border border-[#424143]/10 bg-[#f7f8f5] p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Tours</p>
                        <p class="mt-2 font-rsfla-heading text-3xl font-bold text-[#424143]">{{ $metrics['tours'] }}</p>
                    </div>
                    <div class="rounded-md border border-[#424143]/10 bg-[#f7f8f5] p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Proposals</p>
                        <p class="mt-2 font-rsfla-heading text-3xl font-bold text-[#424143]">{{ $metrics['proposals'] }}</p>
                    </div>
                    <div class="rounded-md border border-[#424143]/10 bg-[#f7f8f5] p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Marketing</p>
                        <p class="mt-2 font-rsfla-heading text-3xl font-bold text-[#424143]">{{ $metrics['marketingActivity'] }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1fr_380px]">
            <div class="space-y-6">
                <div class="print-card rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#8DC442]">Pipeline</p>
                            <h2 class="mt-1 font-rsfla-heading text-2xl font-bold text-[#424143]">Stage Overview</h2>
                            <p class="mt-1 text-sm text-[#424143]/65">Visible movement from inquiry to signed lease.</p>
                        </div>
                        <span class="rounded-md border border-[#424143]/10 bg-[#f7f8f5] px-2.5 py-1 text-xs font-semibold text-[#424143]/70">{{ $visibleProspects->count() }} visible records</span>
                    </div>

                    <div class="mt-6 grid gap-4">
                        @foreach ($pipelineOverview as $status => $label)
                            @php
                                $total = $statusCounts->get($status, 0);
                                $width = $total > 0 ? max(6, (int) round(($total / $maxStatusCount) * 100)) : 0;
                            @endphp
                            <div class="grid gap-2 sm:grid-cols-[170px_1fr_42px] sm:items-center">
                                <div class="text-sm font-medium text-[#424143]">{{ $label }}</div>
                                <div class="h-2 rounded-full bg-[#424143]/10">
                                    <div class="h-2 rounded-full bg-[#8DC442]" style="width: {{ $width }}%"></div>
                                </div>
                                <div class="text-right text-sm font-semibold text-[#424143]">{{ $total }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="print-card rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#8DC442]">Pipeline Detail</p>
                        <h2 class="mt-1 font-rsfla-heading text-2xl font-bold text-[#424143]">Client-visible prospect detail</h2>
                        <p class="mt-1 text-sm text-[#424143]/65">Records are grouped by leasing stage and include only client-visible prospects.</p>
                    </div>

                    <div class="mt-6 space-y-5">
                        @foreach ($pipelineGroups as $groupLabel => $statuses)
                            @php
                                $groupProspects = $visibleProspects->whereIn('status', $statuses);
                            @endphp
                            <section class="report-section rounded-md border border-[#424143]/10">
                                <div class="flex items-center justify-between gap-3 border-b border-[#424143]/10 bg-[#f7f8f5] px-4 py-3">
                                    <h3 class="font-rsfla-heading text-lg font-bold text-[#424143]">{{ $groupLabel }}</h3>
                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-[#424143]/60">{{ $groupProspects->count() }}</span>
                                </div>
                                <div class="divide-y divide-[#424143]/10">
                                    @forelse ($groupProspects as $prospect)
                                        <article class="grid gap-3 px-4 py-4 lg:grid-cols-[1.1fr_0.9fr_0.8fr]">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-[#424143]">{{ $prospect->tenant ?: $prospect->full_name ?: 'Unnamed prospect' }}</p>
                                                <p class="mt-1 text-xs font-medium text-[#424143]/55">{{ Prospect::statusFormLabel($prospect->status) }}</p>
                                            </div>
                                            <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
                                                <div>
                                                    <dt class="font-semibold uppercase tracking-wide text-[#424143]/45">Suite</dt>
                                                    <dd class="mt-0.5 text-[#424143]">{{ $prospect->suite ?: 'TBD' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="font-semibold uppercase tracking-wide text-[#424143]/45">Use</dt>
                                                    <dd class="mt-0.5 text-[#424143]">{{ $prospect->use_type ?: 'TBD' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="font-semibold uppercase tracking-wide text-[#424143]/45">Timing</dt>
                                                    <dd class="mt-0.5 text-[#424143]">{{ $prospect->timing ?: 'TBD' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="font-semibold uppercase tracking-wide text-[#424143]/45">RSF</dt>
                                                    <dd class="mt-0.5 text-[#424143]">{{ $prospect->rsf ? number_format($prospect->rsf) : 'TBD' }}</dd>
                                                </div>
                                            </dl>
                                            <div class="text-xs">
                                                <p class="font-semibold uppercase tracking-wide text-[#424143]/45">Broker</p>
                                                <p class="mt-1 text-sm text-[#424143]">{{ $prospect->broker ?: 'Direct / TBD' }}</p>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="px-4 py-5 text-sm text-[#424143]/60">No client-visible prospects in this stage.</div>
                                    @endforelse
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>

                <div class="print-card rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#8DC442]">Recent Timeline</p>
                        <h2 class="mt-1 font-rsfla-heading text-2xl font-bold text-[#424143]">Latest updates</h2>
                    </div>

                    <div class="mt-6 divide-y divide-[#424143]/10">
                        @forelse ($property->activities as $activity)
                            <article class="flex gap-4 py-4 first:pt-0 last:pb-0">
                                <div class="mt-1 size-2 rounded-full bg-[#8DC442]"></div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <h3 class="truncate text-sm font-semibold text-[#424143]">{{ $activity->subject }}</h3>
                                        <time class="text-xs font-medium text-[#424143]/55">{{ $activity->occurred_at->format('M j, g:i A') }}</time>
                                    </div>
                                    <p class="mt-1 text-sm text-[#424143]/65">{{ $activity->prospect->full_name }} · {{ optional($activity->teamMember)->name ?? 'RSFLA team' }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="rounded-md border border-dashed border-[#424143]/20 bg-[#f7f8f5] p-4 text-sm text-[#424143]/65">No timeline activity has been published yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="print-card rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#8DC442]">Monthly Activity</p>
                            <h2 class="mt-1 font-rsfla-heading text-xl font-bold text-[#424143]">Marketing Activity</h2>
                        </div>
                        <span class="rounded-full bg-[#8DC442]/15 px-2 py-0.5 text-xs font-semibold text-[#4f7423]">{{ $marketingActivities->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse ($marketingActivities as $activity)
                            <article class="report-section rounded-md border border-[#424143]/10 p-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-14 shrink-0 rounded-md bg-[#f7f8f5] px-2 py-2 text-center">
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-[#424143]/50">{{ $activity->activity_date->format('M') }}</p>
                                        <p class="font-rsfla-heading text-xl font-bold text-[#424143]">{{ $activity->activity_date->format('j') }}</p>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-[#424143]">{{ $activity->title }}</p>
                                        <p class="mt-1 text-xs text-[#424143]/55">{{ MarketingActivity::typeLabel($activity->type) }}</p>
                                        @if ($activity->description)
                                            <p class="mt-2 text-sm leading-6 text-[#424143]/65">{{ $activity->description }}</p>
                                        @endif
                                        @if ($activity->metric_label || $activity->metric_value)
                                            <div class="mt-3 inline-flex rounded-md border border-[#424143]/10 bg-[#f7f8f5] px-2.5 py-1 text-xs font-semibold text-[#424143]/70">
                                                {{ $activity->metric_value ?: 'Updated' }} {{ $activity->metric_label }}
                                            </div>
                                        @endif
                                        @if ($activity->url)
                                            <a href="{{ $activity->url }}" target="_blank" rel="noopener" class="print-hidden mt-2 inline-flex text-sm font-semibold text-[#4f7423]">Open</a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="rounded-md border border-dashed border-[#424143]/20 bg-[#f7f8f5] p-4 text-sm leading-6 text-[#424143]/65">No client-visible marketing activity has been added yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="print-card rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Documents</h2>
                        <span class="rounded-full bg-[#8DC442]/15 px-2 py-0.5 text-xs font-semibold text-[#4f7423]">{{ $documentLinks->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse ($documentLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener" class="report-section block rounded-md border border-[#424143]/10 p-3 transition hover:border-[#8DC442] hover:bg-[#f7f8f5]">
                                <p class="text-sm font-medium text-[#424143]">{{ $link->label }}</p>
                                <p class="mt-1 text-xs text-[#424143]/55">{{ $link->description ?: str($link->type)->replace('_', ' ')->title() }}</p>
                            </a>
                        @empty
                            <p class="rounded-md border border-dashed border-[#424143]/20 bg-[#f7f8f5] p-4 text-sm text-[#424143]/65">Documents and property links will appear here when they are shared with the client.</p>
                        @endforelse
                    </div>
                </div>

                <div class="print-card rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
                    <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Team</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($teamMembers as $member)
                            <div class="report-section flex items-start gap-3 rounded-md border border-[#424143]/10 p-3">
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-md bg-[#8DC442]/15 text-xs font-semibold text-[#4f7423]">{{ str($member->name)->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->implode('') }}</div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-[#424143]">{{ $member->name }}</p>
                                    <p class="truncate text-xs text-[#424143]/55">{{ $member->dre ? 'DRE '.$member->dre : 'RSFLA team' }}</p>
                                    @if ($member->email)
                                        <p class="truncate text-xs text-[#424143]/55">{{ $member->email }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="rounded-md border border-dashed border-[#424143]/20 bg-[#f7f8f5] p-4 text-sm text-[#424143]/65">Team assignments have not been published for this property yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="print-card rounded-lg border border-dashed border-[#424143]/25 bg-[#f7f8f5] p-5">
                    <p class="font-rsfla-heading text-lg font-bold text-[#424143]">Market Insights</p>
                    <p class="mt-2 text-sm leading-6 text-[#424143]/65">Reserved for future market data, competitive context, pricing movement, and submarket signals.</p>
                </div>
            </aside>
        </section>
    </div>
</x-layouts.app>
