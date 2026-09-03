@php
    use App\Models\MarketingActivity;
    use App\Models\Prospect;

    $visibleProspects = $reportProspects->sortBy([
        ['sort_order', 'asc'],
        ['tenant', 'asc'],
    ]);
    $documentLinks = $property->visibleLinks;
    $pipelineGroups = [
        ['label' => 'Current Active Prospects', 'description' => 'Active prospects currently being tracked', 'anchor' => 'pipeline-prospects', 'statuses' => [Prospect::STATUS_PROSPECT]],
        ['label' => 'Leases', 'description' => 'Leases in negotiation', 'anchor' => 'pipeline-leases', 'statuses' => [Prospect::STATUS_LEASE_SIGNED]],
        ['label' => 'Proposals', 'description' => 'Proposals in negotiations', 'anchor' => 'pipeline-proposals', 'statuses' => [Prospect::STATUS_PROPOSAL_SENT, Prospect::STATUS_PROPOSAL_ACCEPTED]],
        ['label' => 'Tours', 'description' => 'Scheduled or completed tours', 'anchor' => 'pipeline-tours', 'statuses' => [Prospect::STATUS_TOUR_SCHEDULED, Prospect::STATUS_TOUR_COMPLETED]],
        ['label' => 'Inquiries', 'description' => 'New opportunities', 'anchor' => 'pipeline-inquiry', 'statuses' => [Prospect::STATUS_LEAD]],
    ];
    $pipelineTotals = collect($pipelineGroups)->mapWithKeys(fn ($group) => [
        $group['anchor'] => $visibleProspects->whereIn('status', $group['statuses'])->count(),
    ]);
@endphp

