<x-layouts.app title="Users | RSFLA">
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-[#8DC442]/30 bg-[#8DC442]/10 p-4 text-sm font-semibold text-[#4f7423]">{{ session('status') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Users</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Manage internal access and client property assignments.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]">Create user</a>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#424143]/10 bg-white p-4 shadow-sm lg:grid-cols-[1fr_1fr_2fr_auto]">
        <select name="role" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">All roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>{{ str($role)->title() }}</option>
            @endforeach
        </select>

        <select name="is_active" class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">All status</option>
            <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Active</option>
            <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inactive</option>
        </select>

        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name or email..." class="h-10 rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">

        <div class="flex gap-2">
            <button class="inline-flex h-10 items-center justify-center rounded-md bg-[#424143] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#2f2e30]" type="submit">Filter</button>
            <a href="{{ route('users.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-[#424143]/10 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#424143]/10">
                <thead class="bg-[#f7f8f5]">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-[#424143]/60">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Properties</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#424143]/10 text-sm">
                    @forelse ($users as $listedUser)
                        <tr class="transition hover:bg-[#f7f8f5]">
                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-[#424143]">{{ $listedUser->name }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ $listedUser->email }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-[#424143]/70">{{ str($listedUser->role)->title() }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex h-6 items-center rounded-full px-2.5 text-xs font-semibold {{ $listedUser->is_active ? 'bg-[#8DC442]/15 text-[#4f7423]' : 'bg-zinc-100 text-zinc-500' }}">{{ $listedUser->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="max-w-sm px-4 py-3 text-[#424143]/70">
                                {{ $listedUser->properties->pluck('name')->join(', ') ?: '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('users.edit', $listedUser) }}" class="font-semibold text-[#4f7423] transition hover:text-[#8DC442]">Edit</a>
                                    <form method="POST" action="{{ route('users.destroy', $listedUser) }}" onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-red-600 transition hover:text-red-700" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-[#424143]/60">No users match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#424143]/10 px-4 py-3">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>
