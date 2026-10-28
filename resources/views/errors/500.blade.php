<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Terjadi Kesalahan - CV Elka Mandiri</title>
    @vite('resources/css/app.css')
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-blue-100 selection:text-blue-900">
    {{-- Halaman ini sengaja tidak memakai layout situs: layout membaca database (logo, kontak, footer),
         padahal error 500 sering justru disebabkan masalah database. --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center">
                <a href="{{ url('/') }}"><img src="{{ asset('images/logo.png') }}" alt="Logo Elka Mandiri" class="h-8 w-auto"></a>
            </div>
        </div>
    </nav>

    <main class="min-h-[70vh]">
        <x-error-page
            code="500"
            eyebrow="500 Server Error"
            title="Terjadi Kesalahan pada Server"
            message="Maaf, ada sesuatu yang tidak berjalan semestinya di sisi kami. Silakan coba lagi beberapa saat lagi. Bila masalah berlanjut, mohon hubungi kami.">
            <a href="javascript:location.reload()" class="inline-flex h-12 items-center justify-center bg-blue-800 px-10 text-sm font-bold text-white uppercase tracking-widest ring-1 ring-blue-800 hover:bg-transparent hover:text-blue-800 transition-all duration-300 ease-in-out">
                Coba Lagi
            </a>
            <a href="{{ url('/') }}" class="inline-flex h-12 items-center justify-center px-10 text-sm font-bold text-blue-800 uppercase tracking-widest ring-1 ring-blue-800 hover:bg-blue-800 hover:text-white transition-all duration-300 ease-in-out">
                Ke Beranda
            </a>
        </x-error-page>
    </main>

    <footer class="bg-slate-900 py-6">
        <p class="text-center text-xs text-slate-500">&copy; 2026 CV Elka Mandiri. Hak cipta dilindungi undang-undang.</p>
    </footer>
</body>
</html>
