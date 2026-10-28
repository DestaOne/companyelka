<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeFeatureRequest;
use App\Http\Requests\Admin\HomeSectionRequest;
use App\Models\Activity;
use App\Models\HomeFeature;
use App\Models\HomeSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Bagian "Kualitas. Keandalan. Pelayanan." di Beranda: judul bagian dan tiga kartunya. */
class HomeFeatureController extends Controller
{
    public function editSection(): View
    {
        return view('admin.home.section-edit', ['section' => HomeSection::for(HomeSection::FEATURES)]);
    }

    public function updateSection(HomeSectionRequest $request): RedirectResponse
    {
        HomeSection::for(HomeSection::FEATURES)->update($request->validated());

        Activity::record('memperbarui judul bagian “'.$request->validated('title').'” di Beranda');

        return redirect()->route('admin.home.index')->with('success', 'Judul bagian berhasil diperbarui.');
    }

    public function edit(Request $request): View
    {
        return view('admin.home.feature-edit', ['feature' => HomeFeature::forSlot((int) $request->route('slot'))]);
    }

    public function update(HomeFeatureRequest $request): RedirectResponse
    {
        $feature = HomeFeature::forSlot((int) $request->route('slot'));
        $data = $request->safe()->only(['title', 'description']);

        if ($request->hasFile('image')) {
            $feature->deleteImageFile();
            $data['image'] = $request->file('image')->store('home', 'public');
        }

        $feature->update($data);

        Activity::record("memperbarui kartu “{$feature->title}” di Beranda");

        return redirect()->route('admin.home.index')->with('success', 'Kartu berhasil diperbarui.');
    }
}
