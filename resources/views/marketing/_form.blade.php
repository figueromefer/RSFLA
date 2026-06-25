@php
    $selectedProperty = old('property_id', $marketingActivity->property_id);
    $selectedType = old('type', $marketingActivity->type);
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
        <h2 class="font-rsfla-heading text-xl font-bold text-[#424143]">Marketing Activity</h2>
        <p class="mt-1 text-sm text-[#424143]/60">Track campaigns, outreach, listing updates, and client-visible activity.</p>
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
        <label for="type" class="text-sm font-semibold text-[#424143]">Type</label>
        <select id="type" name="type" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm text-[#424143] outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            @foreach ($types as $type)
                <option value="{{ $type }}" @selected($selectedType === $type)>{{ \App\Models\MarketingActivity::typeLabel($type) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="title" class="text-sm font-semibold text-[#424143]">Title</label>
        <input id="title" name="title" value="{{ old('title', $marketingActivity->title) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="activity_date" class="text-sm font-semibold text-[#424143]">Activity date</label>
        <input id="activity_date" name="activity_date" type="date" value="{{ old('activity_date', optional($marketingActivity->activity_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="metric_label" class="text-sm font-semibold text-[#424143]">Metric label</label>
        <input id="metric_label" name="metric_label" value="{{ old('metric_label', $marketingActivity->metric_label) }}" placeholder="Emails sent, impressions, brokers reached" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div>
        <label for="metric_value" class="text-sm font-semibold text-[#424143]">Metric value</label>
        <input id="metric_value" name="metric_value" value="{{ old('metric_value', $marketingActivity->metric_value) }}" placeholder="1,250" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div class="lg:col-span-2">
        <label for="url" class="text-sm font-semibold text-[#424143]">URL</label>
        <input id="url" name="url" type="url" value="{{ old('url', $marketingActivity->url) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>

    <div class="lg:col-span-2">
        <label for="description" class="text-sm font-semibold text-[#424143]">Description</label>
        <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-md border border-[#424143]/20 bg-white px-3 py-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">{{ old('description', $marketingActivity->description) }}</textarea>
    </div>
</div>

<label class="flex items-start gap-3 rounded-lg border border-[#424143]/10 bg-[#f7f8f5] p-4 text-sm font-medium text-[#424143]">
    <input name="visible_to_client" type="checkbox" value="1" @checked(old('visible_to_client', $marketingActivity->visible_to_client ?? true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
    <span>
        <span class="block font-semibold">Visible to client report</span>
        <span class="mt-1 block font-normal text-[#424143]/60">Internal marketing notes remain hidden from client-facing reports.</span>
    </span>
</label>
