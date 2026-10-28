<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Judul dan paragraf pembuka sebuah bagian Beranda yang bisa diedit admin. */
#[Fillable(['key', 'title', 'description'])]
class HomeSection extends Model
{
    public const FEATURES = 'features';

    /** @var array<string, array{title: string, description: string}> */
    private const DEFAULTS = [
        self::FEATURES => [
            'title' => 'Kualitas. Keandalan. Pelayanan.',
            'description' => 'Temukan alasan mengapa berbagai instansi dan perusahaan di Bali mempercayakan kebutuhan operasional dan ruang kerja mereka kepada CV Elka Mandiri.',
        ],
    ];

    /** Bagian untuk kunci ini; dibuat dari data awal bila belum ada. */
    public static function for(string $key): self
    {
        return static::firstOrCreate(['key' => $key], self::DEFAULTS[$key]);
    }
}
