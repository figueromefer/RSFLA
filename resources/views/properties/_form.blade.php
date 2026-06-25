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
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Property Profile</h2>
        <p class="mt-1 text-sm text-[#424143]/60">Core identity and report presentation for this property.</p>
    </div>

    <div>
        <label for="name" class="text-sm font-semibold text-[#424143]">Name</label>
        <input id="name" name="name" value="{{ old('name', $property->name) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="slug" class="text-sm font-semibold text-[#424143]">Slug</label>
        <input id="slug" name="slug" value="{{ old('slug', $property->slug) }}" placeholder="Auto-generated if blank" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        <p class="mt-1 text-xs text-[#424143]/55">Used in report URLs. Leave blank when creating to generate from name.</p>
    </div>

    <div>
        <label for="address" class="text-sm font-semibold text-[#424143]">Address</label>
        <input id="address" name="address" value="{{ old('address', $property->street_address) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div class="grid gap-5 sm:grid-cols-[1fr_120px]">
        <div>
            <label for="city" class="text-sm font-semibold text-[#424143]">City</label>
            <input id="city" name="city" value="{{ old('city', $property->city) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>

        <div>
            <label for="state" class="text-sm font-semibold text-[#424143]">State</label>
            <input id="state" name="state" value="{{ old('state', $property->state) }}" required maxlength="2" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm uppercase outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>
    </div>

    <div>
        <label for="hero_image" class="text-sm font-semibold text-[#424143]">Hero image URL</label>
        <input id="hero_image" name="hero_image" type="url" value="{{ old('hero_image', $property->hero_image) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="report_title" class="text-sm font-semibold text-[#424143]">Report title</label>
        <input id="report_title" name="report_title" value="{{ old('report_title', $property->report_title) }}" placeholder="{{ $property->name ?: 'Owner Report' }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
</div>

<div class="border-t border-[#424143]/10 pt-5">
    <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Assigned Team</h2>
    <p class="mt-1 text-sm text-[#424143]/60">These team members appear on the property workspace and client report when active.</p>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($teamMembers as $teamMember)
            <label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-white p-3 text-sm text-[#424143] transition hover:border-[#8DC442]">
                <input name="team_member_ids[]" type="checkbox" value="{{ $teamMember->id }}" @checked(in_array($teamMember->id, old('team_member_ids', $property->exists ? $property->teamMembers->pluck('id')->all() : []), true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
                <span class="min-w-0">
                    <span class="block truncate font-semibold">{{ $teamMember->name }}</span>
                    <span class="block truncate text-xs text-[#424143]/55">{{ $teamMember->email ?: 'No email' }}{{ $teamMember->is_active ? '' : ' · inactive' }}</span>
                </span>
            </label>
        @endforeach
    </div>
</div>

<label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 text-sm font-medium text-[#424143]">
    <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $property->is_active ?? true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
    <span>
        <span class="block font-semibold">Active property</span>
        <span class="mt-1 block font-normal text-[#424143]/60">Inactive properties remain available internally but are hidden from client report access.</span>
    </span>
</label>
