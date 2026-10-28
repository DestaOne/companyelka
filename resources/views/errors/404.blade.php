@extends('layouts.app')

@section('content')
<x-error-page
    code="404"
    eyebrow="404 Page Not Found"
    title="Halaman Tidak Ditemukan"
    message="Maaf, sistem kami tidak dapat menemukan halaman yang Anda tuju. Halaman ini mungkin telah dipindahkan, dihapus, atau tautannya rusak." />
@endsection
