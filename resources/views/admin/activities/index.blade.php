@extends('admin.layouts.app')

@section('title', 'Aktivitas')

@section('content')
<p class="mb-4 text-sm text-slate-500">Riwayat seluruh aktivitas admin, dari yang terbaru. Waktu ditampilkan dalam {{ \App\Models\Activity::TIMEZONE_LABEL }}.</p>

<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
    @if($activities->isEmpty())
        <p class="px-6 py-12 text-center text-sm text-slate-500">Belum ada aktivitas.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-widest text-slate-500">
                    <tr>
                        <th scope="col" class="whitespace-nowrap px-6 py-3">Tanggal &amp; Jam</th>
                        <th scope="col" class="whitespace-nowrap px-6 py-3">Admin</th>
                        <th scope="col" class="px-6 py-3">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($activities as $activity)
                        @php $time = $activity->created_at->copy()->setTimezone(\App\Models\Activity::TIMEZONE)->locale('id'); @endphp
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-600">
                                <span class="block font-medium text-slate-900">{{ $time->translatedFormat('d F Y') }}</span>
                                <span class="block text-xs">{{ $time->format('H:i:s') }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="flex items-center gap-x-3">
                                    @if($activity->isVisitorMessage())
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-semibold text-amber-700">?</span>
                                        <span class="font-medium text-slate-900">Pengunjung</span>
                                    @else
                                        <x-avatar :user="$activity->user" size="size-8" text="text-xs" />
                                        <span class="font-medium text-slate-900">{{ $activity->user?->name ?? 'Admin (dihapus)' }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">@if($activity->isVisitorMessage())<a href="{{ route('admin.messages.show', $activity->contact_message_id) }}" class="text-blue-700 hover:underline">{{ $activity->description }}</a>@else{{ ucfirst($activity->description) }}@endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@if($activities->hasPages())
    <nav class="mt-6 flex items-center justify-between gap-x-4 text-sm" aria-label="Halaman">
        @if($activities->onFirstPage())
            <span class="rounded-lg border border-slate-200 px-4 py-2 font-medium text-slate-400">Sebelumnya</span>
        @else
            <a href="{{ $activities->previousPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-100">Sebelumnya</a>
        @endif

        <span class="text-slate-500">Halaman {{ $activities->currentPage() }} dari {{ $activities->lastPage() }}</span>

        @if($activities->hasMorePages())
            <a href="{{ $activities->nextPageUrl() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-100">Berikutnya</a>
        @else
            <span class="rounded-lg border border-slate-200 px-4 py-2 font-medium text-slate-400">Berikutnya</span>
        @endif
    </nav>
@endif
@endsection
