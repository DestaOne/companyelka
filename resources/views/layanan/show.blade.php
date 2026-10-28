@extends('layouts.app')

@section('content')
<!-- Header Detail -->
<div class="bg-slate-50 py-16 md:py-24 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-blue-800 transition-colors duration-200 mb-8">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Katalog
        </a>
        <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight mb-6">{{ $service->title }}</h1>
        <p class="text-lg md:text-xl text-slate-600 max-w-3xl font-medium leading-relaxed">
            {{ $service->summary }}
        </p>
    </div>
</div>

<!-- Konten Detail -->
<div class="bg-white py-16 md:py-24 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">

            <div class="lg:col-span-8 relative w-full aspect-video bg-slate-200 ring-1 ring-slate-200 overflow-hidden">
                <img data-zoomable src="{{ $service->image_url }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
            </div>

            <div class="lg:col-span-4">
                <x-service-detail :blocks="$service->detailBlocks()" />
            </div>

        </div>
    </div>
</div>

<x-cta-banner slug="layanan-detail" />
@endsection
