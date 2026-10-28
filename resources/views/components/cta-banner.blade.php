@props([
    'slug', // Tempat pemakaian, lihat Banner::DEFINITIONS['cta']
])

@php
    $banner = \App\Models\Banner::for(\App\Models\Banner::CTA, $slug);
    $definition = $banner->definition;
@endphp

<div class="relative overflow-hidden bg-blue-900 py-16 md:py-20">
    <!-- Gambar latar -->
    <img src="{{ $banner->image_url }}" alt="" class="absolute inset-0 w-full h-full object-cover object-center">
    <!-- Gradasi biru dari kiri ke kanan, memudar -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900 via-blue-900/85 to-blue-900/10"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @foreach($banner->blocks() as $block)
            @if($block['type'] === 'heading')
                <{{ $loop->first ? 'h2' : 'h3' }} class="text-2xl md:text-3xl lg:text-4xl font-bold text-white tracking-tight mb-4 md:mb-6">
                    {{ $block['text'] }}
                </{{ $loop->first ? 'h2' : 'h3' }}>
            @else
                <p class="text-blue-200 text-base md:text-lg lg:text-xl max-w-2xl mx-auto mb-8 md:mb-10">
                    {{ $block['text'] }}
                </p>
            @endif
        @endforeach

        <a href="{{ url($definition['button_url']) }}" class="inline-flex h-12 items-center justify-center bg-white px-10 text-base font-bold text-blue-900 hover:bg-slate-50 transition duration-200 ring-1 ring-white uppercase tracking-wide">
            {{ $banner->button_text }}
        </a>
    </div>
</div>
