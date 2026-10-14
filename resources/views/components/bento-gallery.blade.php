@props(['images'])

@php
    // Pola ukuran kotak (Bento Grid) berulang setiap 8 gambar
    $gridPattern = [
        'md:col-span-5 md:row-span-2', 
        'md:col-span-7 md:row-span-1', 
        'md:col-span-4 md:row-span-1', 
        'md:col-span-3 md:row-span-1', 
        'md:col-span-3 md:row-span-1', 
        'md:col-span-5 md:row-span-1', 
        'md:col-span-4 md:row-span-2', 
        'md:col-span-8 md:row-span-1', 
    ];
@endphp

@if($images->isEmpty())
    <div class="border border-slate-200 bg-slate-50 py-16 text-center text-slate-500">Belum ada dokumentasi yang ditampilkan.</div>
@else
<div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6 auto-rows-[250px] md:auto-rows-[280px]">
    @foreach($images as $index => $image)
        <div class="{{ $gridPattern[$index % 8] }} relative overflow-hidden group bg-slate-200 ring-1 ring-slate-200">
            <img data-zoomable data-full="{{ $image->image_url }}" loading="lazy" src="{{ $image->thumbnail_url }}" 
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" 
                 alt="Galeri Proyek {{ $index + 1 }}">

            <!-- Gradasi gelap muncul saat hover -->
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

            <!-- Aksen sudut biru -->
            <div class="pointer-events-none absolute top-4 left-4 w-8 h-8 border-t-[3px] border-l-[3px] border-blue-400 opacity-0 group-hover:opacity-100 group-hover:w-12 group-hover:h-12 transition-all duration-500 ease-out pointer-events-none"></div>

            <!-- Keterangan -->
            <div class="pointer-events-none absolute inset-x-0 bottom-0 p-5 md:p-6 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500 ease-out">

                <span class="block text-base md:text-lg font-semibold text-white tracking-tight">Proyek & Kegiatan</span>
            </div>
        </div>
    @endforeach
</div>
@endif
