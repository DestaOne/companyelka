@extends('admin.layouts.app')

@section('title', 'Galeri')

@section('actions')
    <a href="{{ route('admin.gallery.create') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Gambar
    </a>
@endsection

@section('content')
@if($images->isEmpty())
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <p class="text-sm font-medium text-white">Belum ada gambar</p>
        <p class="mt-1 text-sm text-slate-500">Klik "Tambah Gambar" untuk mengunggah dokumentasi pertama.</p>
    </div>
@else
    <p class="mb-4 text-sm text-slate-500">{{ $images->count() }} gambar &middot; urutan mengikuti waktu unggah dan menentukan posisi pada bento grid di halaman Galeri.</p>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @foreach($images as $image)
            <div class="group rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 overflow-hidden">
                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                    <img src="{{ $image->thumbnail_url }}" alt="Gambar galeri {{ $loop->iteration }}" loading="lazy" class="h-full w-full object-cover">
                    <span class="absolute left-2 top-2 bg-slate-950/70 px-2 py-0.5 text-xs font-semibold text-white">{{ $loop->iteration }}</span>
                </div>
                <div class="p-2">
                    <form method="POST" action="{{ route('admin.gallery.update', $image) }}" class="mb-2 flex gap-x-1">
                        @csrf
                        @method('PUT')
                        <input type="text" name="title" value="{{ old('title', $image->title) }}" maxlength="100" placeholder="Judul" aria-label="Judul gambar {{ $loop->iteration }}"
                               class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-700">
                        <button type="submit" class="shrink-0 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100">Simpan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}"
                          data-confirm-title="Hapus gambar?" data-confirm-message="Gambar ini akan dihapus dari galeri beserta berkasnya. Tindakan ini tidak dapat dibatalkan.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex w-full items-center justify-center gap-x-1.5 rounded-lg px-3 py-2 text-sm font-medium text-red-600 border border-red-200 transition-colors hover:bg-red-50">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="mt-12">
    <x-admin-banner type="cta" slug="galeri" />
</div>
@endsection
