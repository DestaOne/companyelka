<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index');

Route::get('/layanan', [ServiceController::class, 'index'])->name('layanan.index');
Route::get('/layanan/{service:slug}', [ServiceController::class, 'show'])->name('layanan.show');

Route::view('/tentang-kami', 'tentang');
Route::get('/galeri', GalleryController::class)->name('galeri');

require __DIR__.'/admin.php';
