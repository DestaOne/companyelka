<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

/**
 * Memproses gambar unggahan menjadi dua berkas WebP:
 * versi besar (untuk preview zoom) dan thumbnail (untuk tampilan di halaman).
 * Orientasi EXIF dari foto ponsel otomatis diperbaiki oleh Intervention.
 */
class ImageProcessor
{
    public const FULL_WIDTH = 2400;

    public const THUMB_WIDTH = 1000;

    public const AVATAR_SIZE = 256;

    /**
     * @return array{image: string, thumbnail: string} path relatif pada disk "public"
     */
    public static function store(UploadedFile $file, string $directory): array
    {
        // Foto beresolusi tinggi butuh memori besar saat didekode; naikkan batas hanya untuk proses ini.
        $memory = (int) ini_parse_quantity((string) ini_get('memory_limit'));
        if ($memory > 0 && $memory < 512 * 1024 * 1024) {
            ini_set('memory_limit', '512M');
        }

        $manager = ImageManager::usingDriver(Driver::class);
        $name = Str::uuid()->toString().'.webp';
        $disk = Storage::disk('public');

        // Didekode sekali: versi besar disimpan dulu, lalu gambar yang sama dikecilkan lagi untuk thumbnail.
        $image = $manager->decode($file)->scaleDown(width: self::FULL_WIDTH);
        $disk->put("{$directory}/{$name}", $image->encodeUsingFormat(Format::WEBP, quality: 82)->toString());

        $image->scaleDown(width: self::THUMB_WIDTH);
        $disk->put("{$directory}/thumbs/{$name}", $image->encodeUsingFormat(Format::WEBP, quality: 78)->toString());

        return [
            'image' => "{$directory}/{$name}",
            'thumbnail' => "{$directory}/thumbs/{$name}",
        ];
    }

    /**
     * Foto profil: dipotong persegi dari tengah, lalu disimpan sebagai satu berkas WebP.
     *
     * @return string path relatif pada disk "public"
     */
    public static function storeAvatar(UploadedFile $file, string $directory): string
    {
        $path = "{$directory}/".Str::uuid()->toString().'.webp';
        $image = ImageManager::usingDriver(Driver::class)->decode($file)->cover(self::AVATAR_SIZE, self::AVATAR_SIZE);

        Storage::disk('public')->put($path, $image->encodeUsingFormat(Format::WEBP, quality: 85)->toString());

        return $path;
    }
}
