<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        foreach (['/admin/hero-banner/home', '/admin/cta/galeri'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }

        $this->put('/admin/cta/home', [])->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/hero-banner/home')->assertForbidden();
    }

    public function test_public_pages_show_the_original_content_by_default(): void
    {
        $this->get('/')
            ->assertSee('Solusi Lengkap untuk Kebutuhan Operasional dan Ruang Kerja')
            ->assertSee('Lihat Layanan Kami')
            ->assertSee('Siap Membangun Ruang Kerja Ideal Anda?')
            ->assertSee('Konsultasi Sekarang');
        $this->get('/layanan')->assertSee('Solusi Ekosistem Kerja')->assertSee('Katalog layanan')->assertSee('Tertarik Dengan Layanan Kami?');
        $this->get('/tentang-kami')->assertSee('CV. Elka Mandiri')->assertSee('Tentang Perusahaan');
        $this->get('/galeri')->assertSee('Ingin Hasil Serupa untuk Ruang Kerja Anda?');
    }

    public function test_separate_hero_and_cta_menus_no_longer_exist(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/hero-banner')->assertNotFound();
        $this->get('/admin/cta')->assertNotFound();
        $this->get('/admin')->assertDontSee('Hero Banner')->assertDontSee('/admin/cta', false)->assertSee('Logo');
    }

    public function test_each_menu_page_shows_its_own_hero_and_cta(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/home')->assertOk()
            ->assertSee(route('admin.banners.hero.edit', 'home'), false)
            ->assertSee(route('admin.banners.cta.edit', 'home'), false)
            ->assertSee('Solusi Lengkap untuk Kebutuhan Operasional dan Ruang Kerja')
            ->assertSee('Siap Membangun Ruang Kerja Ideal Anda?');

        $this->get('/admin/layanan')->assertOk()
            ->assertSee(route('admin.banners.hero.edit', 'layanan'), false)
            ->assertSee(route('admin.banners.cta.edit', 'layanan'), false)
            ->assertSee(route('admin.banners.cta.edit', 'layanan-detail'), false)
            ->assertDontSee(route('admin.banners.hero.edit', 'home'), false);

        $this->get('/admin/tentang')->assertOk()
            ->assertSee(route('admin.banners.hero.edit', 'tentang'), false)
            ->assertDontSee('/admin/cta/', false);

        $this->get('/admin/galeri')->assertOk()
            ->assertSee(route('admin.banners.cta.edit', 'galeri'), false)
            ->assertDontSee('/admin/hero-banner/', false);
    }

    public function test_edit_page_cancel_link_and_unknown_place(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/cta/galeri')->assertOk()->assertSee(route('admin.gallery.index').'#banner-cta-galeri', false);
        $this->get('/admin/hero-banner/tidak-ada')->assertNotFound();
        $this->get('/admin/cta/tentang')->assertNotFound();
    }

    public function test_saving_returns_to_the_page_the_banner_belongs_to(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/cta/galeri', ['content' => '# Judul', 'button_text' => 'Klik'])
            ->assertRedirect(route('admin.gallery.index').'#banner-cta-galeri');
        $this->put('/admin/cta/layanan-detail', ['content' => '# Judul', 'button_text' => 'Klik'])
            ->assertRedirect(route('admin.services.index').'#banner-cta-layanan-detail');
        $this->put('/admin/hero-banner/tentang', ['content' => '# Judul'])
            ->assertRedirect(route('admin.about.index').'#banner-hero-tentang');
    }

    public function test_admin_can_edit_hero_text_button_and_image(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/hero-banner/home', [
            'content' => "# Judul Baru Beranda\nDeskripsi baru untuk hero.",
            'button_text' => 'Mulai Sekarang',
            'image' => UploadedFile::fake()->image('hero.jpg', 1600, 800),
        ])->assertRedirect(route('admin.home.index').'#banner-hero-home')->assertSessionHas('success');

        $banner = Banner::for('hero', 'home');
        Storage::disk('public')->assertExists($banner->image);

        $this->get('/')
            ->assertSee('<h1', false)
            ->assertSee('Judul Baru Beranda')
            ->assertSee('Deskripsi baru untuk hero.')
            ->assertSee('Mulai Sekarang')
            ->assertSee($banner->image_url, false)
            ->assertDontSee('Solusi Lengkap untuk Kebutuhan Operasional dan Ruang Kerja');
        $this->get('/admin/aktivitas')->assertSee('Memperbarui hero banner Beranda');
    }

    public function test_replacing_image_deletes_old_file_and_keeping_it_keeps_the_old_one(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $payload = ['content' => "# Judul\nIsi", 'button_text' => 'Klik'];

        $this->put('/admin/cta/home', $payload + ['image' => UploadedFile::fake()->image('a.jpg', 1600, 600)]);
        $old = Banner::for('cta', 'home')->image;

        $this->put('/admin/cta/home', $payload);
        $this->assertSame($old, Banner::for('cta', 'home')->image);

        $this->put('/admin/cta/home', $payload + ['image' => UploadedFile::fake()->image('b.jpg', 1600, 600)]);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists(Banner::for('cta', 'home')->image);
    }

    public function test_cta_renders_heading_description_and_button_text(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/cta/galeri', ['content' => "# Hubungi Kami\nKami siap membantu.", 'button_text' => 'Chat Kami'])->assertSessionHasNoErrors();

        $this->get('/galeri')->assertSee('Hubungi Kami')->assertSee('Kami siap membantu.')->assertSee('Chat Kami');
    }

    public function test_content_must_have_a_heading_and_is_escaped(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/cta/home', ['content' => 'hanya deskripsi', 'button_text' => 'Klik'])->assertSessionHasErrors('content');
        $this->put('/admin/cta/home', ['content' => '', 'button_text' => 'Klik'])->assertSessionHasErrors('content');

        $this->put('/admin/cta/home', ['content' => "# <script>alert(1)</script>\n<b>tebal</b>", 'button_text' => 'Klik'])->assertSessionHasNoErrors();
        $this->get('/')->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('<b>tebal</b>', false);
    }

    public function test_button_text_is_required_only_where_a_button_exists(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/hero-banner/home', ['content' => '# Judul', 'button_text' => ''])->assertSessionHasErrors('button_text');
        $this->put('/admin/cta/home', ['content' => '# Judul'])->assertSessionHasErrors('button_text');

        $this->put('/admin/hero-banner/tentang', ['content' => '# Judul saja'])->assertSessionHasNoErrors();
        $this->put('/admin/hero-banner/tentang', ['content' => '# Judul', 'button_text' => 'Tidak ada tombol'])->assertSessionHasErrors('button_text');
    }

    public function test_image_validation(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $payload = ['content' => '# Judul', 'button_text' => 'Klik'];

        $this->put('/admin/cta/home', $payload + ['image' => UploadedFile::fake()->image('kecil.jpg', 600, 300)])->assertSessionHasErrors('image');
        $this->put('/admin/cta/home', $payload + ['image' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')])->assertSessionHasErrors('image');
    }

    public function test_editor_is_present_on_edit_page(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/hero-banner/home')
            ->assertOk()
            ->assertSee('id="editor-heading"', false)
            ->assertSee('Lihat Layanan Kami');
    }
}
