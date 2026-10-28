<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use App\Models\Activity;

class ActivityController extends Controller
{
    /** Seluruh log aktivitas admin, terbaru di atas. */
    public function index(): View
    {
        return view('admin.activities.index', [
            'activities' => Activity::with('user')->latest('id')->paginate(25),
        ]);
    }

    /** Menandai semua notifikasi sudah dibaca oleh admin yang sedang login. */
    public function markRead(Request $request): Response
    {
        $request->user()->forceFill(['activities_read_at' => now()])->save();

        return response()->noContent();
    }
}
