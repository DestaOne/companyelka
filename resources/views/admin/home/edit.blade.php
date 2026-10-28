@extends('admin.layouts.app')

@section('title', 'Edit Kotak '.$card->slot)

@section('content')
@php
    $input = 'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1';
    $ok = 'border-slate-300 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $label = 'mb-2 block text-sm font-medium text-slate-950';
    $selected = old('service_id', $card->service_id);
@endphp

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.home.update', $card->slot) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            <div>
                <label for="eyebrow" class="{{ $label }}">Judul Kecil (biru) <span class="text-red-600">*</span></label>
                <input id="eyebrow" type="text" name="eyebrow" value="{{ old('eyebrow', $card->eyebrow) }}" required maxlength="60"
                       class="{{ $input }} {{ $errors->has('eyebrow') ? $bad : $ok }}">
                @error('eyebrow') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                @unless($card->isCta())
                    <p class="mt-2 text-xs text-slate-500">Nomor urut ("{{ sprintf('%02d', $card->slot) }} /") ditambahkan otomatis di depan teks ini.</p>
                @endunless
            </div>

            <div>
                <label for="title" class="{{ $label }}">Judul <span class="text-red-600">*</span></label>
                <input id="title" type="text" name="title" value="{{ old('title', $card->title) }}" required maxlength="80"
                       class="{{ $input }} {{ $errors->has('title') ? $bad : $ok }}">
                @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="{{ $label }}">Deskripsi @unless($card->isCta())<span class="text-red-600">*</span>@endunless</label>
                <textarea id="description" name="description" rows="3" maxlength="250" @unless($card->isCta()) required @endunless
                          class="{{ $input }} {{ $errors->has('description') ? $bad : $ok }}">{{ old('description', $card->description) }}</textarea>
                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                @if($card->isCta())
                    <p class="mt-2 text-xs text-slate-500">Opsional. Kotak biru ini awalnya tanpa deskripsi.</p>
                @endif
            </div>

            <div>
                <label for="service_id" class="{{ $label }}">Kotak Ini Mengarah Ke</label>
                <select id="service_id" name="service_id" class="{{ $input }} {{ $errors->has('service_id') ? $bad : $ok }}">
                    <option value="">Semua layanan (halaman katalog)</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" @selected((string) $selected === (string) $service->id)>
                            {{ $service->title }}{{ $service->status === \App\Models\Service::STATUS_PUBLISHED ? '' : ' (draft)' }}
                        </option>
                    @endforeach
                </select>
                @error('service_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-2 text-xs text-slate-500">Pilih dari layanan yang sudah ada. Layanan berstatus draft belum bisa dibuka pengunjung, jadi kotak akan mengarah ke halaman semua layanan sampai layanan itu diterbitkan.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ route('admin.home.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
