<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Logo navbar dan footer situs. `image` kosong berarti memakai logo bawaan di public/images,
 * sehingga tampilan awal tidak berubah sebelum admin mengunggah logo sendiri.
 */
#[Fillable(['key', 'image'])]
class Logo extends Model
{
    public const NAVBAR = 'navbar';

    public const FOOTER = 'footer';

    /**
     * label: nama di admin. default: berkas bawaan di public. dark: latar pratinjau.
     *
     * @var array<string, array<string, mixed>>
     */
    public const DEFINITIONS = [
        self::NAVBAR => [
            'label' => 'Logo Navbar',
            'description' => 'Tampil di pojok kiri menu atas setiap halaman.',
            'default' => 'images/logo.png',
            'dark' => false,
        ],
        self::FOOTER => [
            'label' => 'Logo Footer',
            'description' => 'Tampil di bagian bawah setiap halaman (latar gelap).',
            'default' => 'images/elkamandiri.jpeg',
            'dark' => true,
        ],
    ];

    /** @return array<string, mixed>|null */
    public static function definition(string $key): ?array
    {
        return self::DEFINITIONS[$key] ?? null;
    }

    public static function for(string $key): self
    {
        static::definition($key) ?? abort(404);

        return static::firstOrCreate(['key' => $key]);
    }

    /**
     * Semua logo, berkunci nama tempatnya.
     *
     * @return Collection<string, self>
     */
    public static function allKeyed(): Collection
    {
        $existing = static::all()->keyBy('key');

        foreach (array_keys(self::DEFINITIONS) as $key) {
            if (! $existing->has($key)) {
                $existing->put($key, static::for($key));
            }
        }

        return $existing;
    }

    /** @return array<string, mixed> */
    public function getDefinitionAttribute(): array
    {
        return static::definition($this->key) ?? [];
    }

    public function hasCustomImage(): bool
    {
        return filled($this->image);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->hasCustomImage()
            ? PublicDisk::url($this->image)
            : asset($this->definition['default']));
    }

    public function deleteImageFile(): void
    {
        if ($this->hasCustomImage()) {
            Storage::disk('public')->delete($this->image);
        }
    }
}
