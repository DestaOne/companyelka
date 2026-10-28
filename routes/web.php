<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/layanan', [ServiceController::class, 'index'])->name('layanan.index');
Route::get('/layanan/{service:slug}', [ServiceController::class, 'show'])->name('layanan.show');

Route::view('/tentang-kami', 'tentang');
Route::post('/kirim-pesan', [ContactMessageController::class, 'store'])->middleware('throttle:contact-message')->name('pesan.store');
Route::get('/galeri', GalleryController::class)->name('galeri');

require __DIR__.'/admin.php';
