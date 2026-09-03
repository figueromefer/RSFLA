<x-layouts.app title="Edit Rent Roll | RSFLA">
    <div class="mx-auto max-w-3xl space-y-5">
        <div>
            <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143]">Edit Tenant</h1>
            <p class="mt-2 text-sm text-[#424143]/65">Rent Roll · {{ $property->name }}</p>
        </div>
        <form method="POST" action="{{ route('properties.rent-roll.update', [$property, $rentRollEntry]) }}" class="space-y-6 rounded-lg border border-[#424143]/10 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('rent-roll._form')
            <div class="flex justify-end gap-2 border-t border-[#424143]/10 pt-5">
                <a href="{{ route('properties.edit', $property).'#rent-roll' }}" class="inline-flex h-10 items-center px-4 text-sm font-semibold text-[#424143]/65">Cancel</a>
                <button class="inline-flex h-10 items-center rounded-md bg-[#8DC442] px-5 text-sm font-semibold text-[#243018]" type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</x-layouts.app>
