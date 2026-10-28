@extends('admin.layouts.app')

@section('title', 'Kontak dan Lokasi')

@section('content')
@php
    $input = 'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1';
    $ok = 'border-slate-300 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $label = 'mb-2 block text-sm font-medium text-slate-950';
    $sub = 'mb-2 block text-sm font-medium text-slate-700';
@endphp

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.contact.update') }}" class="space-y-6" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            <p class="text-sm text-slate-500">Perubahan di sini otomatis berlaku di halaman Tentang Kami (bagian kontak) serta footer seluruh situs.</p>

            <div class="space-y-3">
                <div>
                    <label for="address" class="{{ $label }}">Alamat Kantor <span class="text-red-600">*</span></label>
                    <textarea id="address" name="address" rows="3" required maxlength="300"
                              class="{{ $input }} {{ $errors->has('address') ? $bad : $ok }}">{{ old('address', $contact->address) }}</textarea>
                    @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="maps_link" class="{{ $sub }}">Link Google Maps</label>
                    <input id="maps_link" type="text" name="maps_link" value="{{ old('maps_link', $contact->maps_link) }}" maxlength="2000" placeholder="https://www.google.com/maps/embed?pb=..."
                           class="{{ $input }} {{ $errors->has('maps_link') ? $bad : $ok }}">
                    @error('maps_link') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-slate-500">Opsional. Link ini dipakai untuk tautan alamat. Cukup salin dari Google Maps: Bagikan, lalu Salin link (link pendek maps.app.goo.gl juga bisa). Bila dikosongkan, tautan mengikuti teks alamat.</p>
                </div>
            </div>

            <div class="space-y-3 border-t border-slate-200 pt-6">
                <div>
                    <label for="whatsapp" class="{{ $label }}">Nomor WhatsApp <span class="text-red-600">*</span></label>
                    <input id="whatsapp" type="text" inputmode="tel" name="whatsapp" value="{{ old('whatsapp', $contact->whatsapp_display) }}" required maxlength="25"
                           class="{{ $input }} {{ $errors->has('whatsapp') ? $bad : $ok }}">
                    @error('whatsapp') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-slate-500">Contoh: 08113383679 atau +62 811 3383 679.</p>
                </div>
                <div>
                    <label for="whatsapp_link" class="{{ $sub }}">Link WhatsApp</label>
                    <input id="whatsapp_link" type="text" name="whatsapp_link" value="{{ old('whatsapp_link', $contact->whatsapp_link) }}" maxlength="2000" placeholder="https://wa.me/628113383679?text=Halo"
                           class="{{ $input }} {{ $errors->has('whatsapp_link') ? $bad : $ok }}">
                    @error('whatsapp_link') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-slate-500">Opsional. Harus dari wa.me atau whatsapp.com, mis. untuk mengubah pesan pembuka. Bila dikosongkan, dibuat otomatis dari nomor di atas.</p>
                </div>
            </div>

            <div class="space-y-3 border-t border-slate-200 pt-6">
                <div>
                    <label for="email" class="{{ $label }}">Email <span class="text-red-600">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email', $contact->email) }}" required maxlength="255"
                           class="{{ $input }} {{ $errors->has('email') ? $bad : $ok }}">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email_link" class="{{ $sub }}">Link Email</label>
                    <input id="email_link" type="text" name="email_link" value="{{ old('email_link', $contact->email_link) }}" maxlength="2000" placeholder="mailto:halo@contoh.com?subject=Konsultasi"
                           class="{{ $input }} {{ $errors->has('email_link') ? $bad : $ok }}">
                    @error('email_link') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-2 text-xs text-slate-500">Opsional. Harus diawali mailto:, mis. untuk mengisi subjek otomatis. Bila dikosongkan, dibuat otomatis dari email di atas.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ route('admin.about.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
