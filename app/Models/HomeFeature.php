<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Tiga kartu "Kualitas. Keandalan. Pelayanan." di Beranda; dibuat dari data awal saat pertama kali dibutuhkan. */
#[Fillable(['slot', 'image', 'title', 'description'])]
class HomeFeature extends Model
{
    public const SLOTS = [1, 2, 3];

    /** @var array<int, array{image: string, title: string, description: string}> */
    private const DEFAULTS = [
        1 => [
            'image' => 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?q=80&w=2070&auto=format&fit=crop',
            'title' => 'Solusi Terpadu',
            'description' => 'Satu pintu untuk semua kebutuhan. Dari instalasi piranti lunak, penyediaan komputer, hingga furnitur dalam satu ekosistem yang selaras.',
        ],
        2 => [
            'image' => 'https://images.unsplash.com/photo-1637073849563-5b0ac780ec34?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'title' => 'Standar Profesional',
            'description' => 'Kami hanya menyediakan produk dengan standar industri terbaik untuk memastikan keawetan, keamanan, dan efisiensi jangka panjang.',
        ],
        3 => [
            'image' => 'https://images.unsplash.com/photo-1766066014237-00645c74e9c6?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'title' => 'Dukungan Responsif',
            'description' => 'Layanan pelanggan dan purna jual yang selalu siap membantu Anda menangani segala masalah teknis maupun konsultasi produk.',
        ],
    ];

    /**
     * Ketiga kartu berurutan; yang belum ada dibuat dari data awal.
     *
     * @return Collection<int, self>
     */
    public static function allSlots(): Collection
    {
        $existing = static::all()->keyBy('slot');

        foreach (self::SLOTS as $slot) {
            if (! $existing->has($slot)) {
                $existing->put($slot, static::create(['slot' => $slot] + self::DEFAULTS[$slot]));
            }
        }

        return $existing->sortKeys()->values();
    }

    public static function forSlot(int $slot): self
    {
        abort_unless(in_array($slot, self::SLOTS, true), 404);

        return static::allSlots()->firstWhere('slot', $slot);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->hasLocalImage()
            ? PublicDisk::url($this->image)
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
}
