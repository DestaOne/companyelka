@props(['type', 'slug'])

@php
    $banner = \App\Models\Banner::for($type, $slug);
    $isHero = $type === 'hero';
    $overlay = $isHero ? 'from-slate-950/85 via-slate-900/70 to-slate-900/50' : 'from-blue-900/90 via-blue-900/80 to-blue-800/70';
    $heading = collect($banner->blocks())->firstWhere('type', 'heading')['text'] ?? '';
    $description = collect($banner->blocks())->firstWhere('type', 'text')['text'] ?? '';
@endphp

<section id="{{ $banner->anchor() }}" class="scroll-mt-28" aria-label="{{ $isHero ? 'Hero banner' : 'CTA' }} {{ $banner->definition['label'] }}">
    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
            {{ $isHero ? 'Hero Banner' : 'CTA' }}
            <span class="font-medium normal-case tracking-normal text-slate-400">&middot; {{ $banner->definition['label'] }}</span>
        </p>
        <div class="flex items-center gap-x-1">
            @if($banner->slug !== 'layanan-detail')
                <a href="{{ url($banner->definition['page']) }}" target="_blank" rel="noopener" class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100">Lihat halaman</a>
            @endif
            <a href="{{ route("admin.banners.{$type}.edit", $banner->slug) }}" class="inline-flex items-center gap-x-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-100">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                Edit
            </a>
        </div>
    </div>

    <a href="{{ route("admin.banners.{$type}.edit", $banner->slug) }}" class="group relative block overflow-hidden rounded-2xl bg-slate-900 shadow-sm ring-1 ring-slate-200/70 transition-shadow hover:shadow-md">
        <img src="{{ $banner->image_url }}" alt="" class="absolute inset-0 size-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r {{ $overlay }}"></div>

        <div class="relative px-6 py-8 sm:px-10 {{ $isHero ? 'sm:py-12' : 'sm:py-10' }}">
            @if($banner->definition['badge'] ?? null)
                <span class="mb-3 inline-block border border-white/30 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white/80">{{ $banner->definition['badge'] }}</span>
            @endif
            <h3 class="max-w-2xl break-words font-extrabold leading-tight text-white {{ $isHero ? 'text-2xl sm:text-3xl' : 'text-xl sm:text-2xl' }}">{{ $heading }}</h3>
            @if($description)
                <p class="mt-3 line-clamp-2 max-w-2xl text-sm {{ $isHero ? 'text-slate-300' : 'text-blue-100' }}">{{ $description }}</p>
            @endif
            @if($banner->hasButton())
                <span class="mt-5 inline-flex items-center bg-white px-5 py-2.5 text-sm font-semibold text-blue-800">{{ $banner->button_text }}</span>
            @endif
        </div>
    </a>
</section>
