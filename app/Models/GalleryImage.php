<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['image', 'thumbnail', 'title', 'position'])]
class GalleryImage extends Model
{
    use HasFactory;

    /** Ukuran kotak bento grid; berulang setiap 8 gambar, dipakai halaman Galeri dan pratinjau admin. */
    public const GRID_PATTERN = [
        'md:col-span-5 md:row-span-2',
        'md:col-span-7 md:row-span-1',
        'md:col-span-4 md:row-span-1',
        'md:col-span-3 md:row-span-1',
        'md:col-span-3 md:row-span-1',
        'md:col-span-5 md:row-span-1',
        'md:col-span-4 md:row-span-2',
        'md:col-span-8 md:row-span-1',
    ];

    protected static function booted(): void
    {
        // Gambar baru masuk di posisi paling akhir.
        static::creating(function (self $image) {
            if (! $image->position) {
                $image->position = (int) static::max('position') + 1;
            }
        });
    }

    /** Urutan tampil: posisi yang diatur admin, lalu id sebagai pembeda. */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

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
            : PublicDisk::url($path);
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
