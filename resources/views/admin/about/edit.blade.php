@extends('admin.layouts.app')

@section('title', 'Edit '.$section->definition['label'])

@section('content')
@php
    $definition = $section->definition;
    $kind = $definition['body'];
    $input = 'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1';
    $ok = 'border-slate-300 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $label = 'mb-2 block text-sm font-medium text-slate-950';
@endphp

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.about.update', $section->key) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            @if(! empty($definition['image']))
                <div>
                    <label for="image" class="{{ $label }}">Gambar</label>
                    <img src="{{ $section->image_url }}" alt="Gambar saat ini" class="mb-3 h-48 w-full rounded-lg bg-slate-100 object-cover">
                    <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp"
                           class="block w-full rounded-lg border bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200 {{ $errors->has('image') ? $bad : $ok }}">
                    @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-slate-500">Opsional. JPG, PNG, atau WebP, lebar minimal 800 piksel, maksimal {{ \App\Support\UploadLimit::label(\App\Support\UploadLimit::kilobytes(\App\Http\Requests\Admin\AboutSectionRequest::IMAGE_MAX_KB)) }}. Kosongkan untuk mempertahankan gambar saat ini.</p>
                </div>
            @endif

            @if(! empty($definition['eyebrow']))
                <div>
                    <label for="eyebrow" class="{{ $label }}">Judul Kecil <span class="text-red-600">*</span></label>
                    <input id="eyebrow" type="text" name="eyebrow" value="{{ old('eyebrow', $section->eyebrow) }}" required maxlength="60"
                           class="{{ $input }} {{ $errors->has('eyebrow') ? $bad : $ok }}">
                    @error('eyebrow') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <label for="title" class="{{ $label }}">Judul <span class="text-red-600">*</span></label>
                <input id="title" type="text" name="title" value="{{ old('title', $section->title) }}" required maxlength="150"
                       class="{{ $input }} {{ $errors->has('title') ? $bad : $ok }}">
                @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="body" class="{{ $label }}">{{ $kind === 'list' ? 'Daftar Misi' : ($kind === 'paragraphs' ? 'Paragraf' : 'Teks') }} <span class="text-red-600">*</span></label>
                <textarea id="body" name="body" rows="{{ $kind === 'text' ? 4 : 8 }}" required
                          class="{{ $input }} {{ $errors->has('body') ? $bad : $ok }}">{{ old('body', $section->body) }}</textarea>
                @error('body') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                @if($kind === 'paragraphs')
                    <p class="mt-2 text-xs text-slate-500">Satu baris menjadi satu paragraf. Tekan Enter untuk memulai paragraf baru.</p>
                @elseif($kind === 'list')
                    <p class="mt-2 text-xs text-slate-500">Satu baris menjadi satu butir daftar (maksimal 8 butir). Tekan Enter untuk menambah butir baru.</p>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ route('admin.about.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
