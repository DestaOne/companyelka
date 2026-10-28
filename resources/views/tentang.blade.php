@extends('layouts.app')

@section('content')
<x-hero-banner slug="tentang" />


<!-- Bagian Latar Belakang & Foto -->
<div class="bg-slate-50 py-20 md:py-28 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            
            @php $bg = $about['background']; @endphp
            <div class="relative w-full aspect-[4/3] bg-slate-200 ring-1 ring-slate-200 overflow-hidden shadow-sm">
                <img src="{{ $bg->image_url }}" alt="Fasilitas Kantor" class="w-full h-full object-cover">
            </div>

            <div class="flex flex-col">
                <div class="inline-flex items-center space-x-3 mb-4">
                    <span class="w-6 h-[2px] bg-blue-800"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ $bg->eyebrow }}</span>
                </div>

                <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-snug mb-8">
                    {{ $bg->title }}
                </h2>

                <div class="flex flex-col space-y-6">
                    @foreach($bg->lines() as $paragraph)
                    <p class="text-slate-600 text-base md:text-lg leading-relaxed font-medium text-justify">
                        {{ $paragraph }}
                    </p>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>
</div>



<div class="bg-white py-20 md:py-28 border-b border-slate-200 text-justify">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-4">{{ $about['principles']->title }}</h2>
            <p class="text-slate-500 text-lg font-medium">{{ $about['principles']->body }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 border-t border-l border-slate-300 bg-white">

            <x-feature-card :title="$about['vision']->title">
                <p class="text-slate-600 text-lg leading-relaxed font-light">
                    {{ $about['vision']->body }}
                </p>
            </x-feature-card>

            <x-feature-card :title="$about['mission']->title">
                <ul class="space-y-4">
                    @foreach($about['mission']->lines() as $item)
                    <li class="flex items-start">
                        <span class="w-2 h-2 bg-blue-800 mt-2.5 mr-4 flex-shrink-0"></span>
                        <span class="text-slate-600 text-base md:text-lg leading-relaxed font-light">{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
            </x-feature-card>
        </div>
    </div>
</div>



<div id="kontak" class="scroll-mt-4 py-20 md:py-28 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-stretch">
            
          

            <div class="flex flex-col">
    
  
    <div class="inline-flex items-center space-x-3 mb-4">
        <span class="w-6 h-[2px] bg-blue-800"></span>
        <span class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ $about['contact']->eyebrow }}</span>
    </div>
    
    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-4">{{ $about['contact']->title }}</h2>
    <p class="text-slate-600 text-lg mb-10">{{ $about['contact']->body }}</p>

    <!-- Kotak Besar Bergaris (Container Kontak) -->
    <div class="border border-slate-200 p-6 md:p-8 bg-white">
        
        <!-- divide-y akan otomatis membuat garis tipis di antara item, tapi tidak di atas/bawahnya -->
        <div class="flex flex-col divide-y divide-slate-100">
            
            <x-contact-item
                type="location"
                label="Alamat Kantor"
                :value="$contact->address"
                :url="$contact->maps_url"
            />

            <x-contact-item
                type="whatsapp"
                label="WhatsApp"
                :value="$contact->whatsapp_display"
                :url="$contact->whatsapp_url"
            />

            <x-contact-item
                type="email"
                label="Email"
                :value="$contact->email"
                :url="$contact->email_url"
            />

        </div>
    </div>
</div>

            <div class="bg-white border border-slate-200 p-6 md:p-8 shadow-sm">
                @php
                    $field = 'block w-full border bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-1';
                    $ok = 'border-slate-300 focus:border-blue-800 focus:ring-blue-800';
                    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
                    $label = 'mb-2 block text-sm font-medium text-slate-900';
                @endphp

                <h3 class="text-2xl font-bold text-slate-900 tracking-tight mb-2">Kirim Pesan</h3>
                <p class="text-slate-600 mb-6">Isi formulir berikut dan tim kami akan segera menghubungi Anda.</p>

                @if(session('message_sent'))
                    <div role="status" class="mb-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('message_sent') }}</div>
                @endif

                <form method="POST" action="{{ route('pesan.store') }}" class="space-y-5">
                    @csrf

                    {{-- Kolom jebakan untuk robot; tidak terlihat dan tidak bisa dijangkau pengunjung. --}}
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Website</label>
                        <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div>
                        <label for="name" class="{{ $label }}">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name"
                               class="{{ $field }} {{ $errors->has('name') ? $bad : $ok }}">
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="{{ $label }}">No. Telepon / WhatsApp <span class="text-red-600">*</span></label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required maxlength="20" autocomplete="tel" placeholder="0811 3383 679"
                                   class="{{ $field }} {{ $errors->has('phone') ? $bad : $ok }}">
                            @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="{{ $label }}">Email <span class="text-red-600">*</span></label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                                   class="{{ $field }} {{ $errors->has('email') ? $bad : $ok }}">
                            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="{{ $label }}">Subjek <span class="text-red-600">*</span></label>
                        <input id="subject" type="text" name="subject" value="{{ old('subject') }}" required maxlength="150"
                               class="{{ $field }} {{ $errors->has('subject') ? $bad : $ok }}">
                        @error('subject') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="body" class="{{ $label }}">Pesan <span class="text-red-600">*</span></label>
                        <textarea id="body" name="body" rows="5" required minlength="10" maxlength="3000"
                                  class="{{ $field }} {{ $errors->has('body') ? $bad : $ok }}">{{ old('body') }}</textarea>
                        @error('body') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="inline-flex h-12 w-full items-center justify-center bg-blue-800 px-6 text-base font-medium text-white transition-colors hover:bg-blue-900">Kirim Pesan</button>
                </form>
            </div>

        </div>
    </div>
</div>



@endsection