@extends('admin.layouts.app')

@section('title', 'Galeri')

@section('actions')
    <a href="{{ route('admin.gallery.create') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-800">
        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Gambar
    </a>
@endsection

@section('content')
@if($images->isEmpty())
    <div class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-200/70">
        <p class="text-sm font-medium text-slate-900">Belum ada gambar</p>
        <p class="mt-1 text-sm text-slate-500">Klik "Tambah Gambar" untuk mengunggah dokumentasi pertama.</p>
    </div>
@else
    <div class="mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
        <p class="text-sm text-slate-500">{{ $images->count() }} gambar &middot; pratinjau ini sama dengan tampilan bento grid di halaman Galeri. <strong class="font-semibold text-slate-700">Seret gambar</strong> (pegang ikon <span aria-hidden="true">⠿</span>) ke posisi yang diinginkan, atau pakai tombol panah.</p>
        <p id="reorder-status" role="status" aria-live="polite" class="text-sm font-medium text-slate-500"></p>
    </div>

    <div id="bento" data-url="{{ route('admin.gallery.reorder') }}" data-pattern='@json(\App\Models\GalleryImage::GRID_PATTERN)'
         class="grid grid-cols-1 gap-4 md:grid-cols-12 auto-rows-[220px]">
        @foreach($images as $image)
            <div data-id="{{ $image->id }}" class="{{ \App\Models\GalleryImage::GRID_PATTERN[$loop->index % 8] }} group relative overflow-hidden bg-slate-200 ring-1 ring-slate-200 transition-shadow">
                <img src="{{ $image->thumbnail_url }}" alt="Gambar galeri {{ $loop->iteration }}" loading="lazy" draggable="false" class="absolute inset-0 h-full w-full object-cover">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-slate-950/40"></div>

                <div class="absolute inset-x-0 top-0 flex items-center justify-between gap-x-1 p-2">
                    <div class="flex items-center gap-x-1">
                        <button type="button" data-handle aria-label="Seret untuk memindahkan gambar {{ $loop->iteration }}" title="Seret untuk memindahkan"
                                class="cursor-grab bg-slate-950/70 px-2 py-1 text-sm leading-none text-white hover:bg-slate-950 active:cursor-grabbing">⠿</button>
                        <span data-number class="bg-slate-950/70 px-2 py-1 text-xs font-semibold text-white">{{ $loop->iteration }}</span>
                    </div>
                    <div class="flex items-center gap-x-1">
                        <button type="button" data-move="-1" aria-label="Geser gambar {{ $loop->iteration }} lebih awal" title="Lebih awal" class="bg-slate-950/70 px-2 py-1 text-sm leading-none text-white hover:bg-slate-950">←</button>
                        <button type="button" data-move="1" aria-label="Geser gambar {{ $loop->iteration }} lebih akhir" title="Lebih akhir" class="bg-slate-950/70 px-2 py-1 text-sm leading-none text-white hover:bg-slate-950">→</button>
                        <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}"
                              data-confirm-title="Hapus gambar?" data-confirm-message="Gambar ini akan dihapus dari galeri beserta berkasnya. Tindakan ini tidak dapat dibatalkan.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Hapus gambar {{ $loop->iteration }}" title="Hapus" class="bg-red-600 px-2 py-1 text-sm leading-none text-white hover:bg-red-700">✕</button>
                        </form>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.gallery.update', $image) }}" class="absolute inset-x-0 bottom-0 flex gap-x-1 p-2">
                    @csrf
                    @method('PUT')
                    <input type="text" name="title" value="{{ $image->title }}" maxlength="100" placeholder="Judul" aria-label="Judul gambar {{ $loop->iteration }}"
                           class="min-w-0 flex-1 border-0 bg-white/95 px-2 py-1.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <button type="submit" class="shrink-0 bg-blue-700 px-2.5 py-1.5 text-sm font-medium text-white hover:bg-blue-800">Simpan</button>
                </form>
            </div>
        @endforeach
    </div>

    <script>
        (function () {
            var grid = document.getElementById('bento');
            var status = document.getElementById('reorder-status');
            var pattern = JSON.parse(grid.dataset.pattern);
            var token = document.querySelector('#bento input[name=_token]').value;
            var dragged = null;
            var dirty = false;

            function tiles() { return Array.prototype.slice.call(grid.children); }

            // Ukuran kotak mengikuti posisinya, persis seperti di halaman Galeri.
            function layout() {
                var all = pattern.join(' ').split(' ');
                tiles().forEach(function (tile, i) {
                    all.forEach(function (c) { tile.classList.remove(c); });
                    pattern[i % pattern.length].split(' ').forEach(function (c) { tile.classList.add(c); });
                    tile.querySelector('[data-number]').textContent = i + 1;
                });
            }

            function save() {
                status.textContent = 'Menyimpan…';
                fetch(grid.dataset.url, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ order: tiles().map(function (t) { return Number(t.dataset.id); }) })
                }).then(function (r) {
                    if (!r.ok) { throw new Error(); }
                    status.textContent = 'Urutan tersimpan ✓';
                    status.className = 'text-sm font-medium text-emerald-600';
                }).catch(function () {
                    status.textContent = 'Gagal menyimpan urutan. Muat ulang halaman lalu coba lagi.';
                    status.className = 'text-sm font-medium text-red-600';
                });
            }

            grid.addEventListener('pointerdown', function (e) {
                var handle = e.target.closest('[data-handle]');
                if (handle) { handle.closest('[data-id]').draggable = true; }
            });

            grid.addEventListener('dragstart', function (e) {
                dragged = e.target.closest('[data-id]');
                if (!dragged || e.target !== dragged) { return; }
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', dragged.dataset.id);
                dirty = false;
                setTimeout(function () { dragged.classList.add('opacity-40', 'ring-2', 'ring-blue-600'); }, 0);
            });

            grid.addEventListener('dragover', function (e) {
                if (!dragged) { return; }
                e.preventDefault();
                var target = e.target.closest('[data-id]');
                if (!target || target === dragged) { return; }
                var list = tiles();
                var from = list.indexOf(dragged);
                var to = list.indexOf(target);
                // Disisipkan sebelum tujuan bila dari bawah, sesudahnya bila dari atas, sehingga tidak bolak-balik.
                grid.insertBefore(dragged, from < to ? target.nextSibling : target);
                dirty = true;
                layout();
            });

            grid.addEventListener('drop', function (e) { if (dragged) { e.preventDefault(); } });

            grid.addEventListener('dragend', function () {
                if (!dragged) { return; }
                dragged.draggable = false;
                dragged.classList.remove('opacity-40', 'ring-2', 'ring-blue-600');
                dragged = null;
                if (dirty) { save(); }
            });

            // Alternatif tanpa drag: untuk keyboard dan layar sentuh.
            grid.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-move]');
                if (!btn) { return; }
                var tile = btn.closest('[data-id]');
                var step = Number(btn.dataset.move);
                var sibling = step < 0 ? tile.previousElementSibling : tile.nextElementSibling;
                if (!sibling) { return; }
                grid.insertBefore(tile, step < 0 ? sibling : sibling.nextSibling);
                layout();
                save();
                btn.focus();
            });
        })();
    </script>
@endif

<div class="mt-12">
    <x-admin-banner type="cta" slug="galeri" />
</div>
@endsection
