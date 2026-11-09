<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class ContactMessageController extends Controller
{
    public function store(ContactMessageRequest $request): RedirectResponse
    {
        // Robot yang mengisi kolom jebakan diberi respons sukses agar tidak mencoba lagi, tanpa disimpan.
        if (! $request->isSpam()) {
            $message = ContactMessage::create($request->validated());

            Activity::recordMessage($message);

            $to = Contact::current()->email;
            $mail = [
                'mailto_url' => $message->mailtoUrl($to),
                'gmail_url' => $message->gmailUrl($to),
                'outlook_url' => $message->outlookUrl($to),
            ];
        }

        return redirect(url('/tentang-kami').'#kontak')
            ->with('message_sent', 'Terima kasih, pesan Anda sudah terkirim. Tim kami akan segera menghubungi Anda.')
            ->with($mail ?? []);
    }
}
