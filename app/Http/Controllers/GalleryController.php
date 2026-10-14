<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __invoke(): View
    {
        return view('galeri', [
            'images' => GalleryImage::orderBy('id')->get(),
        ]);
    }
}
