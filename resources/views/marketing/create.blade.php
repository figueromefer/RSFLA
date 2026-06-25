<x-layouts.app title="Create Marketing Activity | RSFLA">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-3 h-1 w-14 bg-[#8DC442]"></div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143] sm:text-4xl">Create Marketing Activity</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Record marketing movement for a property and client report.</p>
        </div>
        <a href="{{ route('marketing.index') }}" class="inline-flex h-10 items-center justify-center rounded-md border border-[#424143]/15 bg-white px-4 text-sm font-semibold text-[#424143] shadow-sm transition hover:border-[#8DC442]">Back to marketing</a>
    </div>

    <form method="POST" action="{{ route('marketing.store') }}" class="max-w-5xl space-y-6 rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
        @csrf
        @include('marketing._form')
        <div class="flex justify-end border-t border-[#424143]/10 pt-5">
            <button class="inline-flex h-10 items-center justify-center rounded-md bg-[#8DC442] px-5 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]" type="submit">Create activity</button>
        </div>
    </form>
</x-layouts.app>
