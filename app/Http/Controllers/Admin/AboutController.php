<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutSectionRequest;
use App\Models\AboutSection;
use App\Models\Activity;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Pratinjau halaman Tentang Kami; tiap bagian bisa dibuka untuk diedit. */
class AboutController extends Controller
{
    public function index(): View
    {
        return view('admin.about.index', [
            'sections' => AboutSection::allKeyed(),
            'contact' => Contact::current(),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('admin.about.edit', ['section' => AboutSection::for((string) $request->route('key'))]);
    }

    public function update(AboutSectionRequest $request): RedirectResponse
    {
        $section = AboutSection::for((string) $request->route('key'));
        $data = $request->safe()->only(['title', 'body', 'eyebrow']);

        if ($request->hasFile('image')) {
            $section->deleteImageFile();
            $data['image'] = $request->file('image')->store('about', 'public');
        }

        $section->update($data);

        Activity::record("memperbarui bagian “{$section->definition['label']}” di Tentang Kami");

        return redirect()->route('admin.about.index')->with('success', 'Bagian berhasil diperbarui.');
    }
}
