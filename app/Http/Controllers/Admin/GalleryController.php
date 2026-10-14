<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;
use App\Models\GalleryImage;
use App\Support\ImageProcessor;
use Illuminate\Http\RedirectResponse;
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
        foreach ($request->file('images') as $file) {
            GalleryImage::create(ImageProcessor::store($file, 'gallery'));
        }

        $count = count($request->file('images'));

        return redirect()->route('admin.gallery.index')->with('success', "{$count} gambar berhasil ditambahkan.");
    }

    public function destroy(GalleryImage $galleryImage): RedirectResponse
    {
        $galleryImage->deleteFiles();
        $galleryImage->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Gambar berhasil dihapus.');
    }
}
