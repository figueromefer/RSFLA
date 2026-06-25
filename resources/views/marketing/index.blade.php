<x-layouts.app title="Marketing | RSFLA">
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-[#8DC442]/30 bg-[#8DC442]/10 p-4 text-sm font-semibold text-[#4f7423]">{{ session('status') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Marketing</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Track campaigns, broadcast emails, listing updates, outreach, and client-visible marketing wins.</p>
        </div>
        <a href="{{ route('marketing.create', ['property_id' => $filters['property_id'] ?? null]) }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Create activity</a>
    </div>

    <form method="GET" action="{{ route('marketing.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#424143]/10 bg-white p-4 shadow-sm xl:grid-cols-[1fr_1fr_1fr_2fr_auto]">
        <select name="property_id" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">All properties</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}" @selected(($filters['property_id'] ?? '') == $property->id)>{{ $property->name }}</option>
            @endforeach
        </select>

        <select name="type" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">All types</option>
            @foreach ($types as $type)
                <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ \App\Models\MarketingActivity::typeLabel($type) }}</option>
            @endforeach
        </select>

        <select name="visible_to_client" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">All visibility</option>
            <option value="1" @selected(($filters['visible_to_client'] ?? '') === '1')>Client visible</option>
            <option value="0" @selected(($filters['visible_to_client'] ?? '') === '0')>Internal only</option>
        </select>

        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title or description..." class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">

        <div class="flex gap-2">
            <button class="inline-flex h-10 items-center justify-center rounded-md bg-[#424143] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#2f2e30]" type="submit">Filter</button>
            <a href="{{ route('marketing.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-[#424143]/10 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#424143]/10">
                <thead class="bg-[#f7f8f5]">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-[#424143]/60">
                        <th class="px-4 py-3">Property</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Visibility</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#424143]/10 text-sm">
                    @forelse ($activities as $activity)
                        <tr class="transition hover:bg-[#f7f8f5]">
                            <td class="whitespace-nowrap px-4 py-3">
                                <a href="{{ route('properties.show', $activity->property) }}" class="font-semibold text-[#424143] transition hover:text-[#4f7423]">{{ $activity->property->name }}</a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ \App\Models\MarketingActivity::typeLabel($activity->type) }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-[#424143]">{{ $activity->title }}</div>
                                <div class="max-w-lg truncate text-xs text-[#424143]/55">{{ $activity->description }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $activity->activity_date->format('M j, Y') }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $activity->visible_to_client ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">{{ $activity->visible_to_client ? 'Client visible' : 'Internal only' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('marketing.edit', $activity) }}" class="font-semibold text-[#4f7423] transition hover:text-[#8DC442]">Edit</a>
                                    <form method="POST" action="{{ route('marketing.destroy', $activity) }}" onsubmit="return confirm('Delete this marketing activity?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-red-600 transition hover:text-red-700" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-[#424143]/60">No marketing activities match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#424143]/10 px-4 py-3">
            {{ $activities->links() }}
        </div>
    </div>
</x-layouts.app>
