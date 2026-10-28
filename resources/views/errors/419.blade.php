@extends('layouts.app')

@section('content')
@php $isAdminArea = request()->is('admin', 'admin/*'); @endphp

<x-error-page
    code="419"
    eyebrow="419 Page Expired"
    title="Sesi Anda Telah Berakhir"
    message="Demi keamanan, halaman ini kedaluwarsa karena sudah terlalu lama tidak digunakan. Silakan muat ulang halaman lalu coba lagi.">
    @if($isAdminArea)
        <a href="{{ route('admin.login') }}" class="inline-flex h-12 items-center justify-center bg-blue-800 px-10 text-sm font-bold text-white uppercase tracking-widest ring-1 ring-blue-800 hover:bg-transparent hover:text-blue-800 transition-all duration-300 ease-in-out">
            Masuk Kembali
        </a>
    @else
        <a href="{{ url()->previous(url('/')) }}" class="inline-flex h-12 items-center justify-center bg-blue-800 px-10 text-sm font-bold text-white uppercase tracking-widest ring-1 ring-blue-800 hover:bg-transparent hover:text-blue-800 transition-all duration-300 ease-in-out">
            Muat Ulang Halaman
        </a>
        <a href="{{ url('/') }}" class="inline-flex h-12 items-center justify-center px-10 text-sm font-bold text-blue-800 uppercase tracking-widest ring-1 ring-blue-800 hover:bg-blue-800 hover:text-white transition-all duration-300 ease-in-out">
            Ke Beranda
        </a>
    @endif
</x-error-page>
@endsection
