{{--
    Preview gambar layar penuh. Tambahkan atribut `data-zoomable` pada <img> mana pun agar bisa dibuka.
    - Scroll / double click : zoom in-out
    - Drag                  : geser saat di-zoom
    - Pinch (layar sentuh)  : zoom
    - Esc / klik latar / tombol X : tutup
--}}
<style>
    img[data-zoomable] { cursor: zoom-in; }
    #lightbox-img { user-select: none; -webkit-user-drag: none; }
</style>

<div id="lightbox" class="fixed inset-0 z-[100] hidden bg-slate-950/90" role="dialog" aria-modal="true" aria-label="Preview gambar">
    <div id="lightbox-stage" class="absolute inset-0 flex items-center justify-center overflow-hidden" style="touch-action: none;">
        <img id="lightbox-img" alt="" class="max-h-[90vh] max-w-[95vw] object-contain" draggable="false">
    </div>

    <button type="button" id="lightbox-close" aria-label="Tutup preview"
            class="absolute top-4 right-4 flex h-11 w-11 items-center justify-center bg-white/10 text-white hover:bg-white/20 transition-colors">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="square" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
    </button>

    <p class="pointer-events-none absolute bottom-4 inset-x-0 px-4 text-center text-xs text-white/70">
        Scroll untuk zoom &middot; seret untuk menggeser &middot; Esc untuk menutup
    </p>
</div>

<script>
(function () {
    var box = document.getElementById('lightbox');
    var stage = document.getElementById('lightbox-stage');
    var img = document.getElementById('lightbox-img');
    var MIN = 1, MAX = 3;
    var s = 1, tx = 0, ty = 0;
    var pointers = new Map();
    var pinchDist = 0, moved = false;

    // animate=true: transisi halus (scroll / double click). false: langsung mengikuti jari/kursor (drag, pinch).
    function apply(animate) {
        img.style.transition = animate ? 'transform 220ms cubic-bezier(0.22, 0.61, 0.36, 1)' : 'none';
        img.style.transform = 'translate(' + tx + 'px,' + ty + 'px) scale(' + s + ')';
        img.style.cursor = s > 1 ? 'grab' : 'zoom-in';
    }

    function clamp() {
        var mx = Math.max(0, (img.offsetWidth * s - stage.clientWidth) / 2);
        var my = Math.max(0, (img.offsetHeight * s - stage.clientHeight) / 2);
        tx = Math.min(mx, Math.max(-mx, tx));
        ty = Math.min(my, Math.max(-my, ty));
    }

    // Zoom ke skala baru dengan titik (cx, cy) tetap berada di bawah kursor.
    function zoomAt(ns, cx, cy, animate) {
        ns = Math.min(MAX, Math.max(MIN, ns));
        var r = stage.getBoundingClientRect();
        var px = cx - (r.left + r.width / 2);
        var py = cy - (r.top + r.height / 2);
        tx = px - (px - tx) * (ns / s);
        ty = py - (py - ty) * (ns / s);
        s = ns;
        if (s === 1) { tx = 0; ty = 0; }
        clamp();
        apply(animate);
    }

    var loadToken = 0;

    // thumb tampil langsung; bila ada versi besar (data-full), diganti begitu selesai dimuat.
    function open(src, alt, full) {
        var token = ++loadToken;
        img.src = src;
        if (full && full !== src) {
            var big = new Image();
            big.onload = function () { if (token === loadToken) { img.src = full; } };
            big.src = full;
        }
        img.alt = alt || '';
        s = 1; tx = 0; ty = 0; apply(false);
        box.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        loadToken++;
        box.classList.add('hidden');
        document.body.style.overflow = '';
        pointers.clear();
        img.removeAttribute('src');
    }

    document.addEventListener('click', function (e) {
        var t = e.target.closest && e.target.closest('img[data-zoomable]');
        if (t) { open(t.currentSrc || t.src, t.alt, t.dataset.full); }
    });

    document.getElementById('lightbox-close').addEventListener('click', close);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !box.classList.contains('hidden')) { close(); }
    });

    stage.addEventListener('wheel', function (e) {
        e.preventDefault();
        zoomAt(s * Math.exp(-e.deltaY * 0.002), e.clientX, e.clientY, true);
    }, { passive: false });

    img.addEventListener('dblclick', function (e) {
        if (s > 1) { zoomAt(1, e.clientX, e.clientY, true); } else { zoomAt(2, e.clientX, e.clientY, true); }
    });

    stage.addEventListener('pointerdown', function (e) {
        pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
        stage.setPointerCapture(e.pointerId);
        moved = false;
        if (pointers.size === 2) {
            var p = Array.from(pointers.values());
            pinchDist = Math.hypot(p[0].x - p[1].x, p[0].y - p[1].y);
        }
    });

    stage.addEventListener('pointermove', function (e) {
        var prev = pointers.get(e.pointerId);
        if (!prev) { return; }
        var cur = { x: e.clientX, y: e.clientY };

        if (pointers.size === 2) {
            pointers.set(e.pointerId, cur);
            var p = Array.from(pointers.values());
            var d = Math.hypot(p[0].x - p[1].x, p[0].y - p[1].y);
            if (pinchDist) { zoomAt(s * (d / pinchDist), (p[0].x + p[1].x) / 2, (p[0].y + p[1].y) / 2, false); }
            pinchDist = d;
            moved = true;
        } else if (s > 1) {
            tx += cur.x - prev.x;
            ty += cur.y - prev.y;
            clamp();
            apply(false);
            pointers.set(e.pointerId, cur);
            moved = true;
        }
    });

    function release(e) {
        pointers.delete(e.pointerId);
        pinchDist = 0;
        // Klik pada latar (bukan gambar, bukan seretan) menutup preview.
        if (e.type === 'pointerup' && !moved && e.target === stage) { close(); }
    }
    stage.addEventListener('pointerup', release);
    stage.addEventListener('pointercancel', release);
})();
</script>
