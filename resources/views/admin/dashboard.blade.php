@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php $card = 'rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 transition-shadow hover:shadow-md'; @endphp

{{-- Banner akses cepat: gaya sama dengan sidebar (gradasi biru dengan lingkaran dekoratif) --}}
<section class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-blue-800 via-blue-700 to-blue-900 p-6 shadow-xl shadow-blue-900/20 sm:p-8" aria-labelledby="quick-access-title">
    <div class="pointer-events-none absolute -left-16 -top-16 size-56 rounded-full bg-white/10" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-20 -right-10 size-64 rounded-full bg-white/10" aria-hidden="true"></div>
    <div class="pointer-events-none absolute right-1/4 top-6 size-3 rounded-full bg-blue-300" aria-hidden="true"></div>
    <div class="pointer-events-none absolute bottom-8 left-1/3 size-2.5 rounded-full bg-blue-200" aria-hidden="true"></div>

    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-200">Akses Cepat</p>
            <h2 id="quick-access-title" class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">Halo, {{ auth()->user()->name }}</h2>
            <p class="mt-2 text-sm text-blue-100">Mulai dari pekerjaan yang paling sering dilakukan.</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('admin.services.create') }}" class="inline-flex items-center justify-center gap-x-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-blue-800 shadow-sm transition-colors hover:bg-blue-50">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Layanan
            </a>
            <a href="{{ route('admin.gallery.create') }}" class="inline-flex items-center justify-center gap-x-2 rounded-xl bg-white/10 px-5 py-3 text-sm font-semibold text-white ring-1 ring-white/40 transition-colors hover:bg-white/20">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                Tambah Foto
            </a>
        </div>
    </div>
</section>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
    <div class="{{ $card }}">
        <div class="flex items-start justify-between gap-x-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Total Layanan</p>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">{{ $serviceCount }}</p>
            </div>
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-700 text-white">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
            </span>
        </div>
        <p class="mt-1 text-xs text-slate-500">{{ $publishedCount }} terbit &middot; {{ $draftCount }} draft</p>
    </div>
    <div class="{{ $card }}">
        <div class="flex items-start justify-between gap-x-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Total Gambar Galeri</p>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">{{ $galleryCount }}</p>
            </div>
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-700 text-white">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
            </span>
        </div>
        <p class="mt-1 text-xs text-slate-500">Gambar terunggah</p>
    </div>
    <div class="{{ $card }}">
        <div class="flex items-start justify-between gap-x-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Jumlah Admin</p>
                <p class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">{{ $adminCount }}</p>
            </div>
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-700 text-white">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
            </span>
        </div>
        <p class="mt-1 text-xs text-slate-500">Akun terdaftar</p>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Perlu perhatian: layanan yang masih draft --}}
    <section class="{{ $card }}" aria-labelledby="attention-title">
        <div class="flex items-center justify-between gap-x-4">
            <h2 id="attention-title" class="text-base font-bold text-slate-950">Perlu Perhatian</h2>
            @if($draftCount > 0)
                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">{{ $draftCount }} draft</span>
            @endif
        </div>
        <p class="mt-1 text-sm text-slate-500">Layanan yang masih berstatus draft dan belum tampil di situs.</p>

        @forelse($drafts as $draft)
            <a href="{{ route('admin.services.edit', $draft) }}" class="mt-4 flex items-center gap-x-3 rounded-xl p-3 ring-1 ring-slate-200 transition-colors hover:bg-slate-50">
                <img src="{{ $draft->image_url }}" alt="" class="size-12 shrink-0 rounded-lg bg-slate-100 object-cover">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-950">{{ $draft->title }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $draft->summary }}</p>
                </div>
                <span class="shrink-0 text-sm font-semibold text-blue-700">Edit</span>
            </a>
        @empty
            <div class="mt-4 flex items-center gap-x-3 rounded-xl bg-green-50 p-4 ring-1 ring-green-200">
                <svg class="size-5 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <p class="text-sm text-slate-800">Tidak ada layanan draft. Semua layanan sudah terbit.</p>
            </div>
        @endforelse

        @if($draftCount > $drafts->count())
            <a href="{{ route('admin.services.index') }}" class="mt-4 inline-block text-sm font-semibold text-blue-700 hover:text-blue-900">Dan {{ $draftCount - $drafts->count() }} layanan draft lainnya: lihat semua layanan</a>
        @endif
    </section>

    {{-- Aktivitas terbaru --}}
    <section class="{{ $card }}" aria-labelledby="activity-title">
        <div class="flex items-center justify-between gap-x-4">
            <h2 id="activity-title" class="text-base font-bold text-slate-950">Aktivitas Terbaru</h2>
            <a href="{{ route('admin.activities.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Lihat semua</a>
        </div>

        <ul class="mt-4 divide-y divide-slate-100">
            @forelse($recentActivities as $activity)
                <li class="flex gap-x-3 py-3">
                    @if($activity->isVisitorMessage())
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-semibold text-amber-700">?</span>
                    @else
                        <x-avatar :user="$activity->user" size="size-8" text="text-xs" />
                    @endif
                    <div class="min-w-0">
                        <p class="break-words text-sm text-slate-700">
                            @if($activity->isVisitorMessage())
                                <a href="{{ route('admin.messages.show', $activity->contact_message_id) }}" class="hover:underline">{{ $activity->description }}</a>
                            @else
                                <span class="font-semibold text-slate-950">{{ $activity->user?->name ?? 'Admin' }}</span> {{ $activity->description }}
                            @endif
                        </p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $activity->created_at->copy()->setTimezone(\App\Models\Activity::TIMEZONE)->locale('id')->translatedFormat('d M Y, H:i') }} {{ \App\Models\Activity::TIMEZONE_LABEL }}</p>
                    </div>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-slate-500">Belum ada aktivitas.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
