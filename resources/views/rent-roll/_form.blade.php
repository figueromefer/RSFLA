@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-5 sm:grid-cols-2">
    <label class="sm:col-span-2 flex items-start gap-3 rounded-md border border-[#8DC442]/25 bg-[#8DC442]/5 p-4 text-sm text-[#424143]">
        <input name="is_vacant" type="checkbox" value="1" @checked(old('is_vacant', $rentRollEntry->is_vacant)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
        <span><span class="block font-semibold">Vacant suite</span><span class="mt-1 block text-[#424143]/60">Tenant name and lease details may be left blank.</span></span>
    </label>
    <div>
        <label for="tenant_name" class="text-sm font-semibold text-[#424143]">Company / Tenant Name</label>
        <input id="tenant_name" name="tenant_name" value="{{ old('tenant_name', $rentRollEntry->tenant_name) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    <div>
        <label for="suite" class="text-sm font-semibold text-[#424143]">Suite</label>
        <input id="suite" name="suite" value="{{ old('suite', $rentRollEntry->suite) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    <div>
        <label for="square_footage" class="text-sm font-semibold text-[#424143]">RSF Occupied</label>
        <input id="square_footage" name="square_footage" type="number" min="0" value="{{ old('square_footage', $rentRollEntry->square_footage) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    <div>
        <label for="lease_commencement_date" class="text-sm font-semibold text-[#424143]">Lease Commencement</label>
        <input id="lease_commencement_date" name="lease_commencement_date" type="date" value="{{ old('lease_commencement_date', $rentRollEntry->lease_commencement_date?->format('Y-m-d')) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    <div>
        <label for="lease_expiration_date" class="text-sm font-semibold text-[#424143]">Lease Expiration</label>
        <input id="lease_expiration_date" name="lease_expiration_date" type="date" value="{{ old('lease_expiration_date', $rentRollEntry->lease_expiration_date?->format('Y-m-d')) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    @foreach (['lease_term' => 'Lease Term', 'start_rent' => 'Start Rent', 'rent_increases' => 'Rent Increases', 'free_rent' => 'Free Rent'] as $field => $label)
        <div>
            <label for="{{ $field }}" class="text-sm font-semibold text-[#424143]">{{ $label }}</label>
            <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $rentRollEntry->{$field}) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>
    @endforeach
</div>
