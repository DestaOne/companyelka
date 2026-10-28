@extends('layouts.app')

@section('content')
<x-hero-banner slug="layanan" />

<!-- Tiap layanan memiliki section sendiri -->
@foreach($services as $index => $service)
<section class="bg-slate-100 py-6 md:py-2 {{ $loop->first ? 'pt-12 md:pt-20' : '' }} {{ $loop->last ? 'pb-12 md:pb-20' : '' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-justify">
        <x-service-card
            :title="$service->title"
            :desc="$service->summary"
            :image="$service->image_url"
            :url="route('layanan.show', $service)"
            :imagePosition="$index % 2 == 0 ? 'left' : 'right'"
        />
    </div>
</section>
@endforeach

<x-cta-banner slug="layanan" />
@endsection
