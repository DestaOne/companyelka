<?php

namespace App\Providers;

use App\Models\Service;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftar layanan di footer mengikuti data dari admin.
        View::composer('layouts.app', function ($view) {
            $view->with('footerServices', Service::published()->orderBy('id')->get());
        });
    }
}
