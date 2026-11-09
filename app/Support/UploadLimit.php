<?php

namespace App\Support;

/**
 * Batas ukuran unggahan yang sebenarnya berlaku: yang terkecil antara batas aplikasi
 * dan konfigurasi PHP (upload_max_filesize, post_max_size), supaya teks dan pesan
 * validasi tidak menjanjikan ukuran yang akan ditolak server.
 */
class UploadLimit
{
    /** Batas per berkas dalam kilobyte. */
    public static function kilobytes(int $appMaxKb): int
    {
        $limits = [$appMaxKb * 1024];

        foreach (['upload_max_filesize', 'post_max_size'] as $key) {
            $bytes = (int) ini_parse_quantity((string) ini_get($key));

            if ($bytes > 0) {
                $limits[] = $bytes;
            }
        }

        return (int) floor(min($limits) / 1024);
    }

    /** Batas per berkas dari konfigurasi PHP saja, tanpa batas tambahan dari aplikasi; 0 berarti tanpa batas. */
    public static function serverKilobytes(): int
    {
        $limits = array_filter(array_map(
            fn (string $key) => (int) ini_parse_quantity((string) ini_get($key)),
            ['upload_max_filesize', 'post_max_size'],
        ), fn (int $bytes) => $bytes > 0);

        return $limits === [] ? 0 : (int) floor(min($limits) / 1024);
    }

    /** Batas total satu permintaan (post_max_size) dalam byte; 0 berarti tanpa batas. */
    public static function postMaxBytes(): int
    {
        return (int) ini_parse_quantity((string) ini_get('post_max_size'));
    }

    public static function label(int $kilobytes): string
    {
        return $kilobytes >= 1024
            ? rtrim(rtrim(number_format($kilobytes / 1024, 1, ',', ''), '0'), ',').' MB'
            : $kilobytes.' KB';
    }
}
