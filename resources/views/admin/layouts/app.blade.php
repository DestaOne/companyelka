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

            <a href="{{ route('admin.home.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.home.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                Home
            </a>
            <a href="{{ route('admin.services.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.services.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                Layanan
            </a>
            <a href="{{ route('admin.about.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.about.*', 'admin.contact.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                Tentang
            </a>
            <a href="{{ route('admin.gallery.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.gallery.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                Galeri
            </a>
            <a href="{{ route('admin.messages.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.messages.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                Pesan Masuk
                @if($unreadMessages > 0)
                    <span class="ml-auto rounded-full bg-red-600 px-2 py-0.5 text-[11px] font-bold leading-4 text-white">{{ $unreadMessages > 99 ? '99+' : $unreadMessages }}</span>
                @endif
            </a>
            <a href="{{ route('admin.activities.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.activities.index') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                Aktivitas
            </a>

            <p class="px-3 pb-1 pt-6 text-xs font-semibold uppercase tracking-wider text-blue-300">Component</p>

            <a href="{{ route('admin.logo.index') }}" class="{{ $navItem }} {{ request()->routeIs('admin.logo.*') ? $navActive : $navIdle }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg>
                Logo
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

            <!-- Notifikasi aktivitas -->
            <details class="relative" id="activity-menu">
                <summary class="relative flex size-10 cursor-pointer list-none items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 [&::-webkit-details-marker]:hidden" aria-label="Notifikasi{{ $unreadActivities > 0 ? ', '.$unreadActivities.' belum dibaca' : '' }}">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    @if($unreadActivities > 0)
                        <span id="activity-badge" class="absolute right-0.5 top-0.5 flex min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold leading-5 text-white ring-2 ring-white">{{ $unreadActivities > 9 ? '9+' : $unreadActivities }}</span>
                    @endif
                </summary>
                <div class="absolute right-0 mt-3 w-80 max-w-[calc(100vw-2rem)] rounded-xl border border-slate-200 bg-white shadow-lg">
                    <p class="border-b border-slate-200 px-4 py-3 text-sm font-semibold text-slate-950">Aktivitas Terbaru</p>
                    <ul class="max-h-96 divide-y divide-slate-100 overflow-y-auto">
                        @forelse($activities as $activity)
                            @php $isNew = $activitiesReadAt && $activity->created_at > $activitiesReadAt; @endphp
                            @if($activity->isVisitorMessage())
                                <li class="{{ $isNew ? 'bg-blue-50' : '' }}">
                                    <a href="{{ $activity->contact_message_id ? route('admin.messages.show', $activity->contact_message_id) : route('admin.messages.index') }}" class="flex gap-x-3 px-4 py-3 hover:bg-slate-50">
                                        <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="break-words text-sm text-slate-700">{{ $activity->description }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $activity->created_at->locale('id')->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                </li>
                            @else
                                <li class="flex gap-x-3 px-4 py-3 {{ $isNew ? 'bg-blue-50' : '' }}">
                                    <x-avatar :user="$activity->user" size="size-8" text="text-xs" class="mt-0.5" />
                                    <div class="min-w-0">
                                        <p class="break-words text-sm text-slate-700"><span class="font-semibold text-slate-950">{{ $activity->user?->name ?? 'Admin' }}</span> {{ $activity->description }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $activity->created_at->locale('id')->diffForHumans() }}</p>
                                    </div>
                                </li>
                            @endif
                        @empty
                            <li class="px-4 py-8 text-center text-sm text-slate-500">Belum ada aktivitas.</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('admin.activities.index') }}" class="block rounded-b-xl border-t border-slate-200 px-4 py-3 text-center text-sm font-medium text-blue-700 hover:bg-slate-50">Lihat semua aktivitas</a>
                </div>
            </details>
            <form id="activity-read-form" method="POST" action="{{ route('admin.activities.read') }}" class="hidden">@csrf</form>

            <!-- User menu -->
            <details class="relative">
                <summary class="flex cursor-pointer list-none items-center [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center gap-x-3">
                        <span class="hidden text-right leading-tight sm:block">
                            <span class="block text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                            <span class="block text-xs text-slate-500">Administrator</span>
                        </span>
                        <x-avatar :user="auth()->user()" class="ring-2 ring-white" />
                    </span>
                </summary>
                <div class="absolute right-0 mt-3 w-56 rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                    <div class="px-3 py-2">
                        <p class="truncate text-sm font-semibold text-slate-950">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="my-1 border-t border-slate-200"></div>
                    <a href="{{ route('admin.profile.edit') }}" class="flex w-full items-center gap-x-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                        Profil
                    </a>
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

<!-- Dialog konfirmasi hapus: aktif untuk form ber-atribut data-confirm-message -->
<dialog id="confirm-dialog" aria-labelledby="confirm-title" aria-describedby="confirm-message"
        class="m-auto w-[calc(100%-2rem)] max-w-md rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/50 backdrop:backdrop-blur-sm">
    <div class="p-6">
        <div class="flex items-start gap-x-4">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </span>
            <div class="min-w-0">
                <h2 id="confirm-title" class="text-base font-semibold text-slate-950"></h2>
                <p id="confirm-message" class="mt-2 break-words text-sm text-slate-600"></p>
            </div>
        </div>
    </div>
    <div class="flex justify-end gap-x-3 rounded-b-2xl bg-slate-50 px-6 py-4">
        <button type="button" id="confirm-cancel" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Batal</button>
        <button type="button" id="confirm-ok" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Ya, hapus</button>
    </div>
</dialog>

<script>
    // Membuka notifikasi menandai semuanya terbaca; item baru tetap disorot sampai halaman dimuat ulang.
    (function () {
        var menu = document.getElementById('activity-menu');
        var form = document.getElementById('activity-read-form');
        if (!menu || !form) { return; }

        menu.addEventListener('toggle', function () {
            var badge = document.getElementById('activity-badge');
            if (!menu.open || !badge) { return; }
            badge.remove();
            fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        });

        document.addEventListener('click', function (e) {
            if (menu.open && !menu.contains(e.target)) { menu.open = false; }
        });
    })();
</script>

<script>
    (function () {
        var dialog = document.getElementById('confirm-dialog');
        var pending = null;

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.dataset || !form.dataset.confirmMessage) { return; }
            e.preventDefault();
            pending = form;
            document.getElementById('confirm-title').textContent = form.dataset.confirmTitle || 'Konfirmasi';
            document.getElementById('confirm-message').textContent = form.dataset.confirmMessage;
            dialog.showModal();
            document.getElementById('confirm-cancel').focus();
        });

        document.getElementById('confirm-cancel').addEventListener('click', function () { pending = null; dialog.close(); });
        dialog.addEventListener('click', function (e) { if (e.target === dialog) { pending = null; dialog.close(); } });
        document.getElementById('confirm-ok').addEventListener('click', function () {
            var form = pending;
            pending = null;
            dialog.close();
            if (form) { form.submit(); }
        });
    })();

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }
</script>
</body>
</html>
