@extends('admin.layouts.app')

@section('title', 'Pesan Masuk')

@section('content')
@php
    $tab = 'rounded-lg px-3 py-2 text-sm font-medium transition-colors';
@endphp

<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">Pesan yang dikirim pengunjung lewat formulir di halaman Tentang Kami. Waktu ditampilkan dalam {{ \App\Models\Activity::TIMEZONE_LABEL }}.</p>

    <div class="flex items-center gap-x-1">
        <a href="{{ route('admin.messages.index') }}" class="{{ $tab }} {{ $filter === 'semua' ? 'bg-blue-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Semua</a>
        <a href="{{ route('admin.messages.index', ['filter' => 'belum-dibaca']) }}" class="{{ $tab }} {{ $filter === 'belum-dibaca' ? 'bg-blue-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Belum dibaca{{ $unreadCount > 0 ? " ({$unreadCount})" : '' }}
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
    @if($messages->isEmpty())
        <p class="px-6 py-12 text-center text-sm text-slate-500">{{ $filter === 'belum-dibaca' ? 'Tidak ada pesan yang belum dibaca.' : 'Belum ada pesan masuk.' }}</p>
    @else
        <ul class="divide-y divide-slate-100">
            @foreach($messages as $message)
                @php $time = $message->created_at->copy()->setTimezone(\App\Models\Activity::TIMEZONE)->locale('id'); @endphp
                <li class="flex items-start gap-x-2 {{ $message->isRead() ? '' : 'bg-blue-50/60' }}">
                    <a href="{{ route('admin.messages.show', $message) }}" class="flex min-w-0 flex-1 items-start gap-x-4 px-4 py-4 hover:bg-slate-50 sm:px-6">
                        <span class="mt-2 size-2.5 shrink-0 rounded-full {{ $message->isRead() ? 'bg-transparent' : 'bg-blue-700' }}" @unless($message->isRead()) title="Belum dibaca" @endunless></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline justify-between gap-x-4">
                                <p class="truncate text-sm {{ $message->isRead() ? 'font-medium text-slate-700' : 'font-bold text-slate-950' }}">{{ $message->name }}</p>
                                <p class="shrink-0 text-xs text-slate-500">{{ $time->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <p class="mt-0.5 truncate text-sm {{ $message->isRead() ? 'text-slate-700' : 'font-semibold text-slate-900' }}">{{ $message->subject }}</p>
                            <p class="mt-0.5 truncate text-sm text-slate-500">{{ $message->body }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Hapus pesan ini?')" class="py-3 pr-3 sm:pr-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600" aria-label="Hapus pesan dari {{ $message->name }}">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>

@if($messages->hasPages())
    <nav class="mt-6 flex items-center justify-between gap-x-4 text-sm" aria-label="Halaman">
        @if($messages->onFirstPage())
            <span class="rounded-lg border border-slate-200 px-4 py-2 font-medium text-slate-400">Sebelumnya</span>
        @else
            <a href="{{ $messages->previousPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-100">Sebelumnya</a>
        @endif

        <span class="text-slate-500">Halaman {{ $messages->currentPage() }} dari {{ $messages->lastPage() }}</span>

        @if($messages->hasMorePages())
            <a href="{{ $messages->nextPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-100">Berikutnya</a>
        @else
            <span class="rounded-lg border border-slate-200 px-4 py-2 font-medium text-slate-400">Berikutnya</span>
        @endif
    </nav>
@endif
@endsection
