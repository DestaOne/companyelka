<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Activity;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Mengelola hero banner (type "hero") dan CTA (type "cta"); jenisnya ditentukan oleh route. */
class BannerController extends Controller
{
    private const NAMES = [Banner::HERO => 'Hero Banner', Banner::CTA => 'CTA'];

    // Jenis ("hero" atau "cta") datang dari default route, jadi dibaca lewat nama, bukan urutan argumen.
    public function edit(Request $request): View
    {
        $type = (string) $request->route('type');
        $slug = (string) $request->route('slug');

        return view('admin.banners.edit', [
            'type' => $type,
            'name' => self::NAMES[$type],
            'banner' => Banner::for($type, $slug),
        ]);
    }

    public function update(BannerRequest $request): RedirectResponse
    {
        $type = (string) $request->route('type');
        $slug = (string) $request->route('slug');
        $banner = Banner::for($type, $slug);
        $data = $request->safe()->only(['content', 'button_text']);

        if ($request->hasFile('image')) {
            $banner->deleteImageFile();
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        Activity::record('memperbarui '.($type === Banner::HERO ? 'hero banner' : 'CTA')." {$banner->definition['label']}");

        return redirect($banner->adminUrl())->with('success', self::NAMES[$type].' berhasil diperbarui.');
    }
}
