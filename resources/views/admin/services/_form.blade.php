@php
    $input = 'block w-full border bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1';
    $ok = 'border-slate-400 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $label = 'mb-2 block text-sm font-medium text-slate-950';
    $tool = 'inline-flex items-center gap-x-1.5 rounded-md px-3 py-1.5 text-xs font-semibold text-slate-700 border border-slate-300 transition-colors hover:bg-slate-100';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(isset($service))
        @method('PUT')
    @endif

    <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">

        <!-- Judul -->
        <div>
            <label for="title" class="{{ $label }}">Judul <span class="text-red-600">*</span></label>
            <input id="title" type="text" name="title" value="{{ old('title', $service->title ?? '') }}" required maxlength="150"
                   class="{{ $input }} {{ $errors->has('title') ? $bad : $ok }}">
            @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="{{ $label }}">Status <span class="text-red-600">*</span></label>
            <select id="status" name="status" required
                    class="{{ $input }} sm:w-64 {{ $errors->has('status') ? $bad : $ok }}">
                @foreach([\App\Models\Service::STATUS_PUBLISHED => 'Terbit', \App\Models\Service::STATUS_DRAFT => 'Draft'] as $value => $text)
                    <option value="{{ $value }}" @selected(old('status', $service->status ?? \App\Models\Service::STATUS_PUBLISHED) === $value)>{{ $text }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-xs text-slate-500">Layanan berstatus Draft tidak tampil di situs. Pilih Terbit agar dapat dilihat pengunjung.</p>
            @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Gambar -->
        <div>
            <label for="image" class="{{ $label }}">
                Gambar @if(! isset($service))<span class="text-red-600">*</span>@endif
            </label>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <img id="image-preview" src="{{ isset($service) ? $service->image_url : '' }}" alt="Pratinjau gambar"
                     class="aspect-video w-full bg-slate-100 object-cover sm:w-64 {{ isset($service) ? '' : 'hidden' }}">
                <div class="flex-1">
                    <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" {{ isset($service) ? '' : 'required' }}
                           class="block w-full text-sm text-slate-700 file:mr-4 file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-2 text-xs text-slate-500">
                        JPG, PNG, atau WebP, maksimal {{ \App\Support\UploadLimit::label(\App\Support\UploadLimit::kilobytes(\App\Http\Requests\Admin\ServiceRequest::IMAGE_MAX_KB)) }}.
                        @if(isset($service)) Kosongkan jika tidak ingin mengganti gambar. @endif
                    </p>
                    @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Deskripsi halaman layanan -->
        <div>
            <label for="summary" class="{{ $label }}">Deskripsi untuk Halaman Layanan <span class="text-red-600">*</span></label>
            <textarea id="summary" name="summary" rows="4" required maxlength="600"
                      class="{{ $input }} {{ $errors->has('summary') ? $bad : $ok }}">{{ old('summary', $service->summary ?? '') }}</textarea>
            <p class="mt-2 text-xs text-slate-500">Ringkasan yang tampil pada kartu layanan dan di bagian atas halaman detail.</p>
            @error('summary') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Deskripsi detail -->
        <div>
            <label for="detail" class="{{ $label }}">Deskripsi Detail Layanan <span class="text-red-600">*</span></label>

            <div class="mb-2 flex flex-wrap items-center gap-2">
                <button type="button" data-format="heading" class="{{ $tool }}">
                    <span class="font-bold">H1</span> Judul bergaris
                </button>
                <button type="button" data-format="list" class="{{ $tool }}">
                    <span class="inline-block size-2 bg-blue-800"></span> List
                </button>
            </div>

            <textarea id="detail" name="detail" rows="12" required maxlength="10000"
                      class="{{ $input }} font-mono {{ $errors->has('detail') ? $bad : $ok }}">{{ old('detail', $service->detail ?? '') }}</textarea>
            <p class="mt-2 text-xs text-slate-500">
                Tulis satu baris per paragraf. Pilih baris lalu klik tombol di atas, atau ketik manual:
                <code class="bg-slate-100 px-1"># Judul</code> untuk judul bergaris dan
                <code class="bg-slate-100 px-1">- Item</code> untuk list berpenanda kotak biru. Baris lain menjadi paragraf.
            </p>
            @error('detail') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex items-center justify-end gap-x-3">
        <a href="{{ route('admin.services.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 border border-slate-300 transition-colors hover:bg-slate-100">Batal</a>
        <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">
            {{ $submit }}
        </button>
    </div>
</form>

<script>
(function () {
    var ta = document.getElementById('detail');
    var prefixes = { heading: '# ', list: '- ' };

    // Pratinjau gambar yang baru dipilih.
    var file = document.getElementById('image');
    var preview = document.getElementById('image-preview');
    file.addEventListener('change', function () {
        if (file.files && file.files[0]) {
            preview.src = URL.createObjectURL(file.files[0]);
            preview.classList.remove('hidden');
        }
    });

    // Tombol format: pasang / lepas awalan pada baris yang dipilih.
    document.querySelectorAll('[data-format]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var prefix = prefixes[btn.dataset.format];
            var v = ta.value;
            var start = v.lastIndexOf('\n', ta.selectionStart - 1) + 1;
            var endIdx = v.indexOf('\n', ta.selectionEnd);
            var end = endIdx === -1 ? v.length : endIdx;

            var lines = v.slice(start, end).split('\n');
            var allHave = lines.every(function (l) { return l.indexOf(prefix) === 0; });

            lines = lines.map(function (l) {
                // Buang awalan format lain lebih dulu agar satu baris hanya punya satu format.
                var clean = l.replace(/^(# |- )/, '');
                return allHave ? clean : prefix + clean;
            });

            var out = lines.join('\n');
            ta.value = v.slice(0, start) + out + v.slice(end);
            ta.focus();
            ta.setSelectionRange(start, start + out.length);
        });
    });
})();
</script>
