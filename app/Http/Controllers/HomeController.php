<?php

namespace App\Http\Controllers;

use App\Models\HomeCard;
use App\Models\HomeFeature;
use App\Models\HomeSection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('index', [
            // Empat kotak layanan; tiap kotak hanya menaut ke layanan yang terbit.
            'cards' => HomeCard::allSlots(),
            'featureSection' => HomeSection::for(HomeSection::FEATURES),
            'features' => HomeFeature::allSlots(),
        ]);
    }
}
