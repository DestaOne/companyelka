@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php $card = 'rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 transition-shadow hover:shadow-md'; @endphp

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
        <p class="mt-1 text-xs text-slate-500">Layanan terdaftar</p>
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
@endsection
