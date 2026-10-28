@extends('admin.layouts.app')

@section('title', 'Tentang')

@section('actions')
    <a href="{{ url('/tentang-kami') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-x-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Lihat di situs</a>
@endsection

@section('content')
@php
    $card = 'group relative block bg-white p-6 shadow-sm ring-1 ring-slate-200/70 transition-shadow hover:shadow-md sm:p-8';
    $edit = 'absolute right-4 top-4 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 group-hover:bg-slate-200';
    $heading = 'mb-3 text-xs font-bold uppercase tracking-widest text-slate-500';
    $bg = $sections['background'];
    $principles = $sections['principles'];
    $vision = $sections['vision'];
    $mission = $sections['mission'];
    $contactText = $sections['contact'];
@endphp

<div class="mb-8">
    <x-admin-banner type="hero" slug="tentang" />
</div>

<p class="mb-6 text-sm text-slate-500">Pratinjau halaman Tentang Kami, urut dari atas ke bawah. Klik sebuah bagian untuk mengubah isinya.</p>

<div class="space-y-6">

    {{-- Latar Belakang --}}
    <a href="{{ route('admin.about.edit', 'background') }}" class="{{ $card }} rounded-2xl">
        <span class="{{ $edit }}">Edit</span>
        <p class="{{ $heading }}">Latar Belakang</p>
        <div class="grid grid-cols-1 items-center gap-6 md:grid-cols-2">
            <img src="{{ $bg->image_url }}" alt="" class="aspect-[4/3] w-full rounded-lg bg-slate-100 object-cover">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ $bg->eyebrow }}</p>
                <h2 class="mt-2 text-xl font-extrabold leading-snug text-slate-900">{{ $bg->title }}</h2>
                @foreach($bg->lines() as $paragraph)
                    <p class="mt-3 line-clamp-4 text-sm font-medium text-slate-600">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </a>

    {{-- Landasan & Tujuan: judul bagian, visi, misi --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
        <p class="{{ $heading }}">Landasan &amp; Tujuan</p>

        <a href="{{ route('admin.about.edit', 'principles') }}" class="group relative mb-6 block rounded-xl border border-slate-200 p-6 text-center transition-colors hover:bg-slate-50">
            <span class="{{ $edit }}">Edit</span>
            <h2 class="text-xl font-bold text-slate-900">{{ $principles->title }}</h2>
            <p class="mx-auto mt-2 max-w-xl text-sm font-medium text-slate-500">{{ $principles->body }}</p>
        </a>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <a href="{{ route('admin.about.edit', 'vision') }}" class="group relative block rounded-xl border border-slate-200 p-6 transition-colors hover:bg-slate-50">
                <span class="{{ $edit }}">Edit</span>
                <h3 class="text-lg font-semibold text-slate-900">{{ $vision->title }}</h3>
                <p class="mt-3 line-clamp-5 text-sm font-light leading-relaxed text-slate-600">{{ $vision->body }}</p>
            </a>
            <a href="{{ route('admin.about.edit', 'mission') }}" class="group relative block rounded-xl border border-slate-200 p-6 transition-colors hover:bg-slate-50">
                <span class="{{ $edit }}">Edit</span>
                <h3 class="text-lg font-semibold text-slate-900">{{ $mission->title }}</h3>
                <ul class="mt-3 space-y-2">
                    @foreach($mission->lines() as $item)
                        <li class="flex items-start text-sm font-light text-slate-600"><span class="mr-3 mt-1.5 size-2 shrink-0 bg-blue-800"></span><span class="line-clamp-2">{{ $item }}</span></li>
                    @endforeach
                </ul>
            </a>
        </div>
    </div>

    {{-- Kontak dan Lokasi --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
        <p class="{{ $heading }}">Kontak dan Lokasi</p>

        <a href="{{ route('admin.about.edit', 'contact') }}" class="group relative mb-6 block rounded-xl border border-slate-200 p-6 transition-colors hover:bg-slate-50">
            <span class="{{ $edit }}">Edit teks</span>
            <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ $contactText->eyebrow }}</p>
            <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $contactText->title }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ $contactText->body }}</p>
        </a>

        <a href="{{ route('admin.contact.edit') }}" class="group relative block rounded-xl border border-slate-200 p-6 transition-colors hover:bg-slate-50">
            <span class="{{ $edit }}">Edit kontak &amp; peta</span>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-xs font-bold uppercase tracking-widest text-slate-900">Alamat Kantor</dt><dd class="mt-1 text-slate-600">{{ $contact->address }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-widest text-slate-900">WhatsApp</dt><dd class="mt-1 text-slate-600">{{ $contact->whatsapp_display }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-widest text-slate-900">Email</dt><dd class="mt-1 text-slate-600">{{ $contact->email }}</dd></div>
            </dl>
            <p class="mt-4 text-xs text-slate-500">Alamat, nomor, email, dan link peta ini juga dipakai di footer seluruh situs.</p>
        </a>
    </div>
</div>
@endsection
