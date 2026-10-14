<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['image', 'thumbnail'])]
class GalleryImage extends Model
{
    use HasFactory;

    /** URL versi besar (untuk preview zoom). */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => static::resolveUrl($this->image));
    }

    /** URL thumbnail untuk tampilan di halaman; memakai versi besar bila tidak ada thumbnail. */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn () => static::resolveUrl($this->thumbnail ?: $this->image));
    }

    /** Tautan eksternal dipakai apa adanya, selain itu dianggap berkas di disk "public". */
    protected static function resolveUrl(string $path): string
    {
        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }

    public function deleteFiles(): void
    {
        foreach ([$this->image, $this->thumbnail] as $path) {
            if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
