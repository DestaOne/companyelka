<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/_uji/{code}', fn (int $code) => abort($code, '', $code === 429 ? ['Retry-After' => 30] : []));
    }

    public function test_each_error_has_a_page_in_the_site_design(): void
    {
        $expected = [
            404 => ['404 Page Not Found', 'Halaman Tidak Ditemukan'],
            403 => ['403 Forbidden', 'Akses Ditolak'],
            419 => ['419 Page Expired', 'Sesi Anda Telah Berakhir'],
            429 => ['429 Too Many Requests', 'Terlalu Banyak Permintaan'],
            500 => ['500 Server Error', 'Terjadi Kesalahan pada Server'],
        ];

        foreach ($expected as $code => [$eyebrow, $title]) {
            $this->get("/_uji/{$code}")
                ->assertStatus($code)
                ->assertSee($eyebrow)
                ->assertSee($title)
                ->assertSee('Hak cipta dilindungi', false);
        }
    }

    public function test_429_tells_how_long_to_wait(): void
    {
        $this->get('/_uji/429')->assertStatus(429)->assertSee('coba lagi dalam 30 detik');
    }

    public function test_419_sends_admin_back_to_login_and_visitors_back_to_the_page(): void
    {
        Route::get('/admin/_uji-419', fn () => abort(419));

        $this->get('/admin/_uji-419')->assertStatus(419)->assertSee('Masuk Kembali')->assertSee(route('admin.login'), false);
        $this->get('/_uji/419')->assertStatus(419)->assertSee('Muat Ulang Halaman')->assertDontSee('Masuk Kembali');
    }

    public function test_expired_csrf_token_shows_the_419_page(): void
    {
        // Middleware CSRF tidak aktif saat test, jadi pengecualiannya dilempar langsung seperti yang dilakukan middleware itu.
        Route::get('/_uji-csrf', fn () => throw new \Illuminate\Session\TokenMismatchException('CSRF token mismatch.'));

        $this->get('/_uji-csrf')->assertStatus(419)->assertSee('Sesi Anda Telah Berakhir');
    }

    public function test_non_admin_sees_the_403_page(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden()->assertSee('Akses Ditolak');
    }

    public function test_500_page_does_not_need_the_database(): void
    {
        // Menyimulasikan database yang mati: layout biasa (footer, logo, kontak) akan gagal membaca.
        DB::shouldReceive('connection')->andThrow(new \RuntimeException('database mati'));

        $this->get('/_uji/500')->assertStatus(500)->assertSee('Terjadi Kesalahan pada Server');
    }
}
