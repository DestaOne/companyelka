@extends('admin.layouts.app')

@section('title', 'Home')

@section('actions')
    <a href="{{ url('/') }}#layanan" target="_blank" rel="noopener" class="inline-flex items-center gap-x-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Lihat di situs</a>
@endsection

@section('content')
<div class="mb-12">
    <x-admin-banner type="hero" slug="home" />
</div>

<h2 class="text-lg font-bold text-slate-950">Layanan Kami</h2>
<p class="mb-4 mt-1 text-sm text-slate-500">Empat kotak layanan pada Beranda. Klik sebuah kotak untuk mengubah judul kecil, judul, deskripsi, dan tujuannya.</p>

<div class="grid grid-cols-1 border-l border-t border-slate-300 bg-white md:grid-cols-2">
    @foreach($cards as $card)
        <a href="{{ route('admin.home.edit', $card->slot) }}"
           class="group relative flex min-h-[260px] flex-col border-b border-r border-slate-300 p-8 transition-colors {{ $card->isCta() ? 'bg-blue-800 hover:bg-blue-700' : 'hover:bg-slate-50' }}">
            <span class="mb-6 block text-sm font-semibold uppercase tracking-wider {{ $card->isCta() ? 'text-blue-200' : 'text-blue-600' }}">{{ $card->eyebrowLabel() }}</span>
            <h2 class="mb-3 text-2xl font-semibold tracking-tight {{ $card->isCta() ? 'text-white' : 'text-slate-900' }}">{{ $card->title }}</h2>
            @if($card->description)
                <p class="mb-6 max-w-sm text-base font-light leading-relaxed text-slate-600">{{ $card->description }}</p>
            @endif

            <p class="mt-auto text-xs {{ $card->isCta() ? 'text-blue-100' : 'text-slate-500' }}">
                Mengarah ke:
                <span class="font-semibold">{{ $card->service?->title ?? 'Semua layanan' }}</span>
                @if($card->service && ! $card->service->isPublished())
                    <span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 font-semibold text-amber-800">Draft: di situs menuju semua layanan</span>
                @endif
            </p>

            <span class="absolute right-6 top-6 inline-flex items-center gap-x-1 rounded-lg px-2.5 py-1 text-xs font-semibold {{ $card->isCta() ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">Edit</span>
        </a>
    @endforeach
</div>

<h2 class="mt-12 text-lg font-bold text-slate-950">{{ $featureSection->title }}</h2>
<p class="mb-4 mt-1 text-sm text-slate-500">Bagian di bawah layanan, berisi judul bagian dan tiga kartu. Klik judul atau sebuah kartu untuk mengubahnya.</p>

<a href="{{ route('admin.home.features.section.edit') }}" class="group relative mb-6 block rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 transition-shadow hover:shadow-md sm:p-8">
    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Judul bagian</p>
    <p class="mt-2 text-2xl font-extrabold uppercase tracking-widest text-slate-900">{{ $featureSection->title }}</p>
    <p class="mt-3 max-w-2xl text-base font-medium text-slate-500">{{ $featureSection->description }}</p>
    <span class="absolute right-6 top-6 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 group-hover:bg-slate-200">Edit</span>
</a>

<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    @foreach($features as $feature)
        <a href="{{ route('admin.home.features.edit', $feature->slot) }}" class="group relative flex h-72 flex-col justify-end overflow-hidden bg-white ring-1 ring-slate-200 transition-shadow hover:shadow-md">
            <img src="{{ $feature->image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-white via-white/80 to-transparent"></div>
            <span class="absolute right-4 top-4 z-10 rounded-lg bg-white/90 px-2.5 py-1 text-xs font-semibold text-slate-700 group-hover:bg-white">Edit</span>
            <div class="relative z-10 p-6">
                <h3 class="mb-2 text-base font-bold uppercase tracking-wide text-slate-900">{{ $feature->title }}</h3>
                <p class="line-clamp-3 text-sm font-medium leading-relaxed text-slate-600">{{ $feature->description }}</p>
            </div>
        </a>
    @endforeach
</div>

<div class="mt-12">
    <x-admin-banner type="cta" slug="home" />
</div>
@endsection
