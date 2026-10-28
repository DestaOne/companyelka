@props([
    'slug', // Tempat pemakaian, lihat Banner::DEFINITIONS['hero']
])

@php
    $banner = \App\Models\Banner::for(\App\Models\Banner::HERO, $slug);
    $definition = $banner->definition;
@endphp

<div class="relative py-24 md:py-36 lg:py-45 flex items-center justify-center overflow-hidden">
    <!-- Gambar Latar Belakang -->
    <img src="{{ $banner->image_url }}" alt="{{ $banner->title() }}" class="absolute inset-0 w-full h-full object-cover object-center">

    <!-- Lapisan Gelap (Overlay) -->
    <div class="absolute inset-0 bg-slate-900/75"></div>

    <!-- Konten Teks -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Badge (hanya jika ada) -->
        @isset($definition['badge'])
        <div class="inline-flex items-center space-x-3 mb-4 md:mb-6">
            <span class="w-8 h-[2px] bg-blue-400"></span>
            <span class="text-xs font-bold uppercase tracking-widest text-blue-400">{{ $definition['badge'] }}</span>
            <span class="w-8 h-[2px] bg-blue-400"></span>
        </div>
        @endisset

        @foreach($banner->blocks() as $block)
            @if($block['type'] === 'heading')
                <{{ $loop->first ? 'h1' : 'h2' }} class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4 md:mb-6">
                    {{ $block['text'] }}
                </{{ $loop->first ? 'h1' : 'h2' }}>
            @else
                <p class="text-base md:text-lg lg:text-2xl text-slate-300 max-w-3xl mx-auto font-normal leading-relaxed mb-8 md:mb-10">
                    {{ $block['text'] }}
                </p>
            @endif
        @endforeach

        <!-- Tombol (hanya pada hero yang punya tombol) -->
        <div>
            @if($banner->hasButton() && filled($banner->button_text))
            <a href="{{ url($definition['button_url']) }}" class="inline-flex h-12 items-center justify-center bg-blue-800 px-10 text-sm font-bold text-white uppercase tracking-widest ring-1 ring-blue-800 hover:bg-transparent hover:text-white transition-all duration-300 ease-in-out">
                {{ $banner->button_text }}
            </a>
            @endif
        </div>

    </div>
</div>
