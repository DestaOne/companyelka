<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeCardRequest;
use App\Models\Activity;
use App\Models\HomeCard;
use App\Models\HomeFeature;
use App\Models\HomeSection;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeCardController extends Controller
{
    public function index(): View
    {
        return view('admin.home.index', [
            'cards' => HomeCard::allSlots(),
            'featureSection' => HomeSection::for(HomeSection::FEATURES),
            'features' => HomeFeature::allSlots(),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('admin.home.edit', [
            'card' => HomeCard::forSlot((int) $request->route('slot')),
            'services' => Service::ordered()->get(['id', 'title', 'status']),
        ]);
    }

    public function update(HomeCardRequest $request): RedirectResponse
    {
        $card = HomeCard::forSlot((int) $request->route('slot'));

        $card->update([
            ...$request->safe()->only(['eyebrow', 'title', 'description']),
            'service_id' => $request->validated('service_id'),
        ]);

        Activity::record("memperbarui kotak layanan Beranda “{$card->title}”");

        return redirect()->route('admin.home.index')->with('success', 'Kotak Beranda berhasil diperbarui.');
    }
}
