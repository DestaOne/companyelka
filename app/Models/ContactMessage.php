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
}
