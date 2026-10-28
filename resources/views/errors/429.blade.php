@extends('layouts.app')

@section('content')
@php
    // Retry-After berisi jumlah detik sampai permintaan boleh dicoba lagi.
    $retry = (int) ($exception->getHeaders()['Retry-After'] ?? 0);
    $wait = $retry >= 60 ? ceil($retry / 60).' menit' : $retry.' detik';
    $message = 'Anda mengirim permintaan terlalu sering dalam waktu singkat. '
        .($retry > 0 ? "Silakan coba lagi dalam {$wait}." : 'Silakan tunggu sebentar lalu coba lagi.');
@endphp

<x-error-page
    code="429"
    eyebrow="429 Too Many Requests"
    title="Terlalu Banyak Permintaan"
    :message="$message" />
@endsection
