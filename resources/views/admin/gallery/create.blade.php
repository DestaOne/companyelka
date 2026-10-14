@extends('admin.layouts.app')

@section('title', 'Tambah Gambar')

@section('content')
@php
    $maxKb = \App\Support\UploadLimit::kilobytes(\App\Http\Requests\Admin\GalleryRequest::APP_MAX_KB);
    $postMax = (int) floor(\App\Support\UploadLimit::postMaxBytes() / 1024);
@endphp
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            <label for="images" class="mb-2 block text-sm font-medium text-slate-950">Gambar <span class="text-red-600">*</span></label>

            <input id="images" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required
                   class="block w-full text-sm text-slate-700 file:mr-4 file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">

            <ul class="mt-2 space-y-1 text-xs text-slate-500">
                <li>Bisa memilih beberapa gambar sekaligus (maksimal 10).</li>
                <li>JPG, PNG, atau WebP, maksimal {{ \App\Support\UploadLimit::label($maxKb) }} per gambar, lebar minimal 800 piksel.</li>
                @if($postMax > 0)
                <li>Total ukuran semua gambar dalam sekali unggah maksimal {{ \App\Support\UploadLimit::label($postMax) }}.</li>
                @endif
                <li>Gambar otomatis dikecilkan dan dikonversi ke WebP, jadi halaman galeri tetap ringan namun tetap tajam saat di-zoom.</li>
            </ul>

            @error('images') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            @foreach($errors->get('images.*') as $messages)
                @foreach($messages as $message)
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @endforeach
            @endforeach

            <div id="previews" class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4"></div>
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ route('admin.gallery.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 border border-slate-300 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Unggah</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('images').addEventListener('change', function (e) {
        var box = document.getElementById('previews');
        box.innerHTML = '';
        Array.from(e.target.files).forEach(function (file) {
            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            img.className = 'aspect-[4/3] w-full bg-slate-100 object-cover';
            box.appendChild(img);
        });
    });
</script>
@endsection
