@extends('admin.layouts.app')

@section('title', 'Layanan')

@section('actions')
  <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
    <form method="GET" action="{{ route('admin.services.index') }}" role="search" class="relative w-full sm:w-72">
        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        <input type="search" name="q" value="{{ $search }}" placeholder="Cari layanan..." aria-label="Cari layanan"
               class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-700">
    </form>
    <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Layanan
    </a>
  </div>
@endsection

@section('content')
<div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 overflow-hidden">
    @forelse($services as $service)
        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:p-6 {{ $loop->first ? '' : 'border-t border-slate-200' }}">
            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="h-24 w-full shrink-0 rounded-lg bg-slate-100 object-cover sm:h-20 sm:w-32">

            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-x-2">
                    <h2 class="truncate text-base font-semibold text-slate-950">{{ $service->title }}</h2>
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $service->isPublished() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $service->isPublished() ? 'Terbit' : 'Draft' }}</span>
                </div>
                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $service->summary }}</p>
                @if($service->isPublished())
                    <a href="{{ route('layanan.show', $service) }}" target="_blank" rel="noopener" class="mt-1 inline-block text-xs font-medium text-blue-700 hover:text-blue-900">
                        /layanan/{{ $service->slug }}
                    </a>
                @endif
            </div>

            <div class="flex shrink-0 items-center gap-x-2">
                <a href="{{ route('admin.services.edit', $service) }}" class="inline-flex items-center gap-x-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 border border-slate-300 transition-colors hover:bg-slate-100">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                      onsubmit="return confirm('Hapus layanan &quot;{{ addslashes($service->title) }}&quot;? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-x-1.5 rounded-lg px-3 py-2 text-sm font-medium text-red-600 border border-red-200 transition-colors hover:bg-red-50">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="p-12 text-center">
            @if($search !== '')
                <p class="text-sm font-medium text-white">Tidak ada layanan yang cocok dengan "{{ $search }}"</p>
                <a href="{{ route('admin.services.index') }}" class="mt-2 inline-block text-sm font-medium text-blue-700 hover:text-blue-900">Hapus pencarian</a>
            @else
                <p class="text-sm font-medium text-white">Belum ada layanan</p>
                <p class="mt-1 text-sm text-slate-500">Klik "Tambah Layanan" untuk membuat layanan pertama.</p>
            @endif
        </div>
    @endforelse
</div>
@endsection
