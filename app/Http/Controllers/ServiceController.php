<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('layanan.index', [
            'services' => Service::published()->ordered()->get(),
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->isPublished(), 404);

        return view('layanan.show', compact('service'));
    }
}
