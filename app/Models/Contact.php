<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * Informasi kontak perusahaan. Hanya ada satu baris; halaman "Tentang Kami"
 * dan footer sama-sama membacanya, sehingga satu perubahan berlaku di keduanya.
 */
#[Fillable(['address', 'whatsapp', 'whatsapp_link', 'email', 'email_link', 'maps_link', 'maps_resolved'])]
class Contact extends Model
{
    private const DEFAULTS = [
        'address' => 'Jl. Tukad Batanghari Blok B No. 1, Panjer, Denpasar Selatan, Bali',
        'whatsapp' => '628113383679',
        'email' => 'elkamandiri.cv@gmail.com',
    ];

    private const WHATSAPP_MESSAGE = 'Halo Elka Mandiri, saya ingin berkonsultasi terkait layanan yang disediakan.';

    /** Satu-satunya baris kontak; dibuat dari data awal bila belum ada. */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], self::DEFAULTS);
    }

    /** Hanya angka, diawali kode negara: "0811-3383-679" dan "+62 811 3383 679" menjadi "628113383679". */
    public static function normalizeWhatsapp(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits;
    }

    /** Nomor untuk ditampilkan, mis. 08113383679. */
    protected function whatsappDisplay(): Attribute
    {
        return Attribute::get(fn () => str_starts_with($this->whatsapp, '62')
            ? '0'.substr($this->whatsapp, 2)
            : $this->whatsapp);
    }

    protected function whatsappUrl(): Attribute
    {
        return Attribute::get(fn () => $this->whatsapp_link
            ?: 'https://wa.me/'.$this->whatsapp.'?text='.rawurlencode(self::WHATSAPP_MESSAGE));
    }

    protected function emailUrl(): Attribute
    {
        return Attribute::get(fn () => $this->email_link ?: 'mailto:'.$this->email);
    }

    /** Host yang boleh dipakai untuk tautan WhatsApp. */
    public const WHATSAPP_HOSTS = ['wa.me', 'api.whatsapp.com', 'whatsapp.com', 'www.whatsapp.com', 'chat.whatsapp.com'];

    /** Host yang boleh dipakai untuk tautan peta (juga dipakai sebagai sumber iframe). */
    public const MAPS_HOSTS = ['google.com', 'www.google.com', 'maps.google.com', 'maps.app.goo.gl', 'goo.gl'];

    /** Tautan Google Maps bila admin mengisinya, selain itu pencarian berdasarkan alamat. */
    protected function mapsUrl(): Attribute
    {
        return Attribute::get(fn () => $this->maps_link
            ?: 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($this->address));
    }

    /** Link pendek Google Maps (hasil "Bagikan, Salin link") yang perlu dibuka dulu untuk tahu lokasinya. */
    private const SHORT_HOSTS = ['maps.app.goo.gl', 'goo.gl'];

    private const LONG_HOSTS = ['google.com', 'www.google.com', 'maps.google.com'];

    /**
     * Link pendek tidak bisa disematkan, jadi alamat panjangnya dicari lewat redirect (hanya antar
     * host Google). Mengembalikan null bila bukan link pendek atau gagal dibuka; peta lalu memakai alamat.
     */
    public static function resolveMapsLink(?string $link): ?string
    {
        $url = $link;

        for ($hops = 0; $hops < 5 && $url; $hops++) {
            $host = parse_url($url, PHP_URL_HOST);

            if (in_array($host, self::LONG_HOSTS, true)) {
                return $url === $link ? null : $url;
            }

            if (! in_array($host, self::SHORT_HOSTS, true)) {
                return null;
            }

            try {
                $next = Http::withoutRedirecting()->timeout(5)->get($url)->header('Location');
            } catch (\Throwable) {
                return null;
            }

            $url = $next !== '' ? $next : null;
        }

        return null;
    }

    /**
     * Sumber peta tertanam, dari link yang sama dengan tautan alamat. Link "Sematkan peta" dari
     * Google dipakai apa adanya; link tempat (/maps/place/Nama), ?q=, atau koordinat disematkan
     * lewat lokasinya; selain itu peta memakai alamat.
     */
    protected function mapsEmbedUrl(): Attribute
    {
        return Attribute::get(function () {
            $link = (string) ($this->maps_resolved ?: $this->maps_link);
            $parts = parse_url($link) ?: [];
            $path = $parts['path'] ?? '';
            $query = $this->address;

            if (in_array($parts['host'] ?? '', self::LONG_HOSTS, true)) {
                if (str_starts_with($path, '/maps/embed')) {
                    return $link;
                }

                parse_str($parts['query'] ?? '', $params);

                if (is_string($params['q'] ?? null) && $params['q'] !== '') {
                    $query = $params['q'];
                } elseif (preg_match('#^/maps/place/([^/]+)#', $path, $m)) {
                    $query = urldecode(str_replace('+', ' ', $m[1]));
                } elseif (preg_match('#@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)#', $path, $m)) {
                    $query = "{$m[1]},{$m[2]}";
                }
            }

            return 'https://maps.google.com/maps?q='.rawurlencode($query).'&t=&z=15&ie=UTF8&iwloc=&output=embed';
        });
    }
}
