@php
    $statusClasses = [
        'prospect' => 'bg-[#424143]/10 text-[#424143]',
        'lead' => 'bg-[#8DC442]/15 text-[#4f7423]',
        'tour_scheduled' => 'bg-blue-50 text-blue-700',
        'tour_completed' => 'bg-cyan-50 text-cyan-700',
        'proposal_sent' => 'bg-amber-50 text-amber-700',
        'proposal_accepted' => 'bg-lime-50 text-lime-700',
        'lease_signed' => 'bg-emerald-50 text-emerald-700',
        'inactive' => 'bg-zinc-100 text-zinc-500',
    ];
@endphp

<x-layouts.app title="{{ $property->name }} | RSFLA">
    <div class="space-y-6">
        <section class="rounded-lg border border-[#424143]/10 bg-white shadow-sm">
            <div class="h-1 rounded-t-lg bg-[#8DC442]"></div>
            <div class="flex flex-col gap-5 px-6 py-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <a href="{{ route('properties.index') }}" class="text-sm font-medium text-[#424143]/60 transition hover:text-[#424143]">Properties</a>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <h1 class="font-rsfla-heading text-4xl font-bold text-[#424143] sm:text-5xl">{{ $property->name }}</h1>
                        <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $property->is_active ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">{{ $property->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <p class="mt-2 text-sm text-[#424143]/65">{{ $property->street_address ?: 'No address' }} · {{ $property->city }}, {{ $property->state }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('properties.edit', $property) }}" class="inline-flex h-10 items-center justify-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Edit</a>
                    <a href="{{ route('properties.report', $property) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">View Client Report</a>
                </div>
            </div>
            <nav class="flex gap-1 overflow-x-auto border-t border-[#424143]/10 bg-[#f7f8f5] px-4 py-2 text-sm font-semibold text-[#424143]/70">
                @foreach (['overview' => 'Overview', 'pipeline' => 'Pipeline', 'marketing' => 'Marketing', 'documents' => 'Documents', 'team' => 'Team', 'activity' => 'Activity', 'report' => 'Report'] as $anchor => $label)
                    <a href="#{{ $anchor }}" class="rounded px-3 py-2 transition hover:bg-white hover:text-[#424143]">{{ $label }}</a>
                @endforeach
            </nav>
        </section>

        <section id="overview" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Overview</h2>
                    <p class="mt-1 text-sm text-[#424143]/65">Current operating snapshot for this property.</p>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    'Total Prospects' => $metrics['totalProspects'],
                    'Visible Prospects' => $metrics['visibleProspects'],
                    'Active Prospects' => $metrics['activeProspects'],
                    'Tours' => $metrics['tours'],
                    'Proposals' => $metrics['proposals'],
                    'Leases' => $metrics['leases'],
                    'Inactive' => $metrics['inactive'],
                    'Last Activity' => $metrics['lastActivity']?->format('M j, Y') ?? 'No activity',
                ] as $label => $value)
                    <div class="rounded-md bg-[#f7f8f5] p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">{{ $label }}</div>
                        <div class="mt-2 text-2xl font-semibold text-[#424143]">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="pipeline" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Pipeline</h2>
                    <p class="mt-1 text-sm text-[#424143]/65">Prospects and tenant opportunities tied to this property.</p>
                </div>
                <a href="{{ route('pipeline.create', ['property_id' => $property->id]) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Add Prospect</a>
            </div>

            <div class="overflow-hidden rounded-lg border border-[#424143]/10">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#424143]/10 text-sm">
                        <thead class="bg-[#f7f8f5] text-left text-xs font-semibold uppercase tracking-wide text-[#424143]/60">
                            <tr>
                                <th class="px-4 py-3">Tenant</th>
                                <th class="px-4 py-3">Suite</th>
                                <th class="px-4 py-3">Use</th>
                                <th class="px-4 py-3">RSF</th>
                                <th class="px-4 py-3">Broker</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#424143]/10">
                            @forelse ($property->prospects as $prospect)
                                <tr class="transition hover:bg-[#f7f8f5]">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-[#424143]">{{ $prospect->tenant ?: $prospect->full_name }}</div>
                                        <div class="text-xs text-[#424143]/55">{{ $prospect->visible_to_client ? 'Visible to client' : 'Internal only' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $prospect->suite ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $prospect->use_type ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $prospect->rsf ? number_format($prospect->rsf) : '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $prospect->broker ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $statusClasses[$prospect->status] ?? 'bg-zinc-100 text-zinc-600' }}">{{ \App\Models\Prospect::statusLabel($prospect->status) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <a href="{{ route('pipeline.edit', $prospect) }}" class="font-semibold text-[#4f7423] transition hover:text-[#8DC442]">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-[#424143]/60">No prospects yet for this property.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="marketing" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Marketing</h2>
                    <p class="mt-1 text-sm text-[#424143]/65">Recent marketing activity and client-visible campaign updates.</p>
                </div>
                <a href="{{ route('marketing.index', ['property_id' => $property->id]) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Manage Marketing</a>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-md bg-[#f7f8f5] p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Total Activities</div>
                    <div class="mt-2 text-2xl font-semibold text-[#424143]">{{ $metrics['totalMarketingActivities'] }}</div>
                </div>
                <div class="rounded-md bg-[#f7f8f5] p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Visible Activities</div>
                    <div class="mt-2 text-2xl font-semibold text-[#424143]">{{ $metrics['visibleMarketingActivities'] }}</div>
                </div>
                <div class="rounded-md bg-[#f7f8f5] p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-[#424143]/55">Latest Activity</div>
                    <div class="mt-2 text-sm font-semibold text-[#424143]">{{ $metrics['latestMarketingActivityDate']?->format('M j, Y') ?? 'No activity' }}</div>
                </div>
            </div>

            <div class="mt-5 divide-y divide-[#424143]/10">
                @forelse ($property->marketingActivities as $activity)
                    <article class="flex flex-col gap-2 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-semibold text-[#424143]">{{ $activity->title }}</h3>
                                <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $activity->visible_to_client ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">{{ $activity->visible_to_client ? 'Client visible' : 'Internal' }}</span>
                            </div>
                            <p class="mt-1 text-sm text-[#424143]/65">{{ $activity->description }}</p>
                        </div>
                        <div class="text-sm font-medium text-[#424143]/55">{{ $activity->activity_date->format('M j, Y') }}</div>
                    </article>
                @empty
                    <p class="mt-5 text-sm text-[#424143]/60">No marketing activities yet.</p>
                @endforelse
            </div>
        </section>

        <section id="documents" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Documents</h2>
                    <p class="mt-1 text-sm text-[#424143]/65">Property links and documents available for internal tracking or client reports.</p>
                </div>
                <a href="{{ route('documents.index', ['property_id' => $property->id]) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Manage Links</a>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                @forelse ($property->links as $link)
                    <a href="{{ $link->url }}" target="_blank" rel="noopener" class="rounded-md border border-[#424143]/10 p-4 transition hover:border-[#8DC442] hover:bg-[#f7f8f5]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold text-[#424143]">{{ $link->label }}</div>
                                <div class="mt-1 text-xs text-[#424143]/55">{{ str($link->type)->replace('_', ' ')->title() }}</div>
                            </div>
                            <span class="text-xs font-semibold {{ $link->is_visible_to_client ? 'text-[#4f7423]' : 'text-[#424143]/45' }}">{{ $link->is_visible_to_client ? 'Client visible' : 'Internal' }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-[#424143]/60">No property links yet.</p>
                @endforelse
            </div>
        </section>

        <section id="team" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Team</h2>
                    <p class="mt-1 text-sm text-[#424143]/65">
                        {{ $property->teamMembers->isNotEmpty() ? 'Assigned RSFLA team members for this property.' : 'No assigned team yet. Showing active team fallback.' }}
                    </p>
                </div>
                <a href="{{ route('properties.edit', $property) }}" class="inline-flex h-10 items-center justify-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Assign Team</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($displayTeamMembers as $member)
                    <article class="rounded-md border border-[#424143]/10 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-md bg-[#8DC442]/15 text-xs font-semibold text-[#4f7423]">
                                @if ($member->photo)
                                    <img src="{{ $member->photo }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ str($member->name)->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->implode('') }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-semibold text-[#424143]">{{ $member->name }}</h3>
                                <p class="mt-1 truncate text-xs text-[#424143]/55">{{ $member->dre ?: 'No DRE' }}</p>
                                <p class="mt-2 text-sm text-[#424143]/70">{{ $member->phone ?: $member->email ?: 'No contact' }}</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-[#424143]/60">No active team members available.</p>
                @endforelse
            </div>
        </section>

        <section id="activity" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Activity</h2>
            <p class="mt-1 text-sm text-[#424143]/65">Recent activity across the property pipeline.</p>
            <div class="mt-5 divide-y divide-[#424143]/10">
                @forelse ($property->activities as $activity)
                    <article class="flex gap-4 py-4 first:pt-0 last:pb-0">
                        <div class="mt-1 size-2 rounded-full bg-[#8DC442]"></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="truncate text-sm font-semibold text-[#424143]">{{ $activity->subject }}</h3>
                                <time class="text-xs font-medium text-[#424143]/55">{{ $activity->occurred_at->format('M j, Y g:i A') }}</time>
                            </div>
                            <p class="mt-1 text-sm text-[#424143]/65">
                                {{ optional($activity->prospect)->tenant ?: optional($activity->prospect)->full_name ?: 'Property activity' }}
                                · {{ str($activity->type)->replace('_', ' ')->title() }}
                                @if ($activity->status_from || $activity->status_to)
                                    · {{ $activity->status_from ? \App\Models\Prospect::statusLabel($activity->status_from) : 'New' }} to {{ $activity->status_to ? \App\Models\Prospect::statusLabel($activity->status_to) : 'Updated' }}
                                @endif
                                · {{ optional($activity->user)->name ?? optional($activity->teamMember)->name ?? 'RSFLA team' }}
                            </p>
                            @if ($activity->body)
                                <p class="mt-2 text-sm leading-6 text-[#424143]/70">{{ $activity->body }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-[#424143]/60">No activity yet.</p>
                @endforelse
            </div>
        </section>

        <section id="report" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Report</h2>
                    <p class="mt-1 text-sm text-[#424143]/65">Open the client-facing report internally to verify visibility and presentation.</p>
                </div>
                <a href="{{ route('properties.report', $property) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Open Client Report</a>
            </div>
        </section>
    </div>
</x-layouts.app>
