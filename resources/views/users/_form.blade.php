@php
    $selectedRole = old('role', $user->role);
    $selectedProperties = old('property_ids', $user->exists ? $user->properties->pluck('id')->all() : []);
@endphp

@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <div class="font-semibold">Review the highlighted fields.</div>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-5 lg:grid-cols-2">
    <div class="lg:col-span-2">
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Account</h2>
        <p class="mt-1 text-sm text-[#424143]/60">Control login access, role, and client property visibility.</p>
    </div>

    <div>
        <label for="name" class="text-sm font-semibold text-[#424143]">Name</label>
        <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="email" class="text-sm font-semibold text-[#424143]">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="role" class="text-sm font-semibold text-[#424143]">Role</label>
        <select id="role" name="role" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected($selectedRole === $role)>{{ str($role)->title() }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="grid gap-5 border-t border-[#424143]/10 pt-5 lg:grid-cols-2">
    <div>
        <label for="password" class="text-sm font-semibold text-[#424143]">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        <p class="mt-1 text-xs text-[#424143]/55">{{ $user->exists ? 'Leave blank to keep current password.' : 'Required for new users.' }}</p>
    </div>

    <div>
        <label for="password_confirmation" class="text-sm font-semibold text-[#424143]">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
</div>

<div class="border-t border-[#424143]/10 pt-5">
    <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Assigned Properties</h2>
    <p class="mt-1 text-sm text-[#424143]/60">Used only for client users. Admin and staff accounts do not require property assignments.</p>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($properties as $property)
            <label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-white p-3 text-sm text-[#424143] transition hover:border-[#8DC442]">
                <input name="property_ids[]" type="checkbox" value="{{ $property->id }}" @checked(in_array($property->id, array_map('intval', $selectedProperties), true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
                <span class="min-w-0">
                    <span class="block truncate font-semibold">{{ $property->name }}</span>
                    <span class="block truncate text-xs text-[#424143]/55">{{ $property->city }}, {{ $property->state }}</span>
                </span>
            </label>
        @endforeach
    </div>
</div>

<label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 text-sm font-medium text-[#424143]">
    <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $user->is_active ?? true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
    <span>
        <span class="block font-semibold">Active user</span>
        <span class="mt-1 block font-normal text-[#424143]/60">Inactive users cannot sign in.</span>
    </span>
</label>
