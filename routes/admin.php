<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomeCardController;
use App\Http\Controllers\Admin\HomeFeatureController;
use App\Http\Controllers\Admin\LogoController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('home', [HomeCardController::class, 'index'])->name('home.index');
        // Bagian "Kualitas. Keandalan. Pelayanan.": judul bagian dan tiga kartu.
        Route::get('home/keunggulan', [HomeFeatureController::class, 'editSection'])->name('home.features.section.edit');
        Route::put('home/keunggulan', [HomeFeatureController::class, 'updateSection'])->name('home.features.section.update');
        Route::get('home/keunggulan/{slot}', [HomeFeatureController::class, 'edit'])->whereNumber('slot')->name('home.features.edit');
        Route::put('home/keunggulan/{slot}', [HomeFeatureController::class, 'update'])->whereNumber('slot')->name('home.features.update');

        Route::get('home/{slot}', [HomeCardController::class, 'edit'])->whereNumber('slot')->name('home.edit');
        Route::put('home/{slot}', [HomeCardController::class, 'update'])->whereNumber('slot')->name('home.update');

        // Harus didaftarkan sebelum resource agar tidak tertangkap route update {service}.
        Route::put('layanan/urutan', [ServiceController::class, 'reorder'])->name('services.reorder');

        Route::resource('layanan', ServiceController::class)
            ->parameters(['layanan' => 'service'])
            ->except('show')
            ->names('services');

        Route::resource('galeri', GalleryController::class)
            ->parameters(['galeri' => 'galleryImage'])
            ->only(['index', 'create', 'store', 'update', 'destroy'])
            ->names('gallery');

        Route::get('pesan', [MessageController::class, 'index'])->name('messages.index');
        Route::get('pesan/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::delete('pesan/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('aktivitas', [ActivityController::class, 'index'])->name('activities.index');
        Route::post('notifikasi/baca', [ActivityController::class, 'markRead'])->name('activities.read');

        // Hero banner dan CTA diedit dari halaman menu masing-masing (Beranda, Layanan, dst.); form edit memakai controller yang sama.
        foreach (['hero-banner' => 'hero', 'cta' => 'cta'] as $path => $type) {
            Route::get("{$path}/{slug}", [BannerController::class, 'edit'])->defaults('type', $type)->name("banners.{$type}.edit");
            Route::put("{$path}/{slug}", [BannerController::class, 'update'])->defaults('type', $type)->name("banners.{$type}.update");
        }

        Route::get('logo', [LogoController::class, 'index'])->name('logo.index');
        Route::get('logo/{key}', [LogoController::class, 'edit'])->name('logo.edit');
        Route::put('logo/{key}', [LogoController::class, 'update'])->name('logo.update');
        Route::delete('logo/{key}', [LogoController::class, 'destroy'])->name('logo.destroy');

        // Tentang Kami: pratinjau halaman, bagian teks, dan kontak.
        Route::get('tentang', [AboutController::class, 'index'])->name('about.index');
        Route::get('tentang/kontak', [ContactController::class, 'edit'])->name('contact.edit');
        Route::put('tentang/kontak', [ContactController::class, 'update'])->name('contact.update');
        Route::get('tentang/bagian/{key}', [AboutController::class, 'edit'])->name('about.edit');
        Route::put('tentang/bagian/{key}', [AboutController::class, 'update'])->name('about.update');

        Route::get('profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profil', [ProfileController::class, 'update'])->name('profile.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});
