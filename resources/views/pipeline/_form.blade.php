@php
    $selectedProperty = old('property_id', $prospect->property_id);
    $selectedStatus = old('status', $prospect->status ?? 'prospect');
@endphp

@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <div class="font-semibold">Review the highlighted fields.</div>
        <ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="grid gap-5 lg:grid-cols-2">
    <div class="lg:col-span-2">
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Leasing Activity</h2>
        <p class="mt-1 text-sm font-medium text-[#4f7423]">Information displayed in the client report.</p>
    </div>
    <div>
        <label for="property_id" class="text-sm font-semibold text-[#424143]">Property</label>
        <select id="property_id" name="property_id" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">Select property</option>
            @foreach ($properties as $property)<option value="{{ $property->id }}" @selected((string) $selectedProperty === (string) $property->id)>{{ $property->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label for="status" class="text-sm font-semibold text-[#424143]">Stage / Status</label>
        <select id="status" name="status" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            @foreach ($statuses as $status)<option value="{{ $status }}" @selected($selectedStatus === $status)>{{ \App\Models\Prospect::statusFormLabel($status) }}</option>@endforeach
        </select>
    </div>
    <div>
        <label for="opportunity_date" class="text-sm font-semibold text-[#424143]">Date</label>
        <input id="opportunity_date" name="opportunity_date" type="date" value="{{ old('opportunity_date', $prospect->opportunity_date?->format('Y-m-d')) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        <p class="mt-1 text-xs text-[#424143]/55">Shown in reports as MM-DD-YYYY.</p>
    </div>
    <div>
        <label for="tenant" class="text-sm font-semibold text-[#424143]">Tenant / Prospect Name</label>
        <input id="tenant" name="tenant" value="{{ old('tenant', $prospect->tenant) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    @foreach (['suite' => 'Suite', 'use' => 'Use', 'timing' => 'Timing', 'broker' => 'Broker'] as $field => $label)
        <div>
            <label for="{{ $field }}" class="text-sm font-semibold text-[#424143]">{{ $label }}</label>
            <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $field === 'use' ? $prospect->use_type : $prospect->{$field}) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>
    @endforeach
    <div>
        <label for="rsf" class="text-sm font-semibold text-[#424143]">RSF</label>
        <input id="rsf" name="rsf" type="number" min="0" value="{{ old('rsf', $prospect->rsf) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    <div class="lg:col-span-2">
        <label for="public_notes" class="text-sm font-semibold text-[#424143]">Public Notes</label>
        <textarea id="public_notes" name="public_notes" rows="4" class="mt-2 w-full rounded-md border border-[#424143]/20 bg-white px-3 py-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">{{ old('public_notes', $prospect->public_notes) }}</textarea>
        <p class="mt-1 text-xs font-medium text-[#4f7423]">Shown in the client report.</p>
    </div>
</div>

<label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 text-sm font-medium text-[#424143]">
    <input name="visible_to_client" type="checkbox" value="1" @checked(old('visible_to_client', $prospect->visible_to_client ?? true)) class="size-4 rounded border-[#424143]/20 accent-[#8DC442]">
    <span><span class="block font-semibold">Show in client report</span><span class="mt-1 block font-normal text-[#424143]/60">New leasing activity is shown by default.</span></span>
</label>
