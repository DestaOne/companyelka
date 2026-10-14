<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
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
        Route::resource('layanan', ServiceController::class)
            ->parameters(['layanan' => 'service'])
            ->except('show')
            ->names('services');

        Route::resource('galeri', GalleryController::class)
            ->parameters(['galeri' => 'galleryImage'])
            ->only(['index', 'create', 'store', 'destroy'])
            ->names('gallery');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});
