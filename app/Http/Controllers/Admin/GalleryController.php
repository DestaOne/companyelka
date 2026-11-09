<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;
use App\Models\Activity;
use App\Models\GalleryImage;
use App\Support\ImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('admin.gallery.index', [
            'images' => GalleryImage::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.gallery.create');
    }

    public function store(GalleryRequest $request): RedirectResponse
    {
        $titles = $request->input('titles', []);

        foreach ($request->file('images') as $index => $file) {
            GalleryImage::create([
                ...ImageProcessor::store($file, 'gallery'),
                'title' => filled($titles[$index] ?? null) ? trim($titles[$index]) : null,
            ]);
        }

        $count = count($request->file('images'));

        Activity::record("menambahkan {$count} gambar ke galeri");

        return redirect()->route('admin.gallery.index')->with('success', "{$count} gambar berhasil ditambahkan.");
    }

    public function update(Request $request, GalleryImage $galleryImage): RedirectResponse
    {
        $data = $request->validate(['title' => ['nullable', 'string', 'max:100']], [
            'title.max' => 'Judul gambar maksimal 100 karakter.',
        ]);

        $galleryImage->update(['title' => filled($data['title'] ?? null) ? trim($data['title']) : null]);

        Activity::record('mengubah judul gambar galeri');

        return redirect()->route('admin.gallery.index')->with('success', 'Judul gambar diperbarui.');
    }

    /** Menyimpan urutan baru hasil drag and drop; `order` berisi seluruh id gambar sesuai urutan yang diinginkan. */
    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'distinct', 'exists:gallery_images,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach (array_values($data['order']) as $index => $id) {
                GalleryImage::whereKey($id)->update(['position' => $index + 1]);
            }
        });

        Activity::record('mengatur ulang posisi gambar galeri');

        return response()->json(['message' => 'Urutan tersimpan.']);
    }

    public function destroy(GalleryImage $galleryImage): RedirectResponse
    {
        $galleryImage->deleteFiles();
        $galleryImage->delete();

        Activity::record('menghapus gambar dari galeri');

        return redirect()->route('admin.gallery.index')->with('success', 'Gambar berhasil dihapus.');
    }
}
