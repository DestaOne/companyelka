<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Hero banner dan CTA yang bisa diedit admin. Tiap tempat pemakaian (mis. hero Beranda)
 * adalah satu baris, dibuat dari data awal di DEFINITIONS saat pertama kali dibutuhkan,
 * sehingga tampilan awal sama persis dengan sebelum bisa diedit.
 *
 * Isi teks (`content`) memakai format sederhana: "# Teks" = judul, baris lain = deskripsi.
 */
#[Fillable(['type', 'slug', 'image', 'content', 'button_text'])]
class Banner extends Model
{
    public const HERO = 'hero';

    public const CTA = 'cta';

    private const CTA_IMAGE = 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2069&auto=format&fit=crop';

    private const CTA_TEXT = 'Tim ahli kami siap memberikan konsultasi dan penawaran terbaik sesuai dengan anggaran dan spesifikasi perusahaan Anda.';

    /**
     * Data awal per tempat pemakaian. `admin_route` adalah halaman admin tempat banner ini diedit. `badge` dan `button_url` tidak bisa diedit admin.
     *
     * @var array<string, array<string, array<string, mixed>>>
     */
    public const DEFINITIONS = [
        self::HERO => [
            'home' => [
                'label' => 'Beranda',
                'page' => '/',
                'admin_route' => 'admin.home.index',
                'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2069&auto=format&fit=crop',
                'content' => "# Solusi Lengkap untuk Kebutuhan Operasional dan Ruang Kerja\nSolusi terpercaya untuk kebutuhan mesin kantor, komputer, perangkat lunak, dan furnitur. Membangun ekosistem kerja profesional untuk bisnis Anda.",
                'button_text' => 'Lihat Layanan Kami',
                'button_url' => '/layanan',
            ],
            'layanan' => [
                'label' => 'Halaman Layanan',
                'page' => '/layanan',
                'admin_route' => 'admin.services.index',
                'badge' => 'Katalog layanan',
                'image' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?q=80&w=2069&auto=format&fit=crop',
                'content' => "# Solusi Ekosistem Kerja\nDari perangkat keras berkinerja tinggi hingga tata ruang presisi. Kami menyediakan produk berkualitas unggul untuk memastikan setiap aspek operasional Anda berjalan tanpa hambatan.",
            ],
            'tentang' => [
                'label' => 'Halaman Tentang Kami',
                'page' => '/tentang-kami',
                'admin_route' => 'admin.about.index',
                'badge' => 'Tentang Perusahaan',
                'image' => 'https://images.unsplash.com/photo-1606836576983-8b458e75221d?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'content' => "# CV. Elka Mandiri\nMitra terpercaya dan penyedia solusi ekosistem ruang kerja profesional berpusat di Denpasar, Bali.",
            ],
        ],
        self::CTA => [
            'home' => [
                'label' => 'Beranda',
                'page' => '/',
                'admin_route' => 'admin.home.index',
                'image' => self::CTA_IMAGE,
                'content' => "# Siap Membangun Ruang Kerja Ideal Anda?\n".self::CTA_TEXT,
                'button_text' => 'Konsultasi Sekarang',
                'button_url' => '/tentang-kami#kontak',
            ],
            'layanan' => [
                'label' => 'Halaman Layanan',
                'page' => '/layanan',
                'admin_route' => 'admin.services.index',
                'image' => self::CTA_IMAGE,
                'content' => "# Tertarik Dengan Layanan Kami?\n".self::CTA_TEXT,
                'button_text' => 'Konsultasi Sekarang',
                'button_url' => '/tentang-kami#kontak',
            ],
            'layanan-detail' => [
                'label' => 'Detail Layanan',
                'page' => '/layanan',
                'admin_route' => 'admin.services.index',
                'image' => self::CTA_IMAGE,
                'content' => "# Tertarik Dengan Layanan Ini?\nKonsultasikan kebutuhan Anda. Tim kami akan menyiapkan penawaran terbaik yang disesuaikan dengan anggaran dan spesifikasi Anda.",
                'button_text' => 'Minta Penawaran',
                'button_url' => '/tentang-kami#kontak',
            ],
            'galeri' => [
                'label' => 'Halaman Galeri',
                'page' => '/galeri',
                'admin_route' => 'admin.gallery.index',
                'image' => self::CTA_IMAGE,
                'content' => "# Ingin Hasil Serupa untuk Ruang Kerja Anda?\n".self::CTA_TEXT,
                'button_text' => 'Konsultasi Sekarang',
                'button_url' => '/tentang-kami#kontak',
            ],
        ],
    ];

    /** @return array<string, mixed>|null */
    public static function definition(string $type, string $slug): ?array
    {
        return self::DEFINITIONS[$type][$slug] ?? null;
    }

    /** Banner untuk satu tempat pemakaian; dibuat dari data awal bila belum ada. */
    public static function for(string $type, string $slug): self
    {
        $definition = static::definition($type, $slug) ?? abort(404);

        return static::firstOrCreate(
            ['type' => $type, 'slug' => $slug],
            [
                'image' => $definition['image'],
                'content' => $definition['content'],
                'button_text' => $definition['button_text'] ?? null,
            ],
        );
    }

    /** @return array<string, mixed> */
    public function getDefinitionAttribute(): array
    {
        return static::definition($this->type, $this->slug) ?? [];
    }

    /** Id elemen kartu banner di halaman admin; dipakai agar admin kembali ke posisi kartunya setelah menyimpan. */
    public function anchor(): string
    {
        return "banner-{$this->type}-{$this->slug}";
    }

    /** Halaman admin tempat banner ini diedit, dengan posisi kartunya. */
    public function adminUrl(): string
    {
        return route($this->definition['admin_route']).'#'.$this->anchor();
    }

    public function hasButton(): bool
    {
        return isset($this->definition['button_url']);
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

    /**
     * Isi teks sebagai data (bukan HTML) agar view tetap meng-escape semuanya.
     *
     * @return list<array{type: 'heading'|'text', text: string}>
     */
    public function blocks(): array
    {
        $blocks = [];

        foreach (preg_split('/\R/', (string) $this->content) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $blocks[] = Str::startsWith($line, '# ')
                ? ['type' => 'heading', 'text' => trim(Str::after($line, '# '))]
                : ['type' => 'text', 'text' => $line];
        }

        return $blocks;
    }

    /** Judul pertama, dipakai sebagai ringkasan di daftar admin dan alt gambar. */
    public function title(): string
    {
        foreach ($this->blocks() as $block) {
            if ($block['type'] === 'heading') {
                return $block['text'];
            }
        }

        return '';
    }
}
