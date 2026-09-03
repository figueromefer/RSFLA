@php
    $marketValues = [
        'RBA' => $property->market_rba,
        'Vacancy' => $property->market_vacancy,
        'Sublet %' => $property->market_sublet_percentage,
        'YTD Absorption' => $property->market_ytd_absorption,
    ];
@endphp

<section id="market-data" class="report-market print-card rounded-xl border border-[#424143]/10 bg-white px-5 py-5 sm:px-6">
    <h2 class="font-rsfla-heading text-xl font-bold uppercase tracking-wide text-[#424143]">Market Data</h2>
    @if (filled($property->market_notes))
        <p class="stage-description mt-0.5 text-sm text-[#424143]/55">{{ $property->market_notes }}</p>
    @endif
    <dl class="market-kpis mt-4 grid grid-cols-2 gap-x-8 gap-y-5 sm:grid-cols-4">
        @foreach ($marketValues as $label => $value)
            <div class="border-l-2 border-[#8DC442]/70 pl-3">
                <dt class="text-xs font-semibold uppercase tracking-wide text-[#424143]/50">{{ $label }}</dt>
                <dd class="mt-1 font-rsfla-heading text-2xl font-bold text-[#424143]">{{ filled($value) ? $value : '—' }}</dd>
            </div>
        @endforeach
    </dl>
</section>