<x-layouts.app title="{{ $property->name }} Report | RSFLA">
    <div class="space-y-5 print:space-y-4">
        <section class="report-hero print-card overflow-hidden rounded-xl border border-[#424143]/10 bg-white">
            <div class="h-1 bg-[#8DC442]"></div>
            <div class="relative grid gap-6 px-5 py-5 sm:px-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(280px,1fr)]">
                <div class="min-w-0">
                    <div class="mb-4 hidden print:block">
                        <img src="{{ asset('images/rsfla-logo.png') }}" alt="RSFLA" class="h-11 w-auto object-contain object-left">
                    </div>
                    <h1 class="font-rsfla-heading text-4xl font-bold leading-none text-[#424143] sm:text-5xl">{{ $property->name }}</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-[#424143]/65">{{ $property->street_address ?: 'Address pending' }}{{ $property->city ? ' · '.$property->city : '' }}{{ $property->state ? ', '.$property->state : '' }}</p>
                    <p class="mt-2 text-xs font-medium text-[#424143]/55">Last updated {{ $lastUpdatedAt->format('M j, Y g:i A') }}</p>
                    @if ($property->property_photo_url)
                        <div class="mt-5 aspect-[16/7] min-h-36 max-h-64 overflow-hidden rounded-lg border border-[#424143]/10 bg-[#f7f8f5] print:hidden">
                            <img src="{{ $property->property_photo_url }}" alt="{{ $property->name }}" class="h-full w-full object-cover">
                        </div>
                    @endif
                </div>
                <div class="min-w-0 border-t border-[#424143]/10 pt-5 lg:border-l lg:border-t-0 lg:pl-6 lg:pr-32 lg:pt-0">
                    <h2 class="font-rsfla-heading text-sm font-bold uppercase tracking-wide text-[#424143]">Property Information</h2>
                    @if (filled($property->property_information))
                        <div class="mt-3 whitespace-pre-line text-sm leading-6 text-[#424143]/70">{{ $property->property_information }}</div>
                    @else
                        <p class="mt-3 text-sm italic text-[#424143]/45">No property information available.</p>
                    @endif
                </div>
                <div class="print-hidden lg:absolute lg:right-6 lg:top-5">
                    <button type="button" onclick="window.print()" class="inline-flex h-9 items-center rounded-md bg-[#8DC442] px-3 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Print / Export</button>
                </div>
            </div>
        </section>
        <section class="report-grid">
            <div class="report-upper">
                <div id="leasing-activity" class="report-leasing print-card scroll-mt-6 rounded-xl border border-[#424143]/10 bg-white px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="font-rsfla-heading text-2xl font-bold uppercase tracking-wide text-[#424143]">Leasing Activity</h2>
                        <div class="print-hidden inline-flex self-start rounded-md border border-[#424143]/10 bg-[#f7f8f5] p-1" aria-label="Leasing activity date range">
                            <a href="{{ request()->fullUrlWithQuery(['range' => '15']) }}#leasing-activity" class="rounded px-3 py-1.5 text-sm font-semibold transition {{ $range === '15' ? 'bg-white text-[#424143] shadow-sm ring-1 ring-[#424143]/5' : 'text-[#424143]/55 hover:text-[#424143]' }}">Last 15 Days</a>
                            <a href="{{ request()->fullUrlWithQuery(['range' => 'all']) }}#leasing-activity" class="rounded px-3 py-1.5 text-sm font-semibold transition {{ $range === 'all' ? 'bg-white text-[#424143] shadow-sm ring-1 ring-[#424143]/5' : 'text-[#424143]/55 hover:text-[#424143]' }}">All Time</a>
                        </div>
                    </div>

                    <div class="mt-5 divide-y divide-[#424143]/10 border-y border-[#424143]/10">
                        @foreach ($pipelineGroups as $group)
                            @php
                                $groupProspects = $visibleProspects->whereIn('status', $group['statuses']);
                            @endphp
                            <details id="{{ $group['anchor'] }}" class="report-section group scroll-mt-6">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-5 px-1 py-4 transition hover:bg-[#f7f8f5] sm:px-3">
                                    <span class="min-w-0">
                                        <span class="block font-rsfla-heading text-xl font-bold text-[#424143]">{{ $group['label'] }}</span>
                                        <span class="stage-description mt-0.5 block text-sm text-[#424143]/55">{{ $group['description'] }}</span>
                                    </span>
                                    <span class="flex shrink-0 items-center gap-4">
                                        <span class="font-rsfla-heading text-3xl font-bold leading-none text-[#424143]">{{ $groupProspects->count() }}</span>
                                        <span class="print-hidden text-2xl leading-none text-[#4f7423] transition group-open:rotate-90" aria-hidden="true">›</span>
                                    </span>
                                </summary>
                                <div class="divide-y divide-[#424143]/10">
                                    @forelse ($groupProspects as $prospect)
                                        <article class="report-record px-2 py-5 sm:px-3">
                                            <div class="min-w-0">
                                                <p class="text-lg font-semibold text-[#424143]">{{ $prospect->tenant ?: $prospect->full_name ?: 'Unnamed prospect' }}</p>
                                                <p class="mt-1 text-sm text-[#424143]/55">{{ $prospect->opportunity_date?->format('m-d-Y') ?: 'Date TBD' }} <span aria-hidden="true">·</span> Suite {{ $prospect->suite ?: 'TBD' }} <span aria-hidden="true">·</span> {{ $prospect->rsf ? number_format($prospect->rsf).' RSF' : 'RSF TBD' }}</p>
                                            </div>
                                            <dl class="mt-4 grid gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                                                <div>
                                                    <dt class="text-xs font-medium uppercase tracking-wide text-[#424143]/45">Use</dt>
                                                    <dd class="mt-0.5 text-[#424143]">{{ $prospect->use_type ?: 'TBD' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium uppercase tracking-wide text-[#424143]/45">Timing</dt>
                                                    <dd class="mt-0.5 text-[#424143]">{{ $prospect->timing ?: 'TBD' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium uppercase tracking-wide text-[#424143]/45">Broker</dt>
                                                    <dd class="mt-0.5 text-[#424143]">{{ $prospect->broker ?: 'Direct / TBD' }}</dd>
                                                </div>
                                            </dl>
                                            @if ($prospect->public_notes)
                                                <div class="mt-4 border-l-2 border-[#8DC442]/60 pl-3 text-sm leading-6 text-[#424143]/65">
                                                    <span class="font-medium text-[#424143]/80">Public Notes:</span> {{ $prospect->public_notes }}
                                                </div>
                                            @endif
                                        </article>
                                    @empty
                                        <div class="px-4 py-5 print:hidden">
                                            <p class="text-sm text-[#424143]/55">No activity in this stage.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>


                <div class="report-sidebar">
                <div id="marketing-activity" class="report-activity print-card rounded-xl border border-[#424143]/10 bg-white p-4 scroll-mt-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Marketing Activity</h2>
                        </div>
                        <span class="rounded-full bg-[#8DC442]/15 px-2 py-0.5 text-xs font-semibold text-[#4f7423]">{{ $marketingActivities->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse ($marketingActivities as $activity)
                            <article class="activity-item rounded-md border border-[#424143]/10 p-3">
                                <div class="flex items-start gap-3">
                                    <time class="w-20 shrink-0 rounded-md bg-[#f7f8f5] px-2 py-2 text-center text-xs font-semibold text-[#424143]/65">{{ $activity->activity_date->format('M j, Y') }}</time>
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
                            <p class="rounded-md border border-dashed border-[#424143]/20 bg-[#f7f8f5] p-4 text-sm leading-6 text-[#424143]/65">No client-visible activity has been added yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="report-documents print-card rounded-xl border border-[#424143]/10 bg-white p-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Documents</h2>
                        <span class="rounded-full bg-[#8DC442]/15 px-2 py-0.5 text-xs font-semibold text-[#4f7423]">{{ $documentLinks->count() }}</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse ($documentLinks as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener" class="document-item block rounded-md border border-[#424143]/10 p-3 transition hover:border-[#8DC442] hover:bg-[#f7f8f5]">
                                <p class="text-sm font-medium text-[#424143]">{{ $link->label }}</p>
                                <p class="mt-1 text-xs text-[#424143]/55">{{ $link->description ?: str($link->type)->replace('_', ' ')->title() }}</p>
                            </a>
                        @empty
                            <p class="rounded-md border border-dashed border-[#424143]/20 bg-[#f7f8f5] p-4 text-sm text-[#424143]/65">Documents and property links will appear here when they are shared with the client.</p>
                        @endforelse
                    </div>
                </div>

                </div>
            </div>
            @include('client._market-data')
            @include('client._rent-roll')
        </section>
    </div>

    <script>
        (() => {
            const openedByPrint = new WeakSet();

            window.addEventListener('beforeprint', () => {
                document.querySelectorAll('details.report-section').forEach((section) => {
                    if (! section.open) {
                        openedByPrint.add(section);
                        section.open = true;
                    }
                });
            });

            window.addEventListener('afterprint', () => {
                document.querySelectorAll('details.report-section').forEach((section) => {
                    if (openedByPrint.has(section)) {
                        section.open = false;
                    }
                });
            });
        })();
    </script>
</x-layouts.app>
