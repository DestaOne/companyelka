@extends('admin.layouts.app')

@section('title', $logo->definition['label'])

@section('content')
@php
    $ok = 'border-slate-300 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $limit = \App\Support\UploadLimit::label(\App\Support\UploadLimit::kilobytes(\App\Http\Requests\Admin\LogoRequest::IMAGE_MAX_KB));
@endphp

<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.logo.update', $logo->key) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            <div>
                <span class="mb-2 block text-sm font-medium text-slate-950">Logo saat ini</span>
                <div class="flex h-32 items-center justify-center rounded-lg px-4 {{ $logo->definition['dark'] ? 'bg-slate-900' : 'bg-slate-100' }}">
                    <img src="{{ $logo->image_url }}" alt="Logo saat ini" class="max-h-16 w-auto max-w-full">
                </div>
            </div>

            <div>
                <label for="image" class="mb-2 block text-sm font-medium text-slate-950">Logo Baru <span class="text-red-600">*</span></label>
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" required
                       class="block w-full rounded-lg border bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200 {{ $errors->has('image') ? $bad : $ok }}">
                @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-2 text-xs text-slate-500">JPG, PNG, atau WebP, lebar minimal 200 piksel, maksimal {{ $limit }}. PNG transparan paling cocok untuk logo.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ route('admin.logo.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Simpan Logo</button>
        </div>
    </form>
</div>
@endsection
