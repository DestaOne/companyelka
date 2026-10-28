@extends('layouts.app')

@section('content')
<div class="bg-white py-12 md:py-16 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-12">
            <div class="inline-flex items-center space-x-3 mb-4">
                <span class="w-6 h-[2px] bg-blue-800"></span>
                <span class="text-xs font-bold uppercase tracking-widest text-blue-800">Galeri</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-4">Dokumentasi Proyek & Kegiatan</h1>
            <p class="text-lg text-slate-500 max-w-2xl">Potret pekerjaan, pengadaan, dan kegiatan kami bersama para mitra korporat di seluruh Bali.</p>
        </div>

        <x-bento-gallery :images="$images" />

    </div>
</div>

<x-cta-banner slug="galeri" />
@endsection
