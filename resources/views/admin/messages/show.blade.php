@extends('admin.layouts.app')

@section('title', 'Detail Pesan')

@php
    $nav = 'inline-flex size-10 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition-colors hover:bg-slate-100';
    $navOff = 'inline-flex size-10 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300';
@endphp

@section('actions')
    <div class="flex items-center gap-x-2">
        @if($older)
            <a href="{{ route('admin.messages.show', $older) }}" class="{{ $nav }}" aria-label="Pesan sebelumnya" title="Pesan sebelumnya (lebih lama)">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </a>
        @else
            <span class="{{ $navOff }}" aria-hidden="true"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg></span>
        @endif
        @if($newer)
            <a href="{{ route('admin.messages.show', $newer) }}" class="{{ $nav }}" aria-label="Pesan berikutnya" title="Pesan berikutnya (lebih baru)">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </a>
        @else
            <span class="{{ $navOff }}" aria-hidden="true"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></span>
        @endif
        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-x-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
            Semua pesan
        </a>
    </div>
@endsection

@section('content')
@php
    $time = $message->created_at->copy()->setTimezone(\App\Models\Activity::TIMEZONE)->locale('id');
    $label = 'text-xs font-bold uppercase tracking-widest text-slate-500';
    $row = 'group flex items-start gap-x-3 rounded-xl px-3 py-3 transition-colors hover:bg-slate-50';
    $icon = 'flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700';
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">

    {{-- Pesan --}}
    <article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 lg:col-span-2" aria-labelledby="message-subject">
        <header class="flex items-center gap-x-4 border-b border-slate-100 bg-slate-50/70 px-6 py-5 sm:px-8">
            <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-700 to-blue-800 text-lg font-semibold text-white" aria-hidden="true">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-base font-semibold text-slate-950">{{ $message->name }}</p>
                <p class="text-sm text-slate-500">
                    {{ $time->translatedFormat('d F Y, H:i') }} {{ \App\Models\Activity::TIMEZONE_LABEL }}
                    <span class="text-slate-300" aria-hidden="true">&middot;</span>
                    {{ $message->created_at->locale('id')->diffForHumans() }}
                </p>
            </div>
            @if($wasUnread)
                <span class="shrink-0 rounded-full bg-blue-700 px-3 py-1 text-xs font-semibold text-white">Baru</span>
            @else
                <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Sudah dibaca</span>
            @endif
        </header>

        <div class="px-6 py-6 sm:px-8 sm:py-8">
            <p class="{{ $label }}">Subjek</p>
            <h2 id="message-subject" class="mt-1 break-words text-2xl font-bold tracking-tight text-slate-950">{{ $message->subject }}</h2>

            <p class="{{ $label }} mt-8">Isi pesan</p>
            {{-- whitespace-pre-line mempertahankan baris baru dari pengirim; isinya tetap di-escape. --}}
            <div class="mt-2 whitespace-pre-line break-words rounded-xl bg-slate-50 p-5 text-base leading-relaxed text-slate-800 ring-1 ring-slate-200/70">{{ $message->body }}</div>
        </div>
    </article>

    {{-- Pengirim dan tindakan --}}
    <aside class="space-y-6 lg:sticky lg:top-28">
        <section class="rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200/70" aria-labelledby="sender-title">
            <h3 id="sender-title" class="{{ $label }} px-3 pb-1 pt-3">Kontak pengirim</h3>

            <a href="tel:{{ preg_replace('/[^\d+]/', '', $message->phone) }}" class="{{ $row }}">
                <span class="{{ $icon }}"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg></span>
                <span class="min-w-0">
                    <span class="block text-xs text-slate-500">Telepon / WhatsApp</span>
                    <span class="block break-words text-sm font-medium text-slate-900 group-hover:text-blue-700">{{ $message->phone }}</span>
                </span>
            </a>

            <a href="mailto:{{ $message->email }}" class="{{ $row }}">
                <span class="{{ $icon }}"><svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg></span>
                <span class="min-w-0">
                    <span class="block text-xs text-slate-500">Email</span>
                    <span class="block break-all text-sm font-medium text-slate-900 group-hover:text-blue-700">{{ $message->email }}</span>
                </span>
            </a>
        </section>

        <section class="space-y-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70" aria-labelledby="reply-title">
            <h3 id="reply-title" class="{{ $label }}">Balas pesan</h3>

            <a href="{{ $message->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="flex w-full items-center justify-center gap-x-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                Balas via WhatsApp
            </a>
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="flex w-full items-center justify-center gap-x-2 rounded-lg border border-blue-700 px-4 py-3 text-sm font-semibold text-blue-700 transition-colors hover:bg-blue-50">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/></svg>
                Balas via Email
            </a>
        </section>

        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Hapus pesan dari {{ e($message->name) }}? Tindakan ini tidak bisa dibatalkan.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="flex w-full items-center justify-center gap-x-2 rounded-lg px-4 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                Hapus pesan
            </button>
        </form>
    </aside>
</div>
@endsection
