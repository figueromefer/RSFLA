<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'RSFLA') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            @media print {
                @page {
                    size: letter;
                    margin: 0.4in 0.45in;
                }

                body {
                    background: #ffffff !important;
                    color: #424143 !important;
                }

                header,
                footer,
                .print-hidden {
                    display: none !important;
                }

                main {
                    max-width: none !important;
                    padding: 0 !important;
                }

                h1 {
                    font-size: 28pt !important;
                }

                h2 {
                    font-size: 18pt !important;
                }

                p,
                dd,
                dt,
                time,
                span,
                a {
                    font-size: 9pt;
                }

                section,
                article,
                aside,
                .print-card,
                .report-section {
                    break-inside: avoid;
                    page-break-inside: avoid;
                    box-shadow: none !important;
                }

                .grid {
                    break-inside: avoid;
                }

                details.report-section > :not(summary) {
                    display: block !important;
                }
            }
        </style>
    </head>
    <body class="min-h-screen bg-white font-sans text-[#424143] antialiased">
        <div class="min-h-screen bg-[linear-gradient(180deg,#ffffff_0%,#f7f8f5_52%,#ffffff_100%)]">
            <header class="border-b border-[#424143]/10 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-[1440px] items-center justify-between gap-4 px-5 py-3 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex shrink-0 items-center" aria-label="RSFLA home">
                        <img src="{{ asset('images/rsfla-logo.png') }}" alt="RSFLA" class="h-9 w-auto object-contain sm:h-10">
                    </a>

                    @auth
                        @if (auth()->user()->hasRole('admin', 'staff'))
                            <nav class="print-hidden hidden items-center gap-1 rounded-md border border-[#424143]/10 bg-[#f7f8f5] p-1 text-sm text-[#424143]/70 lg:flex">
                                @foreach ([
                                    ['label' => 'Properties', 'route' => 'properties.index', 'active' => 'properties.*'],
                                    ['label' => 'Leasing Activity', 'route' => 'pipeline.index', 'active' => 'pipeline.*'],
                                    ['label' => 'Marketing Activity', 'route' => 'marketing.index', 'active' => 'marketing.*'],
                                    ['label' => 'Documents', 'route' => 'documents.index', 'active' => 'documents.*'],
                                    ['label' => 'Users', 'route' => 'users.index', 'active' => 'users.*'],
                                ] as $item)
                                    <a href="{{ route($item['route']) }}" class="rounded px-3 py-1.5 transition hover:bg-white hover:text-[#424143] {{ request()->routeIs($item['active']) ? 'bg-white text-[#424143] shadow-sm ring-1 ring-[#424143]/5' : '' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </nav>
                        @endif
                    @endauth

                    <nav class="flex items-center gap-3 text-sm text-[#424143]/70">
                    @auth
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="inline-flex h-9 items-center rounded-md border border-[#424143]/15 bg-white px-3 text-sm font-medium text-[#424143] shadow-sm transition hover:border-[#8DC442] hover:text-[#424143]" type="submit">Logout</button>
                        </form>
                    @else
                        <a class="inline-flex h-9 items-center rounded-md bg-[#8DC442] px-3 text-sm font-semibold text-[#243018] shadow-sm transition hover:bg-[#7ab336]" href="{{ route('login') }}">Login</a>
                    @endauth
                </nav>
                </div>
            </header>

            <main class="mx-auto max-w-[1440px] px-5 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            <footer class="mx-auto max-w-[1440px] px-5 pb-6 pt-2 text-xs text-[#424143]/50 sm:px-6 lg:px-8">
                <div class="border-t border-[#424143]/10 pt-4">RSFLA property intelligence platform</div>
            </footer>
        </div>
    </body>
</html>
