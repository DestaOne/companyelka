@extends('admin.layouts.app')

@section('title', $name.': '.$banner->definition['label'])

@section('content')
@php
    $input = 'block w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1';
    $ok = 'border-slate-300 focus:border-blue-700 focus:ring-blue-700';
    $bad = 'border-red-600 focus:border-red-600 focus:ring-red-600';
    $label = 'mb-2 block text-sm font-medium text-slate-950';
    $dark = $type === 'hero' ? 'bg-slate-900' : 'bg-blue-900';
    $sub = $type === 'hero' ? 'text-slate-300' : 'text-blue-200';
@endphp

<div class="max-w-3xl">
    <form method="POST" action="{{ route("admin.banners.{$type}.update", $banner->slug) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
            <div>
                <label for="image" class="{{ $label }}">Gambar Latar</label>
                <img src="{{ $banner->image_url }}" alt="Gambar saat ini" class="mb-3 h-40 w-full rounded-lg bg-slate-100 object-cover">
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp"
                       class="block w-full rounded-lg border bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200 {{ $errors->has('image') ? $bad : $ok }}">
                @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-2 text-xs text-slate-500">Opsional. JPG, PNG, atau WebP, lebar minimal 1200 piksel, maksimal {{ \App\Support\UploadLimit::label(\App\Support\UploadLimit::kilobytes(\App\Http\Requests\Admin\BannerRequest::IMAGE_MAX_KB)) }}. Kosongkan untuk mempertahankan gambar saat ini.</p>
            </div>

            <div>
                <span id="content-label" class="{{ $label }}">Teks <span class="text-red-600">*</span></span>

                {{-- Tanpa JavaScript, teks diedit langsung dengan format: baris berawalan "# " menjadi judul. --}}
                <textarea id="content" name="content" rows="6" required maxlength="1500" aria-labelledby="content-label"
                          class="{{ $input }} {{ $errors->has('content') ? $bad : $ok }}">{{ old('content', $banner->content) }}</textarea>

                <div id="editor" class="hidden overflow-hidden rounded-lg border {{ $errors->has('content') ? 'border-red-600' : 'border-slate-300' }}">
                    <div class="flex items-center gap-x-2 border-b border-slate-200 bg-slate-50 px-3 py-2">
                        <button type="button" id="editor-heading" aria-pressed="false" title="Jadikan judul (tekan lagi untuk kembali ke deskripsi)"
                                class="flex size-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-sm font-extrabold text-slate-700 hover:bg-slate-100 aria-pressed:border-blue-700 aria-pressed:bg-blue-700 aria-pressed:text-white">H</button>
                        <span class="text-xs text-slate-500">Letakkan kursor di sebuah baris lalu tekan <strong>H</strong> untuk menjadikannya judul. Baris lain menjadi deskripsi.</span>
                    </div>
                    <div id="editor-area" contenteditable="true" role="textbox" aria-multiline="true" aria-labelledby="content-label"
                         class="min-h-40 space-y-3 p-5 text-center focus:outline-none {{ $dark }}"></div>
                </div>
                @error('content') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            @if($banner->hasButton())
                <div>
                    <label for="button_text" class="{{ $label }}">Teks Tombol <span class="text-red-600">*</span></label>
                    <input id="button_text" type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" required maxlength="40"
                           class="{{ $input }} {{ $errors->has('button_text') ? $bad : $ok }}">
                    @error('button_text') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-x-3">
            <a href="{{ $banner->adminUrl() }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">Simpan Perubahan</button>
        </div>
    </form>
</div>

<style>
    /* Pratinjau gaya: judul besar dan tebal, deskripsi lebih kecil dan redup, seperti di situs. */
    #editor-area h2 { color: #fff; font-weight: 800; font-size: {{ $type === 'hero' ? '1.75rem' : '1.5rem' }}; line-height: 1.2; margin: 0; }
    #editor-area p { margin: 0; font-size: 1rem; line-height: 1.6; color: {{ $type === 'hero' ? '#cbd5e1' : '#bfdbfe' }}; }
</style>

<script>
(function () {
    var textarea = document.getElementById('content');
    var editor = document.getElementById('editor');
    var area = document.getElementById('editor-area');
    var button = document.getElementById('editor-heading');
    var form = textarea.form;

    function block(tag, text) {
        var el = document.createElement(tag);
        if (text) { el.textContent = text; } else { el.appendChild(document.createElement('br')); }
        return el;
    }

    // Format simpan: baris "# Judul" untuk judul, baris lain untuk deskripsi.
    function load() {
        area.textContent = '';
        textarea.value.split(/\r?\n/).forEach(function (line) {
            line = line.trim();
            if (line === '') { return; }
            area.appendChild(line.indexOf('# ') === 0 ? block('h2', line.slice(2).trim()) : block('p', line));
        });
        if (!area.firstChild) { area.appendChild(block('p')); }
    }

    function serialize() {
        var lines = [];
        Array.prototype.forEach.call(area.childNodes, function (node) {
            var text = (node.innerText !== undefined ? node.innerText : node.textContent || '').replace(/\s+/g, ' ').trim();
            if (text === '') { return; }
            lines.push((node.nodeName === 'H2' || /^H[1-6]$/.test(node.nodeName) ? '# ' : '') + text);
        });
        return lines.join('\n');
    }

    function currentBlock() {
        var sel = window.getSelection();
        var node = sel.rangeCount ? sel.anchorNode : null;
        while (node && node.parentNode !== area) { node = node.parentNode; }
        return node && node.parentNode === area ? node : null;
    }

    function refreshButton() {
        var el = currentBlock();
        button.setAttribute('aria-pressed', el && el.nodeName === 'H2' ? 'true' : 'false');
    }

    // Tombol H mengubah baris tempat kursor berada: deskripsi menjadi judul, judul kembali menjadi deskripsi.
    button.addEventListener('mousedown', function (e) { e.preventDefault(); });
    button.addEventListener('click', function () {
        area.focus();
        var el = currentBlock();
        document.execCommand('formatBlock', false, el && el.nodeName === 'H2' ? '<p>' : '<h2>');
        refreshButton();
    });

    area.addEventListener('keydown', function (e) {
        // Hanya judul dan deskripsi yang didukung: tanpa tebal, miring, atau garis bawah.
        if ((e.ctrlKey || e.metaKey) && /^[biu]$/i.test(e.key)) { e.preventDefault(); }
    });
    area.addEventListener('paste', function (e) {
        e.preventDefault();
        var text = (e.clipboardData || window.clipboardData).getData('text').replace(/\s*\r?\n\s*/g, ' ');
        document.execCommand('insertText', false, text);
    });
    area.addEventListener('input', function () {
        if (!area.firstChild) { area.appendChild(block('p')); }
    });
    document.addEventListener('selectionchange', function () { if (area.contains(document.activeElement)) { refreshButton(); } });

    form.addEventListener('submit', function () { textarea.value = serialize(); });

    document.execCommand('defaultParagraphSeparator', false, 'p');
    load();
    textarea.classList.add('hidden');
    textarea.removeAttribute('required');
    editor.classList.remove('hidden');
})();
</script>
@endsection
