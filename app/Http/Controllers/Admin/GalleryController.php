<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;
use App\Models\Activity;
use App\Models\GalleryImage;
use App\Support\ImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('admin.gallery.index', [
            'images' => GalleryImage::orderBy('id')->get(),
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

    public function destroy(GalleryImage $galleryImage): RedirectResponse
    {
        $galleryImage->deleteFiles();
        $galleryImage->delete();

        Activity::record('menghapus gambar dari galeri');

        return redirect()->route('admin.gallery.index')->with('success', 'Gambar berhasil dihapus.');
    }
}
