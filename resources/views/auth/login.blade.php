<x-layouts.app title="Login | RSFLA">
    <div class="mx-auto mt-16 max-w-md rounded-lg border border-[#424143]/10 bg-white p-8 shadow-sm">
        <div class="mb-8 h-1 w-16 bg-[#8DC442]"></div>
        <h1 class="font-rsfla-heading text-3xl font-bold text-[#424143]">Sign in</h1>
        <p class="mt-2 text-sm text-[#424143]/65">Access the internal workspace or client property reports.</p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
            @csrf

            <div>
                <label for="email" class="text-sm font-medium text-[#424143]">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-2 block h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none transition focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
                @error('email')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password" class="text-sm font-medium text-[#424143]">Password</label>
                <input id="password" name="password" type="password" required class="mt-2 block h-11 w-full rounded-md border border-[#424143]/20 bg-white px-3 text-sm outline-none transition focus:border-[#8DC442] focus:ring-2 focus:ring-[#8DC442]/20">
                @error('password')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-[#424143]/70">
                <input name="remember" type="checkbox" value="1" class="size-4 rounded border-[#424143]/20 accent-[#8DC442]">
                Remember me
            </label>

            <button class="inline-flex h-11 w-full items-center justify-center rounded-md bg-[#8DC442] px-4 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]" type="submit">Sign in</button>
        </form>
    </div>
</x-layouts.app>
