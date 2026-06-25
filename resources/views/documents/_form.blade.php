@php
    $selectedProperty = old('property_id', $propertyLink->property_id);
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
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Resource</h2>
        <p class="mt-1 text-sm text-[#424143]/60">Attach a link or document to a property and control whether clients can see it.</p>
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
        <label for="label" class="text-sm font-semibold text-[#424143]">Label</label>
        <input id="label" name="label" value="{{ old('label', $propertyLink->label) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div class="lg:col-span-2">
        <label for="url" class="text-sm font-semibold text-[#424143]">URL</label>
        <input id="url" name="url" type="url" value="{{ old('url', $propertyLink->url) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
</div>

<label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 text-sm font-medium text-[#424143]">
    <input name="visible_to_client" type="checkbox" value="1" @checked(old('visible_to_client', $propertyLink->is_visible_to_client ?? true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
    <span>
        <span class="block font-semibold">Visible to client report</span>
        <span class="mt-1 block font-normal text-[#424143]/60">Internal links remain available to RSFLA but are hidden from client reports.</span>
    </span>
</label>
