<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ContactMessage;
use App\Models\GalleryImage;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Item per daftar di dashboard; 6 pertama tampil penuh, sisanya digulir. */
    private const LIST_LIMIT = 20;

    public function __invoke(): View
    {
        $draftCount = Service::where('status', Service::STATUS_DRAFT)->count();

        return view('admin.dashboard', [
            'serviceCount' => Service::count(),
            'publishedCount' => Service::published()->count(),
            'draftCount' => $draftCount,
            'galleryCount' => GalleryImage::count(),
            'unreadMessageCount' => ContactMessage::unread()->count(),
            'unreadMessages' => ContactMessage::unread()->latest('id')->limit(self::LIST_LIMIT)->get(),
            'drafts' => Service::where('status', Service::STATUS_DRAFT)->ordered()->limit(self::LIST_LIMIT)->get(),
            'recentActivities' => Activity::with('user')->latest('id')->limit(5)->get(),
        ]);
    }
}
