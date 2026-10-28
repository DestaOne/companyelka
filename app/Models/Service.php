<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'image', 'summary', 'detail', 'status', 'position'])]
class Service extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'terbit';

    /** Layanan baru otomatis ditempatkan di urutan paling akhir. */
    protected static function booted(): void
    {
        static::creating(function (self $service) {
            $service->position ??= (int) static::max('position') + 1;
        });
    }

    /** Urutan tampil yang diatur admin; id sebagai pembeda bila posisi sama. */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

    /** Hanya layanan berstatus terbit yang tampil di situs publik. */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * URL gambar: tautan eksternal dipakai apa adanya, selain itu dianggap berkas di disk "public".
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->hasLocalImage()
            ? Storage::disk('public')->url($this->image)
            : $this->image);
    }

    public function hasLocalImage(): bool
    {
        return ! Str::startsWith($this->image, ['http://', 'https://']);
    }

    public function deleteImageFile(): void
    {
        if ($this->hasLocalImage()) {
            Storage::disk('public')->delete($this->image);
        }
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'layanan';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /**
     * Mengubah deskripsi detail menjadi blok terstruktur.
     *   "# Teks" -> judul bergaris, "- Teks" -> item list, lainnya -> paragraf.
     * Dikembalikan sebagai data (bukan HTML) agar view tetap meng-escape seluruh isi.
     *
     * @return list<array{type: string, text?: string, items?: list<string>}>
     */
    public function detailBlocks(): array
    {
        $blocks = [];

        foreach (preg_split('/\R/', (string) $this->detail) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (Str::startsWith($line, '# ')) {
                $blocks[] = ['type' => 'heading', 'text' => trim(Str::after($line, '# '))];
            } elseif (Str::startsWith($line, '- ')) {
                $text = trim(Str::after($line, '- '));
                $last = array_key_last($blocks);

                if ($last !== null && $blocks[$last]['type'] === 'list') {
                    $blocks[$last]['items'][] = $text;
                } else {
                    $blocks[] = ['type' => 'list', 'items' => [$text]];
                }
            } else {
                $blocks[] = ['type' => 'text', 'text' => $line];
            }
        }

        return $blocks;
    }
}
