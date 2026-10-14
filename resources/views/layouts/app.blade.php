<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Elka Mandiri</title>
    @vite('resources/css/app.css')
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-blue-100 selection:text-blue-900">

    

 <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <div class="flex-shrink-0 flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Elka Mandiri" class="h-8 w-auto">
                </div>

                <!-- Menu Desktop (Murni efek Hover seperti semula) -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="relative text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors duration-200 after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-[2px] after:bg-blue-800 after:transition-all after:duration-300 hover:after:w-full">
                        Beranda
                    </a>
                    <a href="{{ url('/layanan') }}" class="relative text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors duration-200 after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-[2px] after:bg-blue-800 after:transition-all after:duration-300 hover:after:w-full">
                        Layanan
                    </a>
                    <a href="{{ url('/tentang-kami') }}" class="relative text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors duration-200 after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-[2px] after:bg-blue-800 after:transition-all after:duration-300 hover:after:w-full">
                        Tentang Kami
                    </a>
                    <a href="{{ url('/galeri') }}" class="relative text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors duration-200 after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-0 after:h-[2px] after:bg-blue-800 after:transition-all after:duration-300 hover:after:w-full">
                        Galeri
                    </a>
                    
                    <a href="{{ url('/tentang-kami#kontak') }}" class="relative inline-flex h-9 items-center justify-center rounded-none bg-blue-800 px-5 text-sm font-medium text-white hover:bg-blue-900 transition duration-200 ease-in-out">
                        Hubungi Kami
                    </a>
                </div>

                <!-- Tombol Hamburger (Mobile) -->
                <div class="flex items-center md:hidden">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-800 transition-colors">
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <div class="hidden md:hidden bg-white border-t border-slate-200 absolute w-full shadow-lg" id="mobile-menu">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="{{ url('/') }}" class="block px-3 py-3 text-base font-medium transition-all {{ request()->is('/') ? 'text-slate-900 bg-slate-50 border-l-2 border-blue-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-l-2 hover:border-blue-800' }}">
                    Beranda
                </a>
                <a href="{{ url('/layanan') }}" class="block px-3 py-3 text-base font-medium transition-all {{ request()->is('layanan') ? 'text-slate-900 bg-slate-50 border-l-2 border-blue-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-l-2 hover:border-blue-800' }}">
                    Layanan
                </a>
                <a href="{{ url('/tentang-kami') }}" class="block px-3 py-3 text-base font-medium transition-all {{ request()->is('tentang-kami') ? 'text-slate-900 bg-slate-50 border-l-2 border-blue-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-l-2 hover:border-blue-800' }}">
                    Tentang Kami
                </a>
                <a href="{{ url('/galeri') }}" class="block px-3 py-3 text-base font-medium transition-all {{ request()->is('galeri') ? 'text-slate-900 bg-slate-50 border-l-2 border-blue-800' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 hover:border-l-2 hover:border-blue-800' }}">
                    Galeri
                </a>

                <div class="pt-4">
                    <a href="{{ url('/tentang-kami#kontak') }}" class="flex w-full items-center justify-center h-12 bg-blue-800 text-white text-base font-medium hover:bg-blue-900 transition duration-200">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-white pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
                <div class="md:col-span-4">
                    <img src="{{ asset('images/elkamandiri.jpeg') }}" alt="Logo Elka Mandiri" class="h-8 w-auto mb-6">
                    <p class="text-sm text-slate-400 leading-relaxed mb-6">
                        Solusi terpercaya untuk kebutuhan mesin kantor, komputer, perangkat lunak, dan furnitur.
                    </p>
                </div>
                
                <div class="md:col-span-4">
                    <h3 class="text-xs font-semibold text-white tracking-wider uppercase mb-4">Kontak Kami</h3>
                    <ul class="space-y-3">
                        <li>
                            <a class="text-sm text-slate-400 hover:text-white transition-colors duration-200 block leading-relaxed">
                                Jalan Tukad Batanghari Blok B Nomor 1, Kelurahan Panjer Kec, Denpasar Selatan, Kota Denpasar Provinsi Bali
                            </a>
                        </li>
                        <li>
                            <a href="mailto:elkamandiri.cv@gmail.com" class="text-sm text-slate-400 hover:text-white transition-colors duration-200 block">
                                Email: elkamandiri.cv@gmail.com
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/628113383679?text=halo%20elkamandiri,%20saya%20ingin%20berkonsultasi%20terkaitan%20layanan%20yang%20disediakan" class="text-sm text-slate-400 hover:text-white transition-colors duration-200 block">
                                Telepon: 08113383679
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="md:col-span-4">
                    <h3 class="text-xs font-semibold text-white tracking-wider uppercase mb-4">Layanan</h3>
                    <ul class="space-y-3">
                        @foreach($footerServices as $footerService)
                        <li><a href="{{ route('layanan.show', $footerService) }}" class="text-sm text-slate-400 hover:text-white transition-colors duration-200">{{ $footerService->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <div class="mt-16 border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-xs text-slate-500">
                    &copy; 2026 CV Elka Mandiri. Hak cipta dilindungi undang-undang.
                </p>
            </div>
        </div>
    </footer>


    <x-image-lightbox />

</body>
</html>