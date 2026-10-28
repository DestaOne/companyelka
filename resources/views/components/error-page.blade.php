@props([
    'code',     // Angka besar di kanan, mis. "404"
    'eyebrow',  // Teks kecil di atas judul, mis. "404 Page Not Found"
    'title',
    'message',
])

<div class="bg-white py-16 md:py-24 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            <div class="flex flex-col order-2 lg:order-1">

                <div class="inline-flex items-center space-x-4 mb-6">
                    <span class="text-sm font-bold uppercase tracking-widest text-blue-800">{{ $eyebrow }}</span>
                    <span class="w-16 h-[2px] bg-blue-800"></span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                    {{ $title }}
                </h1>

                <p class="text-lg md:text-xl text-slate-500 font-medium mb-10 leading-relaxed max-w-lg">
                    {{ $message }}
                </p>

                <div class="flex flex-col gap-3 sm:flex-row">
                    @if($slot->isEmpty())
                        <a href="{{ url('/') }}" class="inline-flex h-12 items-center justify-center bg-blue-800 px-10 text-sm font-bold text-white uppercase tracking-widest ring-1 ring-blue-800 hover:bg-transparent hover:text-blue-800 transition-all duration-300 ease-in-out">
                            Kembali ke Beranda
                        </a>
                    @else
                        {{ $slot }}
                    @endif
                </div>
            </div>

            <div class="flex justify-center lg:justify-end order-1 lg:order-2">

                <span class="font-black text-slate-900 tracking-tighter leading-none select-none" style="font-size: clamp(120px, 16vw, 250px);">
                    {{ $code }}
                </span>
            </div>

        </div>
    </div>
</div>
