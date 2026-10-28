@extends('admin.layouts.app')

@section('title', 'Profil')

@section('content')
@php
    $input = 'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1';
    $ok = 'border-slate-300 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $label = 'mb-2 block text-sm font-medium text-slate-950';
    // Bagian password terbuka kembali bila validasinya gagal.
    $passwordOpen = old('change_password') || $errors->hasAny(['current_password', 'password']);
@endphp

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            <div>
                <span class="{{ $label }}">Foto Profil</span>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <x-avatar :user="$user" size="size-24" text="text-3xl" id="photo-preview" />
                    <div class="min-w-0 flex-1">
                        <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" aria-label="Foto profil"
                               class="block w-full rounded-lg border bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200 {{ $errors->has('photo') ? $bad : $ok }}">
                        @error('photo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-slate-500">Opsional. JPG, PNG, atau WebP, minimal 200 x 200 piksel, maksimal {{ \App\Support\UploadLimit::label(\App\Support\UploadLimit::kilobytes(\App\Http\Requests\Admin\ProfileRequest::PHOTO_MAX_KB)) }}. Foto dipotong persegi dari tengah.</p>
                        @if($user->photo)
                            <label class="mt-3 inline-flex items-center gap-x-2 text-sm text-slate-700">
                                <input type="checkbox" name="remove_photo" value="1" @checked(old('remove_photo')) class="size-4 rounded border-slate-300 text-blue-700 focus:ring-blue-700">
                                Hapus foto saat ini
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <label for="name" class="{{ $label }}">Username <span class="text-red-600">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="username"
                       class="{{ $input }} {{ $errors->has('name') ? $bad : $ok }}">
                @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="{{ $label }}">Email <span class="text-red-600">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email"
                       class="{{ $input }} {{ $errors->has('email') ? $bad : $ok }}">
                @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-2 text-xs text-slate-500">Email dipakai untuk masuk ke panel admin.</p>
            </div>

            <div class="border-t border-slate-200 pt-6">
                <input type="hidden" name="change_password" id="change_password" value="{{ $passwordOpen ? 1 : 0 }}">

                <button type="button" id="password-toggle" aria-expanded="{{ $passwordOpen ? 'true' : 'false' }}" aria-controls="password-fields"
                        class="inline-flex items-center gap-x-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    <span id="password-toggle-text">{{ $passwordOpen ? 'Batal ubah password' : 'Ubah password' }}</span>
                </button>

                <div id="password-fields" class="mt-6 space-y-6 {{ $passwordOpen ? '' : 'hidden' }}">
                    <div>
                        <label for="current_password" class="{{ $label }}">Password Lama <span class="text-red-600">*</span></label>
                        <input id="current_password" type="password" name="current_password" autocomplete="current-password" {{ $passwordOpen ? 'required' : 'disabled' }}
                               class="{{ $input }} {{ $errors->has('current_password') ? $bad : $ok }}">
                        @error('current_password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="{{ $label }}">Password Baru <span class="text-red-600">*</span></label>
                        <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" {{ $passwordOpen ? 'required' : 'disabled' }}
                               class="{{ $input }} {{ $errors->has('password') ? $bad : $ok }}">
                        @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-slate-500">Minimal 8 karakter.</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="{{ $label }}">Konfirmasi Password Baru <span class="text-red-600">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" {{ $passwordOpen ? 'required' : 'disabled' }}
                               class="{{ $input }} {{ $ok }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
(function () {
    var toggle = document.getElementById('password-toggle');
    var fields = document.getElementById('password-fields');
    var flag = document.getElementById('change_password');
    var text = document.getElementById('password-toggle-text');
    var inputs = fields.querySelectorAll('input');

    // Saat tertutup, kolom password dinonaktifkan agar tidak ikut terkirim dan tidak diisi otomatis browser.
    function setOpen(open) {
        fields.classList.toggle('hidden', !open);
        flag.value = open ? 1 : 0;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        text.textContent = open ? 'Batal ubah password' : 'Ubah password';
        inputs.forEach(function (el) {
            el.disabled = !open;
            el.required = open;
            if (!open) { el.value = ''; }
        });
        if (open) { document.getElementById('current_password').focus(); }
    }

    toggle.addEventListener('click', function () { setOpen(fields.classList.contains('hidden')); });

    // Pratinjau foto yang baru dipilih sebelum disimpan.
    var photo = document.getElementById('photo');
    photo.addEventListener('change', function () {
        var file = photo.files[0];
        var preview = document.getElementById('photo-preview');
        if (!file || !/^image\//.test(file.type)) { return; }
        var img = preview.tagName === 'IMG' ? preview : document.createElement('img');
        img.id = 'photo-preview';
        img.className = 'size-24 shrink-0 rounded-full bg-slate-100 object-cover';
        img.alt = 'Pratinjau foto';
        img.src = URL.createObjectURL(file);
        if (img !== preview) { preview.replaceWith(img); }
    });
})();
</script>
@endsection
