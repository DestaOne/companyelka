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

    /**
     * @return array{image: string, thumbnail: string} path relatif pada disk "public"
     */
    public static function store(UploadedFile $file, string $directory): array
    {
        $manager = ImageManager::usingDriver(Driver::class);
        $name = Str::uuid()->toString().'.webp';
        $disk = Storage::disk('public');

        $full = $manager->decode($file)->scaleDown(width: self::FULL_WIDTH);
        $disk->put("{$directory}/{$name}", $full->encodeUsingFormat(Format::WEBP, quality: 82)->toString());

        $thumb = $manager->decode($file)->scaleDown(width: self::THUMB_WIDTH);
        $disk->put("{$directory}/thumbs/{$name}", $thumb->encodeUsingFormat(Format::WEBP, quality: 78)->toString());

        return [
            'image' => "{$directory}/{$name}",
            'thumbnail' => "{$directory}/thumbs/{$name}",
        ];
    }
}
