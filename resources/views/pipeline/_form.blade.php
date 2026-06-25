@php
    $selectedProperty = old('property_id', $prospect->property_id);
    $selectedStatus = old('status', $prospect->status ?? 'prospect');
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
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Opportunity</h2>
        <p class="mt-1 text-sm text-[#424143]/60">Core pipeline details used by the internal team and the client report.</p>
    </div>

    <div>
        <label for="property_id" class="text-sm font-semibold text-[#424143]">Property</label>
        <select id="property_id" name="property_id" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <option value="">Select property</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}" @selected((string) $selectedProperty === (string) $property->id)>{{ $property->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="status" class="text-sm font-semibold text-[#424143]">Status</label>
        <select id="status" name="status" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ \App\Models\Prospect::statusFormLabel($status) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="suite" class="text-sm font-semibold text-[#424143]">Suite</label>
        <input id="suite" name="suite" value="{{ old('suite', $prospect->suite) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="tenant" class="text-sm font-semibold text-[#424143]">Tenant</label>
        <input id="tenant" name="tenant" value="{{ old('tenant', $prospect->tenant) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="use" class="text-sm font-semibold text-[#424143]">Use</label>
        <input id="use" name="use" value="{{ old('use', $prospect->use_type) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="timing" class="text-sm font-semibold text-[#424143]">Timing</label>
        <input id="timing" name="timing" value="{{ old('timing', $prospect->timing) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="rsf" class="text-sm font-semibold text-[#424143]">RSF</label>
        <input id="rsf" name="rsf" type="number" min="0" value="{{ old('rsf', $prospect->rsf) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="broker" class="text-sm font-semibold text-[#424143]">Broker</label>
        <input id="broker" name="broker" value="{{ old('broker', $prospect->broker) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div class="border-t border-[#424143]/10 pt-5 lg:col-span-2">
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Contact</h2>
        <p class="mt-1 text-sm text-[#424143]/60">Primary contact details for follow-up and activity history.</p>
    </div>

    <div>
        <label for="contact_name" class="text-sm font-semibold text-[#424143]">Contact name</label>
        <input id="contact_name" name="contact_name" value="{{ old('contact_name', $prospect->contact_name) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="email" class="text-sm font-semibold text-[#424143]">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $prospect->email) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="phone" class="text-sm font-semibold text-[#424143]">Phone</label>
        <input id="phone" name="phone" value="{{ old('phone', $prospect->phone) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="sort_order" class="text-sm font-semibold text-[#424143]">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $prospect->sort_order ?? 0) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
</div>

<div class="border-t border-[#424143]/10 pt-5">
    <label for="notes" class="text-sm font-semibold text-[#424143]">Notes</label>
    <textarea id="notes" name="notes" rows="5" class="mt-2 w-full rounded-md border border-[#424143]/20 bg-white px-3 py-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">{{ old('notes', $prospect->notes) }}</textarea>
</div>

<label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 text-sm font-medium text-[#424143]">
    <input name="visible_to_client" type="checkbox" value="1" @checked(old('visible_to_client', $prospect->visible_to_client ?? true)) class="size-4 rounded border-[#424143]/20 accent-[#8DC442]">
    <span>
        <span class="block font-semibold">Visible to client report</span>
        <span class="mt-1 block font-normal text-[#424143]/60">Turn this off for internal-only opportunities, early conversations, or sensitive notes.</span>
    </span>
</label>
