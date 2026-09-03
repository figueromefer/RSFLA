@php
    $statusClasses = [
        'prospect' => 'bg-[#424143]/10 text-[#424143]', 'lead' => 'bg-[#8DC442]/15 text-[#4f7423]',
        'tour_scheduled' => 'bg-blue-50 text-blue-700', 'tour_completed' => 'bg-cyan-50 text-cyan-700',
        'proposal_sent' => 'bg-amber-50 text-amber-700', 'proposal_accepted' => 'bg-lime-50 text-lime-700',
        'lease_signed' => 'bg-emerald-50 text-emerald-700', 'inactive' => 'bg-zinc-100 text-zinc-500',
    ];
@endphp

<x-layouts.app title="Leasing Activity | RSFLA">
    @if (session('status'))<div class="mb-5 rounded-lg border border-[#8DC442]/30 bg-[#8DC442]/10 p-4 text-sm font-semibold text-[#4f7423]">{{ session('status') }}</div>@endif
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Leasing Activity</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Maintain the leasing stages shown in client reports.</p>
        </div>
        <a href="{{ route('pipeline.create', ['property_id' => $filters['property_id'] ?? null]) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Add Leasing Activity</a>
    </div>

    <form method="GET" action="{{ route('pipeline.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#424143]/10 bg-white p-4 shadow-sm lg:grid-cols-[1fr_1fr_2fr_auto]">
        <select name="property_id" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442]">
            <option value="">All properties</option>
            @foreach ($properties as $property)<option value="{{ $property->id }}" @selected(($filters['property_id'] ?? '') == $property->id)>{{ $property->name }}</option>@endforeach
        </select>
        <select name="status" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442]">
            <option value="">All stages</option>
            @foreach ($statuses as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ \App\Models\Prospect::statusFormLabel($status) }}</option>@endforeach
        </select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search tenant, broker, use, suite..." class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442]">
        <div class="flex gap-2">
            <button class="inline-flex h-10 items-center rounded-md bg-[#424143] px-4 text-sm font-semibold text-white" type="submit">Filter</button>
            <a href="{{ route('pipeline.index') }}" class="inline-flex h-10 items-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143]">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-[#424143]/10 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#424143]/10 text-sm">
                <thead class="bg-[#f7f8f5] text-left text-xs font-semibold uppercase tracking-wide text-[#424143]/60">
                    <tr>@foreach (['Property', 'Tenant / Prospect', 'Date', 'Suite', 'Use', 'RSF', 'Timing', 'Broker', 'Stage', ''] as $heading)<th class="whitespace-nowrap px-4 py-3">{{ $heading }}</th>@endforeach</tr>
                </thead>
                <tbody class="divide-y divide-[#424143]/10">
                    @forelse ($prospects as $prospect)
                        <tr class="transition hover:bg-[#f7f8f5]">
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-[#424143]">{{ $prospect->property->name }}</td>
                            <td class="px-4 py-3"><div class="font-semibold text-[#424143]">{{ $prospect->tenant ?: $prospect->full_name }}</div>@if ($prospect->public_notes)<div class="mt-1 max-w-sm truncate text-xs text-[#4f7423]">{{ $prospect->public_notes }}</div>@endif</td>
                            <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $prospect->opportunity_date?->format('m-d-Y') ?: '—' }}</td>
                            <td class="px-4 py-3 text-[#424143]/70">{{ $prospect->suite ?: '—' }}</td>
                            <td class="px-4 py-3 text-[#424143]/70">{{ $prospect->use_type ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $prospect->rsf ? number_format($prospect->rsf) : '—' }}</td>
                            <td class="px-4 py-3 text-[#424143]/70">{{ $prospect->timing ?: '—' }}</td>
                            <td class="px-4 py-3 text-[#424143]/70">{{ $prospect->broker ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$prospect->status] ?? 'bg-zinc-100 text-zinc-600' }}">{{ \App\Models\Prospect::statusFormLabel($prospect->status) }}</span></td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('pipeline.edit', $prospect) }}" class="font-semibold text-[#4f7423]">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-4 py-8 text-center text-[#424143]/60">No leasing activity found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $prospects->links() }}</div>
</x-layouts.app>
