<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Ukuran Unggahan Terlalu Besar</title>
    @vite('resources/css/app.css')
</head>
<body class="h-full bg-gray-50 text-gray-950 antialiased">
<div class="flex min-h-full items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white p-8 text-center shadow-sm ring-1 ring-gray-950/5">
        <p class="text-sm font-semibold text-blue-600">413</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight">Ukuran unggahan terlalu besar</h1>
        <p class="mt-3 text-sm text-gray-500">
            Total ukuran berkas yang dikirim melebihi batas server
            @php $limit = (int) floor(\App\Support\UploadLimit::postMaxBytes() / 1024); @endphp
            @if($limit > 0) ({{ \App\Support\UploadLimit::label($limit) }} per sekali unggah)@endif.
            Kecilkan ukuran gambar atau unggah lebih sedikit gambar sekaligus.
        </p>
        <a href="javascript:history.back()" class="mt-6 inline-block bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-500">Kembali</a>
    </div>
</div>
</body>
</html>
