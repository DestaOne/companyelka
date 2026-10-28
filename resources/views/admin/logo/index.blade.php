@extends('admin.layouts.app')

@section('title', 'Logo')

@section('content')
<p class="mb-4 text-sm text-slate-500">Logo yang tampil di menu atas (navbar) dan bagian bawah (footer) situs. Pilih logo yang ingin diganti.</p>

<div class="divide-y divide-slate-200 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
    @foreach($logos as $logo)
        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:p-6">
            <div class="flex h-24 w-full shrink-0 items-center justify-center rounded-lg px-4 sm:h-20 sm:w-48 {{ $logo->definition['dark'] ? 'bg-slate-900' : 'bg-slate-100' }}">
                <img src="{{ $logo->image_url }}" alt="{{ $logo->definition['label'] }}" class="max-h-12 w-auto max-w-full">
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-700">{{ $logo->hasCustomImage() ? 'Logo kustom' : 'Logo bawaan' }}</p>
                <h2 class="mt-1 truncate text-base font-semibold text-slate-950">{{ $logo->definition['label'] }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $logo->definition['description'] }}</p>
            </div>

            <div class="flex shrink-0 items-center gap-x-2">
                @if($logo->hasCustomImage())
                    <form method="POST" action="{{ route('admin.logo.destroy', $logo->key) }}" onsubmit="return confirm('Kembalikan logo ke bawaan?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Kembalikan</button>
                    </form>
                @endif
                <a href="{{ route('admin.logo.edit', $logo->key) }}" class="inline-flex items-center gap-x-1.5 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                    Ganti
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
