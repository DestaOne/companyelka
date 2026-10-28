<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactRequest;
use App\Models\Activity;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function edit(): View
    {
        return view('admin.contact.edit', ['contact' => Contact::current()]);
    }

    public function update(ContactRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['maps_resolved'] = Contact::resolveMapsLink($data['maps_link'] ?? null);

        Contact::current()->update($data);

        Activity::record('memperbarui informasi kontak');

        $message = 'Kontak berhasil diperbarui.';

        // Link pendek yang tidak bisa dibuka: tautan alamat tetap memakai link itu, tetapi peta mengikuti teks alamat.
        if (filled($data['maps_link'] ?? null) && ! $data['maps_resolved'] && ! str_contains($data['maps_link'], '/maps/') && ! str_contains($data['maps_link'], 'google.com/maps')) {
            $message .= ' Link Google Maps tidak dapat dibaca lokasinya, jadi peta memakai teks alamat.';
        }

        return redirect()->route('admin.contact.edit')->with('success', $message);
    }
}
