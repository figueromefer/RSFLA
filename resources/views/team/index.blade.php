<x-layouts.app title="Team | RSFLA">
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-[#8DC442]/30 bg-[#8DC442]/10 p-4 text-sm font-semibold text-[#4f7423]">{{ session('status') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Team</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Manage RSFLA team profiles, contact details, and property assignments.</p>
        </div>
        <a href="{{ route('team.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Create team member</a>
    </div>

    <form method="GET" action="{{ route('team.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#424143]/10 bg-white p-4 shadow-sm lg:grid-cols-[1fr_1fr_auto]">
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, email, phone, DRE..." class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        <select name="is_active" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">All status</option>
            <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Active</option>
            <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inactive</option>
        </select>
        <div class="flex gap-2">
            <button class="inline-flex h-10 items-center justify-center rounded-md bg-[#424143] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#2f2e30]" type="submit">Filter</button>
            <a href="{{ route('team.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Reset</a>
        </div>
    </form>

    <section class="grid gap-4 xl:grid-cols-2">
        @forelse ($teamMembers as $teamMember)
            <article class="rounded-lg border border-[#424143]/10 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-md bg-[#8DC442]/15 text-sm font-semibold text-[#4f7423]">
                        @if ($teamMember->photo)
                            <img src="{{ $teamMember->photo }}" alt="{{ $teamMember->name }}" class="h-full w-full object-cover">
                        @else
                            {{ str($teamMember->name)->explode(' ')->map(fn ($part) => str($part)->substr(0, 1))->take(2)->implode('') }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="font-rsfla-heading text-2xl font-bold text-[#424143]">{{ $teamMember->name }}</h2>
                                <p class="mt-1 text-sm text-[#424143]/60">{{ $teamMember->dre ?: 'No DRE' }} · {{ $teamMember->properties_count }} properties</p>
                            </div>
                            <span class="inline-flex h-6 w-fit items-center rounded-full px-2.5 text-xs font-semibold {{ $teamMember->is_active ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">{{ $teamMember->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <div class="mt-4 grid gap-2 text-sm text-[#424143]/70 sm:grid-cols-2">
                            <div>{{ $teamMember->phone ?: 'No phone' }}</div>
                            <div>{{ $teamMember->email ?: 'No email' }}</div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3">
                            @if ($teamMember->bio_url)
                                <a href="{{ $teamMember->bio_url }}" target="_blank" rel="noopener" class="text-sm font-semibold text-[#4f7423] transition hover:text-[#8DC442]">Bio</a>
                            @endif
                            <a href="{{ route('team.edit', $teamMember) }}" class="text-sm font-semibold text-[#4f7423] transition hover:text-[#8DC442]">Edit</a>
                            <form method="POST" action="{{ route('team.destroy', $teamMember) }}" onsubmit="return confirm('Delete this team member?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm font-semibold text-red-600 transition hover:text-red-700" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-[#424143]/10 bg-white p-6 text-sm text-[#424143]/65 shadow-sm">No team members match the current filters.</div>
        @endforelse
    </section>

    <div class="mt-5">
        {{ $teamMembers->links() }}
    </div>
</x-layouts.app>
