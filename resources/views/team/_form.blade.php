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
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Profile</h2>
        <p class="mt-1 text-sm text-[#424143]/60">Public-facing team information used in property workspaces and client reports.</p>
    </div>

    <div>
        <label for="name" class="text-sm font-semibold text-[#424143]">Name</label>
        <input id="name" name="name" value="{{ old('name', $teamMember->name) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="dre" class="text-sm font-semibold text-[#424143]">DRE</label>
        <input id="dre" name="dre" value="{{ old('dre', $teamMember->dre) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="phone" class="text-sm font-semibold text-[#424143]">Phone</label>
        <input id="phone" name="phone" value="{{ old('phone', $teamMember->phone) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="email" class="text-sm font-semibold text-[#424143]">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $teamMember->email) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="bio_url" class="text-sm font-semibold text-[#424143]">Bio URL</label>
        <input id="bio_url" name="bio_url" type="url" value="{{ old('bio_url', $teamMember->bio_url) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="photo" class="text-sm font-semibold text-[#424143]">Photo</label>
        <input id="photo" name="photo" value="{{ old('photo', $teamMember->photo) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
</div>

<label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 text-sm font-medium text-[#424143]">
    <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $teamMember->is_active ?? true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
    <span>
        <span class="block font-semibold">Active team member</span>
        <span class="mt-1 block font-normal text-[#424143]/60">Inactive members remain in history but are hidden from client reports.</span>
    </span>
</label>
