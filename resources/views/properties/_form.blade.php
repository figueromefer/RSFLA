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
        <h2 class="font-rsfla-heading text-xl font-bold uppercase tracking-wide text-[#424143]">Property</h2>
    </div>

    <div>
        <label for="name" class="text-sm font-semibold text-[#424143]">Property Name</label>
        <input id="name" name="name" value="{{ old('name', $property->name) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
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
        <label for="property_photo" class="text-sm font-semibold text-[#424143]">Property Photo</label>
        @if ($property->property_photo_url)
            <img src="{{ $property->property_photo_url }}" alt="Current photo for {{ $property->name }}" class="mt-2 h-40 w-full rounded-md border border-[#424143]/10 object-cover">
        @endif
        <input id="property_photo" name="property_photo" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full rounded-md border border-[#424143]/20 bg-white px-3 py-2 text-sm text-[#424143] file:mr-3 file:rounded-md file:border-0 file:bg-[#f7f8f5] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-[#424143] hover:file:bg-[#eef3e8]">
        <p class="mt-1 text-xs text-[#424143]/55">JPG, PNG, or WEBP. Maximum 5 MB. Uploading a new photo replaces the current local photo.</p>
    </div>

</div>

<section class="border-t border-[#424143]/10 pt-5">
    <h2 class="font-rsfla-heading text-xl font-bold uppercase tracking-wide text-[#424143]">Property Information</h2>
    <textarea id="property_information" name="property_information" rows="7" class="mt-4 w-full rounded-md border border-[#424143]/20 bg-white px-3 py-2 text-sm leading-6 outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">{{ old('property_information', $property->property_information) }}</textarea>
    <p class="mt-1 text-xs text-[#424143]/55">General property information shown in the client report.</p>
</section>

<section id="market-data" class="border-t border-[#424143]/10 pt-5">
    <h2 class="font-rsfla-heading text-xl font-bold uppercase tracking-wide text-[#424143]">Market Data</h2>
    <div class="mt-4 grid gap-5 sm:grid-cols-2">
        <div>
            <label for="market_rba" class="text-sm font-semibold text-[#424143]">RBA</label>
            <input id="market_rba" name="market_rba" value="{{ old('market_rba', $property->market_rba) }}" placeholder="$5.73" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>
        <div>
            <label for="market_vacancy" class="text-sm font-semibold text-[#424143]">Vacancy</label>
            <input id="market_vacancy" name="market_vacancy" value="{{ old('market_vacancy', $property->market_vacancy) }}" placeholder="15.7%" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>
        <div>
            <label for="market_sublet_percentage" class="text-sm font-semibold text-[#424143]">Sublet %</label>
            <input id="market_sublet_percentage" name="market_sublet_percentage" value="{{ old('market_sublet_percentage', $property->market_sublet_percentage) }}" placeholder="8.4%" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>
        <div>
            <label for="market_ytd_absorption" class="text-sm font-semibold text-[#424143]">YTD Absorption</label>
            <input id="market_ytd_absorption" name="market_ytd_absorption" value="{{ old('market_ytd_absorption', $property->market_ytd_absorption) }}" placeholder="(285K)" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
        </div>
        <div class="sm:col-span-2">
            <label for="market_notes" class="text-sm font-semibold text-[#424143]">Market Data Subtitle</label>
            <input id="market_notes" name="market_notes" type="text" value="{{ old('market_notes', $property->market_notes) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 py-2 text-sm leading-6 outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
            <p class="mt-1 text-xs text-[#424143]/55">Short subtitle displayed below Market Data in the client report.</p>
        </div>
    </div>
</section>

@if ($property->exists)
    @include('properties._rent-roll', ['manageRentRoll' => true])
@endif

<div class="border-t border-[#424143]/10 pt-5">
    <h2 class="font-rsfla-heading text-sm font-bold uppercase tracking-wide text-[#424143]/65">Status</h2>
    <label class="mt-3 flex items-start gap-3 text-sm font-medium text-[#424143]">
        <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $property->is_active ?? true)) class="mt-0.5 size-4 rounded border-[#424143]/20 accent-[#8DC442]">
        <span>
            <span class="block font-semibold">Active property</span>
            <span class="mt-1 block font-normal text-[#424143]/60">Inactive properties remain available internally but are hidden from client report access.</span>
        </span>
    </label>
</div>
