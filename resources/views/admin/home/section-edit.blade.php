@extends('admin.layouts.app')

@section('title', 'Edit Judul Bagian')

@section('content')
@php
    $input = 'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1';
    $ok = 'border-slate-300 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $label = 'mb-2 block text-sm font-medium text-slate-950';
@endphp

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.home.features.section.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            <div>
                <label for="title" class="{{ $label }}">Judul <span class="text-red-600">*</span></label>
                <input id="title" type="text" name="title" value="{{ old('title', $section->title) }}" required maxlength="120"
                       class="{{ $input }} {{ $errors->has('title') ? $bad : $ok }}">
                @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-2 text-xs text-slate-500">Di situs ditampilkan dengan huruf kapital semua.</p>
            </div>

            <div>
                <label for="description" class="{{ $label }}">Deskripsi <span class="text-red-600">*</span></label>
                <textarea id="description" name="description" rows="4" required maxlength="400"
                          class="{{ $input }} {{ $errors->has('description') ? $bad : $ok }}">{{ old('description', $section->description) }}</textarea>
                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ route('admin.home.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
