<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') - Admin Elka Mandiri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        /* Konten memudar masuk setiap berpindah halaman; sidebar dan topbar tetap diam. */
        @keyframes page-fade-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        .page-fade-in { animation: page-fade-in 350ms ease-out both; }
        @media (prefers-reduced-motion: reduce) { .page-fade-in { animation: none; } }
    </style>
</head>
<body class="h-full bg-blue-50 text-slate-950 antialiased">

<!-- Lingkaran dekoratif latar (sama seperti halaman login) -->
<div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
    <div class="absolute -left-40 top-1/4 size-[32rem] rounded-full bg-blue-100"></div>
    <div class="absolute -right-24 -top-24 size-80 rounded-full bg-blue-100/70"></div>
    <div class="absolute bottom-[-8rem] right-1/4 size-72 rounded-full bg-blue-200/50"></div>
</div>

@php
    $navItem = 'group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors';
    $navActive = 'bg-white text-blue-800 shadow-sm';
    $navIdle = 'text-blue-100 hover:bg-white/10 hover:text-white';
    $initial = strtoupper(mb_substr(auth()->user()->name, 0, 1));
@endphp

<div class="min-h-full">

    <!-- Overlay (mobile) -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-30 hidden bg-slate-950/50 lg:hidden"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col p-0 transition-transform duration-200 lg:translate-x-0 lg:p-4">
      <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden bg-gradient-to-br from-blue-800 via-blue-700 to-blue-900 shadow-xl shadow-blue-900/20 lg:rounded-2xl">
        <div class="pointer-events-none absolute -left-16 -top-16 size-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-20 -right-10 size-64 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-8 top-24 size-3 rounded-full bg-blue-300"></div>
        <div class="pointer-events-none absolute bottom-32 left-8 size-2.5 rounded-full bg-blue-200"></div>
        <div class="relative flex h-16 shrink-0 items-center justify-center px-6">
            <span class="inline-flex rounded-lg bg-white px-2.5 py-1.5"><img src="{{ asset('images/elkamandiri.jpeg') }}" alt="Elka Mandiri" class="h-7 w-auto"></span>
        </div>

        <nav class="relative flex-1 space-y-1 overflow-y-auto px-4 py-6">
            <a href="{{ route('admin.dashboard') }}" class="{{ $navItem }} {{ request()->routeIs('admin.dashboard') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Dashboard
            </a>

            <p class="px-3 pb-1 pt-6 text-xs font-semibold uppercase tracking-wider text-blue-300">Konten</p>

            <a href="{{ route('admin.services.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.services.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                Layanan
            </a>
            <a href="{{ route('admin.gallery.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.gallery.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                Galeri
            </a>
        </nav>
      </div>
    </aside>

    <!-- MAIN -->
    <div class="lg:pl-72">

        <!-- TOPBAR -->
        <header class="sticky top-0 z-20 mx-4 mt-4 flex h-16 items-center gap-x-4 rounded-2xl bg-white px-4 shadow-md shadow-slate-200/70 ring-1 ring-slate-200 sm:px-6 lg:mx-8">
            <button type="button" onclick="toggleSidebar()" class="-ml-2 rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Buka menu">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
            </button>

            <div class="flex-1"></div>

            <a href="{{ url('/') }}" target="_blank" rel="noopener" class="hidden items-center gap-x-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 sm:inline-flex">
                Lihat situs
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
            </a>

            <!-- User menu -->
            <details class="relative">
                <summary class="flex cursor-pointer list-none items-center [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center gap-x-3">
                        <span class="hidden text-right leading-tight sm:block">
                            <span class="block text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                            <span class="block text-xs text-slate-500">Administrator</span>
                        </span>
                        <span class="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-700 to-blue-800 text-sm font-semibold text-white ring-2 ring-white">{{ $initial }}</span>
                    </span>
                </summary>
                <div class="absolute right-0 mt-3 w-56 rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                    <div class="px-3 py-2">
                        <p class="truncate text-sm font-semibold text-slate-950">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="my-1 border-t border-slate-200"></div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-x-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </details>
        </header>

        <main class="page-fade-in mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-12">
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">@yield('title', 'Dashboard')</h1>
                @yield('actions')
            </div>

            @if(session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }
</script>
</body>
</html>
