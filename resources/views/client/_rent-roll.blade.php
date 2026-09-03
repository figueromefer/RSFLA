<section id="rent-roll" class="report-rent print-card rounded-xl border border-[#424143]/10 bg-white px-5 py-5 sm:px-6">
    <h2 class="font-rsfla-heading text-xl font-bold uppercase tracking-wide text-[#424143]">Rent Roll</h2>
    <div class="mt-4 overflow-x-auto">
        <table class="rent-roll-table min-w-[1050px] w-full border-collapse text-left text-xs">
            <thead>
                <tr class="border-b border-[#424143]/15 bg-[#f7f8f5] text-[11px] font-semibold uppercase tracking-wide text-[#424143]/60">
                    @foreach (['Company', 'Suite', 'Lease Commencement', 'Lease Expiration', 'RSF Occupied', 'Lease Term', 'Start Rent', 'Rent Increases', 'Free Rent'] as $heading)
                        <th class="px-3 py-3 whitespace-nowrap">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-[#424143]/10">
                @forelse ($property->rentRollEntries as $entry)
                    <tr class="rent-roll-row {{ $entry->is_vacant ? 'bg-[#8DC442]/8' : '' }}">
                        <td class="px-3 py-3 font-semibold text-[#424143]">{{ $entry->is_vacant ? 'Vacant' : ($entry->tenant_name ?: '—') }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-[#424143]/70">{{ $entry->suite ?: '—' }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-[#424143]/70">{{ ! $entry->is_vacant ? ($entry->lease_commencement_date?->format('m-d-Y') ?? '—') : '—' }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-[#424143]/70">{{ ! $entry->is_vacant ? ($entry->lease_expiration_date?->format('m-d-Y') ?? '—') : '—' }}</td>
                        <td class="px-3 py-3 whitespace-nowrap text-[#424143]/70">{{ $entry->square_footage !== null ? number_format($entry->square_footage) : '—' }}</td>
                        @foreach (['lease_term', 'start_rent', 'rent_increases', 'free_rent'] as $field)
                            <td class="px-3 py-3 text-[#424143]/70">{{ ! $entry->is_vacant ? ($entry->{$field} ?: '—') : '—' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-3 py-8 text-center text-sm text-[#424143]/55">No rent roll data available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
