        <section id="rent-roll" class="rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">Rent Roll</h2>
                    <p class="mt-1 text-sm text-[#424143]/65">Current occupied tenants and vacant suites.</p>
                </div>
                @if ($manageRentRoll ?? false)
                    <a href="{{ route('properties.rent-roll.create', $property) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018]">Add Entry</a>
                @endif
            </div>
            <div class="overflow-hidden rounded-lg border border-[#424143]/10">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#424143]/10 text-sm">
                        <thead class="bg-[#f7f8f5] text-left text-xs font-semibold uppercase tracking-wide text-[#424143]/60">
                            <tr>
                                <th class="px-4 py-3">Company</th>
                                <th class="px-4 py-3">Suite</th>
                                <th class="px-4 py-3">RSF Occupied</th>
                                <th class="px-4 py-3">Lease Commencement</th>
                                <th class="px-4 py-3">Lease Expiration</th>
                                @if ($manageRentRoll ?? false)
                                    <th class="px-4 py-3 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#424143]/10">
                            @forelse ($property->rentRollEntries as $entry)
                                <tr class="{{ $entry->is_vacant ? 'bg-[#8DC442]/8' : '' }}">
                                    <td class="px-4 py-3 font-semibold text-[#424143]">{{ $entry->is_vacant ? 'Vacant' : $entry->tenant_name }}</td>
                                    <td class="px-4 py-3 text-[#424143]/70">{{ $entry->suite ?: '-' }}</td>
                                    <td class="px-4 py-3 text-[#424143]/70">{{ $entry->square_footage !== null ? number_format($entry->square_footage) : '-' }}</td>
                                    <td class="px-4 py-3 text-[#424143]/70">{{ ! $entry->is_vacant ? ($entry->lease_commencement_date?->format('m-d-Y') ?? '-') : '-' }}</td>
                                    <td class="px-4 py-3 text-[#424143]/70">{{ ! $entry->is_vacant ? ($entry->lease_expiration_date?->format('m-d-Y') ?? '-') : '-' }}</td>
                                    @if ($manageRentRoll ?? false)
                                        <td class="px-4 py-3 text-right"><div class="flex justify-end gap-3">
                                            <a href="{{ route('properties.rent-roll.edit', [$property, $entry]) }}" class="font-semibold text-[#4f7423]">Edit</a>
                                            <button type="submit" form="delete-rent-roll-{{ $entry->id }}" class="font-semibold text-red-600">Delete</button>
                                        </div></td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ ($manageRentRoll ?? false) ? 6 : 5 }}" class="px-4 py-8 text-center text-[#424143]/60">No rent roll data available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
