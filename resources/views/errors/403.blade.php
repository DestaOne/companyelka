@extends('layouts.app')

@section('content')
<x-error-page
    code="403"
    eyebrow="403 Forbidden"
    title="Akses Ditolak"
    message="Maaf, Anda tidak memiliki izin untuk membuka halaman ini. Bila Anda merasa ini sebuah kesalahan, silakan hubungi administrator." />
@endsection
