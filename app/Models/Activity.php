<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/** Catatan aktivitas admin yang tampil di ikon notifikasi panel admin. */
#[Fillable(['user_id', 'description', 'contact_message_id'])]
class Activity extends Model
{
    /** Zona waktu tampilan log (database menyimpan UTC). */
    public const TIMEZONE = 'Asia/Makassar';

    public const TIMEZONE_LABEL = 'WITA';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(ContactMessage::class, 'contact_message_id');
    }

    /**
     * Aktivitas yang berasal dari pengunjung (pesan masuk), bukan dari admin.
     * Setelah pesannya dihapus, contact_message_id menjadi null, tetapi catatannya tetap dari pengunjung.
     */
    public function isVisitorMessage(): bool
    {
        return $this->contact_message_id !== null
            || ($this->user_id === null && str_starts_with($this->description, 'Pesan baru dari '));
    }

    /** Mencatat pesan baru dari pengunjung agar muncul di lonceng notifikasi. */
    public static function recordMessage(ContactMessage $message): void
    {
        static::create([
            'contact_message_id' => $message->id,
            'description' => "Pesan baru dari {$message->name}: {$message->subject}",
        ]);
    }

    /** Mencatat aktivitas admin yang sedang login, mis. "menambahkan 3 gambar ke galeri". */
    public static function record(string $description): void
    {
        static::create(['user_id' => Auth::id(), 'description' => $description]);
    }

    /** Jumlah aktivitas yang terjadi setelah admin terakhir membuka notifikasi. */
    public static function unreadCountFor(User $user): int
    {
        return static::where('created_at', '>', $user->activities_read_at ?? $user->created_at)->count();
    }
}
