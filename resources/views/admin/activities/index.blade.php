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
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700"><svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg></span>
                                        <span class="font-medium text-slate-900">Pengunjung</span>
                                    @else
                                        <x-avatar :user="$activity->user" size="size-8" text="text-xs" />
                                        <span class="font-medium text-slate-900">{{ $activity->user?->name ?? 'Admin (dihapus)' }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">@if($activity->contact_message_id)<a href="{{ route('admin.messages.show', $activity->contact_message_id) }}" class="text-blue-700 hover:underline">{{ $activity->description }}</a>@elseif($activity->isVisitorMessage()){{ $activity->description }}@else{{ ucfirst($activity->description) }}@endif</td>
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
