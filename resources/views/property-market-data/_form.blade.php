@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-5">
    <div>
        <label for="title" class="text-sm font-semibold text-[#424143]">Title</label>
        <input id="title" name="title" value="{{ old('title', $marketDataEntry->title) }}" required class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    <div>
        <label for="report_date" class="text-sm font-semibold text-[#424143]">Report Date</label>
        <input id="report_date" name="report_date" type="date" value="{{ old('report_date', $marketDataEntry->report_date?->format('Y-m-d')) }}" class="mt-2 h-11 w-full rounded-md border border-[#424143]/20 px-3 text-sm outline-none focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
    </div>
    <div>
        <label for="image" class="text-sm font-semibold text-[#424143]">Image</label>
        @if ($marketDataEntry->exists)
            <a href="{{ $marketDataEntry->image_url }}" target="_blank" rel="noopener" class="mt-2 block">
                <img src="{{ $marketDataEntry->image_url }}" alt="{{ $marketDataEntry->title }}" class="max-h-64 w-auto max-w-full rounded-md border border-[#424143]/10 object-contain">
            </a>
        @endif
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(! $marketDataEntry->exists) class="mt-2 block w-full rounded-md border border-[#424143]/20 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-[#f7f8f5] file:px-3 file:py-2 file:text-sm file:font-semibold">
        <p class="mt-1 text-xs text-[#424143]/55">JPG, JPEG, PNG, or WEBP. Maximum 10 MB. Leave blank when editing to keep the current image.</p>
    </div>
</div>
