@props([
    'title',
    'desc',
    'image',
    'url',
    'imagePosition' => 'left'  // Secara bawaan gambar di kiri, bisa diubah ke 'right'
])

@php $left = $imagePosition == 'left'; @endphp

<!-- CARD MEMANJANG: teks dan panel gambar berdampingan -->
<div class="group bg-white rounded-1x1 border border-slate-200 shadow-sm p-4 sm:p-6 lg:p-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">

        <!-- TEKS -->
        <div class="flex flex-col px-2 sm:px-0 lg:px-4 lg:py-2 {{ $left ? 'lg:order-2' : 'lg:order-1' }}">
            <h2 class="text-2xl md:text-4xl font-bold text-slate-900 tracking-tight mb-3 md:mb-4">
                {{ $title }}
            </h2>
            <p class="text-base md:text-lg text-slate-500 leading-relaxed mb-8 line-clamp-4" title="{{ $desc }}">
                {{ $desc }}
            </p>
            <!-- mt-auto: tombol dan garis selalu menempel di bagian bawah kartu -->
            <div class="flex items-center mb-6 mt-auto">
                <a href="{{ $url }}" class="inline-flex items-center justify-center h-10 px-6 text-sm font-bold text-blue-800 ring-1 ring-blue-800 hover:bg-blue-800 hover:text-white transition duration-200 uppercase tracking-wide">
                    Detail Layanan
                </a>
            </div>
            <div class="w-12 h-[2px] bg-slate-300 group-hover:w-full group-hover:bg-blue-800 transition-all duration-500 ease-out"></div>
        </div>

        <!-- GAMBAR -->
        <div class="{{ $left ? 'lg:order-1' : 'lg:order-2' }}">
            <div class="w-full aspect-[16/10] bg-slate-200 overflow-hidden">
                <img data-zoomable loading="lazy" src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
            </div>
        </div>

    </div>
</div>
