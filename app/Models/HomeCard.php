<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Empat kotak "Layanan Kami" di Beranda. Kotak 1-3 berupa kartu layanan; kotak 4 adalah kartu
 * biru ajakan menuju katalog. Barisnya dibuat dari data awal saat pertama kali dibutuhkan.
 */
#[Fillable(['slot', 'eyebrow', 'title', 'description', 'service_id'])]
class HomeCard extends Model
{
    public const SLOTS = [1, 2, 3, 4];

    /** Kotak terakhir memakai gaya kartu biru dan tidak diberi nomor. */
    public const CTA_SLOT = 4;

    /**
     * Data awal; `service` adalah slug layanan tujuan awal.
     *
     * @var array<int, array<string, string|null>>
     */
    private const DEFAULTS = [
        1 => ['eyebrow' => 'IT & Perangkat Lunak', 'title' => 'Infrastruktur Digital', 'description' => 'Pengadaan PC, laptop, server, serta instalasi dan konfigurasi jaringan tingkat lanjut.', 'service' => 'komputer'],
        2 => ['eyebrow' => 'Mesin Kantor', 'title' => 'Efisiensi Dokumen', 'description' => 'Perangkat cetak skala besar, mesin fotokopi, dan sistem absensi biometrik.', 'service' => 'mesin-kantor'],
        3 => ['eyebrow' => 'Furnitur', 'title' => 'Ruang Kerja Ergonomis', 'description' => 'Desain dan suplai partisi ruangan, meja, kursi direksi, serta penyimpanan arsip.', 'service' => 'furniture'],
        4 => ['eyebrow' => 'Eksplorasi Katalog', 'title' => 'Lihat layanan lainnya', 'description' => null, 'service' => null],
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Keempat kotak berurutan; yang belum ada dibuat dari data awal.
     *
     * @return Collection<int, self>
     */
    public static function allSlots(): Collection
    {
        $existing = static::with('service')->get()->keyBy('slot');

        foreach (self::SLOTS as $slot) {
            if ($existing->has($slot)) {
                continue;
            }

            $default = self::DEFAULTS[$slot];

            $card = static::create([
                'slot' => $slot,
                'eyebrow' => $default['eyebrow'],
                'title' => $default['title'],
                'description' => $default['description'],
                'service_id' => $default['service'] ? Service::where('slug', $default['service'])->value('id') : null,
            ]);

            $existing->put($slot, $card->load('service'));
        }

        return $existing->sortKeys()->values();
    }

    public static function forSlot(int $slot): self
    {
        abort_unless(in_array($slot, self::SLOTS, true), 404);

        return static::allSlots()->firstWhere('slot', $slot);
    }

    public function isCta(): bool
    {
        return $this->slot === self::CTA_SLOT;
    }

    /** Teks biru kecil; kotak layanan diberi nomor urut otomatis, mis. "01 / Mesin Kantor". */
    public function eyebrowLabel(): string
    {
        return $this->isCta() ? $this->eyebrow : sprintf('%02d / %s', $this->slot, $this->eyebrow);
    }

    /** Tujuan kotak di situs: halaman layanan yang dipilih bila sudah terbit, selain itu daftar semua layanan. */
    public function url(): string
    {
        return $this->service?->isPublished()
            ? route('layanan.show', $this->service)
            : route('layanan.index');
    }
}
