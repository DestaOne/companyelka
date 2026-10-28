@props(['user', 'size' => 'size-10', 'text' => 'text-sm'])

@if($user?->photo_url)
    <img src="{{ $user->photo_url }}" alt="Foto {{ $user->name }}" {{ $attributes->class([$size, 'shrink-0 rounded-full bg-slate-100 object-cover']) }}>
@else
    <span {{ $attributes->class([$size, $text, 'flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-700 to-blue-800 font-semibold text-white']) }}>{{ mb_strtoupper(mb_substr($user?->name ?? '?', 0, 1)) }}</span>
@endif
