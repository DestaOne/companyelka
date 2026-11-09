<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Pesan yang dikirim pengunjung lewat formulir di halaman Tentang Kami. */
#[Fillable(['name', 'phone', 'email', 'subject', 'body', 'read_at'])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function scopeUnread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (! $this->isRead()) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /** Tautan wa.me ke pengirim, dengan kalimat balasan pembuka yang sudah terisi. */
    public function whatsappUrl(): string
    {
        $text = "Halo {$this->name}, terima kasih telah menghubungi CV Elka Mandiri terkait “{$this->subject}”.";

        return 'https://wa.me/'.Contact::normalizeWhatsapp($this->phone).'?text='.rawurlencode($text);
    }

    /** Isi email yang sama seperti di formulir, ditambah data pengirim. */
    private function emailBody(): string
    {
        return "{$this->body}\r\n\r\nNama: {$this->name}\r\nWhatsApp: {$this->phone}\r\nEmail: {$this->email}";
    }

    /** Tautan mailto: ke email perusahaan, untuk aplikasi email (ponsel) atau klien email bawaan. */
    public function mailtoUrl(string $to): string
    {
        return 'mailto:'.$to.'?subject='.rawurlencode($this->subject).'&body='.rawurlencode($this->emailBody());
    }

    /** Jendela tulis pesan Gmail di web, dengan tujuan, subjek, dan isi yang sudah terisi. */
    public function gmailUrl(string $to): string
    {
        return 'https://mail.google.com/mail/?view=cm&fs=1&to='.rawurlencode($to)
            .'&su='.rawurlencode($this->subject).'&body='.rawurlencode($this->emailBody());
    }

    /** Jendela tulis pesan Outlook di web. */
    public function outlookUrl(string $to): string
    {
        return 'https://outlook.live.com/mail/0/deeplink/compose?to='.rawurlencode($to)
            .'&subject='.rawurlencode($this->subject).'&body='.rawurlencode($this->emailBody());
    }
}
