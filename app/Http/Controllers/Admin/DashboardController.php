<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\GalleryImage;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $draftCount = Service::where('status', Service::STATUS_DRAFT)->count();

        return view('admin.dashboard', [
            'serviceCount' => Service::count(),
            'publishedCount' => Service::published()->count(),
            'draftCount' => $draftCount,
            'galleryCount' => GalleryImage::count(),
            'adminCount' => User::where('is_admin', true)->count(),
            'drafts' => Service::where('status', Service::STATUS_DRAFT)->ordered()->limit(5)->get(),
            'recentActivities' => Activity::with('user')->latest('id')->limit(5)->get(),
        ]);
    }
}
