<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Teks bagian-bagian halaman Tentang Kami yang bisa diedit admin. Tiap bagian adalah satu baris,
 * dibuat dari data awal di DEFINITIONS saat pertama kali dibutuhkan sehingga tampilan awal tidak berubah.
 *
 * `body` berisi satu baris per paragraf (bagian "paragraphs"), satu paragraf (bagian "text"),
 * atau satu baris per butir daftar (bagian "list").
 */
#[Fillable(['key', 'image', 'eyebrow', 'title', 'body'])]
class AboutSection extends Model
{
    public const BACKGROUND = 'background';

    public const PRINCIPLES = 'principles';

    public const VISION = 'vision';

    public const MISSION = 'mission';

    public const CONTACT = 'contact';

    /**
     * label: nama di admin. body: jenis isi (paragraphs, text, list). image/eyebrow: kolom yang bisa diedit.
     *
     * @var array<string, array<string, mixed>>
     */
    public const DEFINITIONS = [
        self::BACKGROUND => [
            'label' => 'Latar Belakang',
            'body' => 'paragraphs',
            'image' => true,
            'eyebrow' => true,
            'defaults' => [
                'image' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?q=80&w=2069&auto=format&fit=crop',
                'eyebrow' => 'Latar Belakang',
                'title' => 'Dedikasi untuk Efisiensi dan Kemajuan Bisnis Anda',
                'body' => "CV Elka Mandiri adalah perusahaan penyedia solusi terpadu yang berpusat di Denpasar, Bali. Kami hadir untuk menjawab tantangan dunia bisnis modern yang menuntut efisiensi, kecepatan, dan kenyamanan dalam setiap aspek operasional ruang kerja.\nBerbekal komitmen pelayanan tinggi, kami mengkhususkan diri pada perdagangan eceran mesin kantor, komputer dan perlengkapannya, peralatan listrik dan penerangan, piranti lunak (software), hingga penyediaan furnitur premium guna memastikan ekosistem kerja perusahaan Anda berjalan optimal.",
            ],
        ],
        self::PRINCIPLES => [
            'label' => 'Landasan & Tujuan',
            'body' => 'text',
            'defaults' => [
                'title' => 'Landasan & Tujuan',
                'body' => 'Prinsip dasar yang mengarahkan setiap langkah pelayanan kami kepada para mitra korporat.',
            ],
        ],
        self::VISION => [
            'label' => 'Visi Perusahaan',
            'body' => 'text',
            'defaults' => [
                'title' => 'Visi Perusahaan',
                'body' => 'Menjadi perusahaan penyedia kebutuhan operasional kantor dan ruang kerja terdepan di Bali yang dikenal luas akan kualitas produk unggulan, keandalan layanan, dan solusi inovatif bagi setiap mitra bisnis.',
            ],
        ],
        self::MISSION => [
            'label' => 'Misi Utama',
            'body' => 'list',
            'defaults' => [
                'title' => 'Misi Utama',
                'body' => "Menyediakan rangkaian produk mesin kantor, komputer, dan furnitur dengan standar kualitas terbaik.\nMemberikan layanan purna jual yang responsif dan solutif demi kepuasan pelanggan secara konsisten.\nMembangun kemitraan jangka panjang yang didasari pada kepercayaan, kejujuran, dan profesionalisme.",
            ],
        ],
        self::CONTACT => [
            'label' => 'Teks Kontak dan Lokasi',
            'body' => 'text',
            'eyebrow' => true,
            'defaults' => [
                'eyebrow' => 'Informasi Kantor',
                'title' => 'Kontak dan Lokasi',
                'body' => 'Kawasan strategis yang memudahkan koordinasi dan konsultasi langsung mengenai kebutuhan operasional bisnis Anda.',
            ],
        ],
    ];

    /** @return array<string, mixed>|null */
    public static function definition(string $key): ?array
    {
        return self::DEFINITIONS[$key] ?? null;
    }

    /** Bagian untuk kunci ini; dibuat dari data awal bila belum ada. */
    public static function for(string $key): self
    {
        $definition = static::definition($key) ?? abort(404);

        return static::firstOrCreate(['key' => $key], $definition['defaults']);
    }

    /**
     * Semua bagian, berkunci nama bagian.
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

    /**
     * Isi `body` sebagai baris-baris tidak kosong (paragraf atau butir daftar).
     *
     * @return list<string>
     */
    public function lines(): array
    {
        return array_values(array_filter(
            array_map('trim', preg_split('/\R/', (string) $this->body)),
            fn (string $line) => $line !== '',
        ));
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image === null || ! $this->hasLocalImage()
            ? $this->image
            : PublicDisk::url($this->image));
    }

    public function hasLocalImage(): bool
    {
        return $this->image !== null && ! Str::startsWith($this->image, ['http://', 'https://']);
    }

    public function deleteImageFile(): void
    {
        if ($this->hasLocalImage()) {
            Storage::disk('public')->delete($this->image);
        }
    }
}
