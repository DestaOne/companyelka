<?php

namespace App\Providers;

use App\Models\AboutSection;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactMessage;
use App\Models\Logo;
use App\Models\Service;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Batas pengiriman formulir pesan per alamat IP; angkanya diatur di config/contact.php (CONTACT_MAX_PER_HOUR).
        RateLimiter::for('contact-message', fn (Request $request) => Limit::perHour(config('contact.max_per_hour'))->by($request->ip()));

        // Daftar layanan di footer mengikuti data dari admin.
        View::composer('layouts.app', function ($view) {
            $view->with([
                'footerServices' => Service::published()->ordered()->get(),
                'logos' => Logo::allKeyed(),
            ]);
        });

        // Ikon notifikasi di topbar admin: daftar aktivitas terbaru dan jumlah yang belum dibaca.
        View::composer('admin.layouts.app', function ($view) {
            $user = auth()->user();

            $view->with([
                'activities' => Activity::with('user')->latest('id')->limit(10)->get(),
                'unreadMessages' => ContactMessage::unread()->count(),
                'unreadActivities' => $user ? Activity::unreadCountFor($user) : 0,
                'activitiesReadAt' => $user?->activities_read_at ?? $user?->created_at,
            ]);
        });

        // Footer dan halaman Tentang Kami membaca kontak yang sama, jadi selalu seragam.
        View::composer(['layouts.app', 'tentang'], function ($view) {
            $view->with('contact', Contact::current());
        });

        View::composer('tentang', function ($view) {
            $view->with('about', AboutSection::allKeyed());
        });
    }
}
