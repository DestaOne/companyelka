<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk - Admin Elka Mandiri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <style>body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }</style>
</head>
<body class="h-full bg-blue-50 text-slate-900 antialiased">

<div class="relative flex min-h-full items-center justify-center overflow-hidden px-4 py-10">

    <!-- Lingkaran dekoratif latar -->
    <div class="pointer-events-none absolute -left-40 top-1/4 size-[32rem] rounded-full bg-blue-100"></div>
    <div class="pointer-events-none absolute -right-24 -top-24 size-80 rounded-full bg-blue-100/70"></div>
    <div class="pointer-events-none absolute bottom-[-8rem] right-1/4 size-72 rounded-full bg-blue-200/50"></div>

    <div class="relative grid w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl shadow-blue-900/10 ring-1 ring-slate-200 lg:grid-cols-2">

        <!-- Panel ilustrasi (hanya desktop) -->
        <div class="relative hidden flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-blue-800 via-blue-700 to-blue-900 p-10 text-center text-white lg:flex">
            <div class="pointer-events-none absolute -left-16 -top-16 size-56 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-20 -right-10 size-64 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute right-12 top-14 size-4 rounded-full bg-blue-300"></div>
            <div class="pointer-events-none absolute bottom-24 left-10 size-3 rounded-full bg-blue-200"></div>

            <svg viewBox="0 0 320 240" class="relative w-full max-w-sm" fill="none" aria-hidden="true">
                <circle cx="160" cy="130" r="95" fill="white" fill-opacity=".1"/>
                <!-- Gelembung pesan -->
                <rect x="14" y="34" width="112" height="38" rx="8" fill="white"/>
                <path d="M96 72l12 14 4-14z" fill="white"/>
                <g fill="#1d4ed8"><circle cx="38" cy="53" r="4"/><circle cx="58" cy="53" r="4"/><circle cx="78" cy="53" r="4"/><circle cx="98" cy="53" r="4"/></g>
                <rect x="196" y="58" width="112" height="38" rx="8" fill="#bfdbfe"/>
                <path d="M214 96l-8 14 22-14z" fill="#bfdbfe"/>
                <g fill="#1e3a8a"><circle cx="220" cy="77" r="4"/><circle cx="240" cy="77" r="4"/><circle cx="260" cy="77" r="4"/><circle cx="280" cy="77" r="4"/></g>
                <!-- Laptop -->
                <rect x="88" y="120" width="144" height="92" rx="8" fill="#0f172a"/>
                <rect x="96" y="128" width="128" height="76" rx="4" fill="#1e40af"/>
                <rect x="108" y="140" width="52" height="6" rx="3" fill="white" fill-opacity=".85"/>
                <rect x="108" y="154" width="86" height="6" rx="3" fill="white" fill-opacity=".45"/>
                <rect x="108" y="168" width="68" height="6" rx="3" fill="white" fill-opacity=".45"/>
                <circle cx="204" cy="184" r="10" fill="#60a5fa"/>
                <path d="M70 212h180l-10 14H80z" fill="#cbd5e1"/>
                <!-- Tanaman -->
                <path d="M40 226c-2-26 6-44 18-54-2 20 0 38 8 54z" fill="#93c5fd"/>
                <path d="M62 226c0-22 10-38 24-46-4 18-4 32 0 46z" fill="#60a5fa"/>
                <path d="M258 226c4-24 14-38 28-46 0 18-4 32-10 46z" fill="#93c5fd"/>
                <path d="M276 226c-2-18 2-30 12-38 0 14 2 26 6 38z" fill="#60a5fa"/>
            </svg>

            <h2 class="relative mt-8 text-2xl font-bold tracking-tight">Kelola situs dengan mudah.</h2>
            <p class="relative mt-2 max-w-xs text-sm leading-relaxed text-blue-100">Perbarui layanan dan galeri CV Elka Mandiri dari satu panel admin.</p>
        </div>

        <!-- Form -->
        <div class="flex flex-col justify-center px-6 py-10 sm:px-12 sm:py-14">
            <img src="{{ asset('images/elkamandiri.jpeg') }}" alt="Elka Mandiri" class="h-10 w-auto self-start">

            <h1 class="mt-8 text-2xl font-extrabold tracking-tight text-slate-900">Masuk ke Akun Admin</h1>
            <p class="mt-1 text-sm text-slate-500">Panel admin CV Elka Mandiri</p>

            <form method="POST" action="{{ route('admin.login') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com"
                           class="block h-12 w-full rounded-md border bg-white px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-red-600 focus:border-red-600 focus:ring-red-600/20' : 'border-slate-300 focus:border-blue-700 focus:ring-blue-700/20' }}">
                    @error('email')
                        <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password"
                           class="block h-12 w-full rounded-md border bg-white px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 {{ $errors->has('password') ? 'border-red-600 focus:border-red-600 focus:ring-red-600/20' : 'border-slate-300 focus:border-blue-700 focus:ring-blue-700/20' }}">
                    @error('password')
                        <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-x-2 text-sm text-slate-700">
                    <input type="checkbox" name="remember" class="size-4 rounded border-slate-400 text-blue-700 focus:ring-blue-700">
                    Ingat saya
                </label>

                <button type="submit" class="h-12 w-full rounded-md bg-blue-700 text-sm font-bold text-white shadow-sm transition-colors hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                    Masuk
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-500">
                <a href="{{ url('/') }}" class="font-semibold text-blue-700 hover:text-blue-900">&larr; Kembali ke situs</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
