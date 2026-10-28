@extends('layouts.app')

@section('content')
<x-hero-banner slug="home" />

<div id="layanan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
    <div class="flex flex-col md:flex-row justify-between items-end mb-12">
        <div class="max-w-2xl">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">Layanan Kami</h2>
            <p class="mt-4 text-lg text-slate-500">Beragam pilihan produk berkualitas untuk memenuhi segala kebutuhan operasional dan tata ruang perusahaan Anda.</p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 border-t border-l border-slate-300 bg-white">
        @foreach($cards as $card)
            @if($card->isCta())
            <!-- Kotak terakhir: ajakan ke katalog layanan -->
            <a href="{{ $card->url() }}" class="border-b border-r border-slate-300 p-12 bg-blue-800 hover:bg-blue-700 transition-colors duration-300 group cursor-pointer relative flex flex-col justify-between min-h-[350px]">
                <div>
                    <span class="text-blue-200 text-sm font-semibold uppercase tracking-wider mb-8 block">{{ $card->eyebrowLabel() }}</span>
                    <h3 class="text-4xl font-semibold text-white mb-4 tracking-tight leading-tight">{{ $card->title }}</h3>
                    @if($card->description)
                    <p class="text-blue-100 text-lg font-light leading-relaxed max-w-sm">{{ $card->description }}</p>
                    @endif
                </div>
                <!-- Panah yang meluncur saat di-hover -->
                <div class="absolute bottom-10 right-10 text-white transform group-hover:translate-x-4 transition-transform duration-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </a>
            @else
            <a href="{{ $card->url() }}" class="block border-b border-r border-slate-300 p-12 hover:bg-slate-50 transition-colors duration-300 group cursor-pointer relative">
                <span class="text-blue-600 text-sm font-semibold uppercase tracking-wider mb-8 block">{{ $card->eyebrowLabel() }}</span>
                <h3 class="text-3xl font-semibold text-slate-900 mb-4 tracking-tight">{{ $card->title }}</h3>
                <p class="text-slate-600 text-lg font-light leading-relaxed mb-8 max-w-sm">{{ $card->description }}</p>
                <div class="absolute bottom-10 right-10 text-blue-600 transform group-hover:translate-x-2 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </a>
            @endif
        @endforeach
        </div>
    </div>

<div class="bg-white border-t border-slate-200 py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-widest uppercase mb-4 font-mon">{{ $featureSection->title }}</h2>
            <p class="text-lg text-slate-500 max-w-2xl font-medium">{{ $featureSection->description }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($features as $feature)
            <div class="group relative flex flex-col justify-end h-[450px] overflow-hidden ring-1 ring-slate-200 bg-white">

                <div class="absolute top-6 right-6 w-12 h-12 z-20 pointer-events-none">
                    <div class="absolute top-0 right-0 w-0 h-[3px] bg-blue-800 group-hover:w-full transition-all duration-300 ease-out"></div>
                    <div class="absolute top-0 right-0 w-[3px] h-0 bg-blue-800 group-hover:h-full transition-all duration-300 ease-out"></div>
                </div>

                <div class="absolute inset-0 w-full h-full">
                    <img src="{{ $feature->image_url }}" alt="{{ $feature->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-transparent"></div>

                <div class="relative z-10 p-8">
                    <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-blue-800 transition-colors uppercase tracking-wide">{{ $feature->title }}</h3>
                    <p class="text-slate-600 font-medium leading-relaxed">{{ $feature->description }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<x-cta-banner slug="home" />


@endsection