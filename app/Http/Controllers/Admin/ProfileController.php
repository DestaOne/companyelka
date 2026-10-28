<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileRequest;
use App\Support\ImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email']));

        if ($request->hasFile('photo')) {
            $user->deletePhotoFile();
            $user->photo = ImageProcessor::storeAvatar($request->file('photo'), 'avatars');
        } elseif ($request->boolean('remove_photo')) {
            $user->deletePhotoFile();
            $user->photo = null;
        }

        if ($request->changesPassword()) {
            // Cast "hashed" pada model yang meng-hash password.
            $user->password = $request->validated('password');
        }

        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
