<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LogoRequest;
use App\Models\Activity;
use App\Models\Logo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogoController extends Controller
{
    public function index(): View
    {
        return view('admin.logo.index', ['logos' => Logo::allKeyed()]);
    }

    public function edit(Request $request): View
    {
        return view('admin.logo.edit', ['logo' => Logo::for((string) $request->route('key'))]);
    }

    public function update(LogoRequest $request): RedirectResponse
    {
        $logo = Logo::for((string) $request->route('key'));

        $logo->deleteImageFile();
        $logo->update(['image' => $request->file('image')->store('logos', 'public')]);

        Activity::record("mengganti {$logo->definition['label']}");

        return redirect()->route('admin.logo.index')->with('success', $logo->definition['label'].' berhasil diganti.');
    }

    /** Mengembalikan logo bawaan dan menghapus berkas yang diunggah. */
    public function destroy(Request $request): RedirectResponse
    {
        $logo = Logo::for((string) $request->route('key'));

        $logo->deleteImageFile();
        $logo->update(['image' => null]);

        Activity::record("mengembalikan {$logo->definition['label']} ke bawaan");

        return redirect()->route('admin.logo.index')->with('success', $logo->definition['label'].' dikembalikan ke bawaan.');
    }
}
