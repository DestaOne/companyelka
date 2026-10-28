<?php

namespace Tests\Feature\Admin;

use App\Models\AboutSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        foreach (['/admin/tentang', '/admin/tentang/bagian/vision', '/admin/tentang/kontak'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }

        $this->put('/admin/tentang/bagian/vision', [])->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/tentang')->assertForbidden();
    }

    public function test_public_page_shows_original_content_by_default(): void
    {
        $this->get('/tentang-kami')
            ->assertSee('Dedikasi untuk Efisiensi dan Kemajuan Bisnis Anda')
            ->assertSee('CV Elka Mandiri adalah perusahaan penyedia solusi terpadu')
            ->assertSee('Berbekal komitmen pelayanan tinggi')
            ->assertSee('Landasan &amp; Tujuan', false)
            ->assertSee('Visi Perusahaan')
            ->assertSee('Misi Utama')
            ->assertSee('Membangun kemitraan jangka panjang')
            ->assertSee('Informasi Kantor')
            ->assertSee('Kontak dan Lokasi')
            ->assertSee('photo-1497366754035-f200968a6e72', false);
    }

    public function test_admin_page_previews_every_section_and_the_hero(): void
    {
        $this->actingAs($this->admin())->get('/admin/tentang')
            ->assertOk()
            ->assertSee('Dedikasi untuk Efisiensi')
            ->assertSee('Visi Perusahaan')
            ->assertSee('Misi Utama')
            ->assertSee('elkamandiri.cv@gmail.com')
            ->assertSee(route('admin.banners.hero.edit', 'tentang'), false)
            ->assertSee(route('admin.about.edit', 'background'), false)
            ->assertSee(route('admin.contact.edit'), false);
    }

    public function test_sidebar_menu_is_named_tentang(): void
    {
        $this->actingAs($this->admin())->get('/admin')
            ->assertSee(route('admin.about.index'), false)
            ->assertSee('Tentang');
    }

    public function test_admin_can_edit_background_with_image_and_paragraphs(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/bagian/background', [
            'eyebrow' => 'Sejarah Kami',
            'title' => 'Judul Latar Baru',
            'body' => "Paragraf pertama baru.\n\nParagraf kedua baru.\nParagraf ketiga baru.",
            'image' => UploadedFile::fake()->image('kantor.jpg', 1200, 900),
        ])->assertRedirect(route('admin.about.index'))->assertSessionHas('success');

        $section = AboutSection::for('background');
        Storage::disk('public')->assertExists($section->image);

        $this->get('/tentang-kami')
            ->assertSee('Sejarah Kami')
            ->assertSee('Judul Latar Baru')
            ->assertSee('Paragraf pertama baru.')
            ->assertSee('Paragraf kedua baru.')
            ->assertSee('Paragraf ketiga baru.')
            ->assertSee($section->image_url, false)
            ->assertDontSee('Dedikasi untuk Efisiensi');
        $this->get('/admin/aktivitas')->assertSee('Memperbarui bagian');
    }

    public function test_replacing_image_deletes_old_and_omitting_keeps_it(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $payload = ['eyebrow' => 'E', 'title' => 'T', 'body' => 'B'];

        $this->put('/admin/tentang/bagian/background', $payload + ['image' => UploadedFile::fake()->image('a.jpg', 1000, 800)]);
        $old = AboutSection::for('background')->image;

        $this->put('/admin/tentang/bagian/background', $payload);
        $this->assertSame($old, AboutSection::for('background')->image);

        $this->put('/admin/tentang/bagian/background', $payload + ['image' => UploadedFile::fake()->image('b.jpg', 1000, 800)]);
        Storage::disk('public')->assertMissing($old);
    }

    public function test_admin_can_edit_vision_mission_principles_and_contact_texts(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/bagian/vision', ['title' => 'Visi Baru', 'body' => 'Teks visi baru.'])->assertSessionHasNoErrors();
        $this->put('/admin/tentang/bagian/mission', ['title' => 'Misi Baru', 'body' => "Butir satu.\nButir dua."])->assertSessionHasNoErrors();
        $this->put('/admin/tentang/bagian/principles', ['title' => 'Prinsip Baru', 'body' => 'Teks prinsip baru.'])->assertSessionHasNoErrors();
        $this->put('/admin/tentang/bagian/contact', ['eyebrow' => 'Hubungi', 'title' => 'Temui Kami', 'body' => 'Teks kontak baru.'])->assertSessionHasNoErrors();

        $html = $this->get('/tentang-kami')->assertOk()->getContent();

        foreach (['Visi Baru', 'Teks visi baru.', 'Misi Baru', 'Butir satu.', 'Butir dua.', 'Prinsip Baru', 'Teks prinsip baru.', 'Hubungi', 'Temui Kami', 'Teks kontak baru.'] as $text) {
            $this->assertStringContainsString($text, $html);
        }

        $this->assertSame(2, substr_count($html, 'Butir '));
    }

    public function test_validation_and_unknown_section(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/bagian/vision', ['title' => '', 'body' => ''])->assertSessionHasErrors(['title', 'body']);
        $this->put('/admin/tentang/bagian/mission', ['title' => 'T', 'body' => implode("\n", array_fill(0, 9, 'butir'))])->assertSessionHasErrors('body');
        $this->put('/admin/tentang/bagian/background', ['eyebrow' => '', 'title' => 'T', 'body' => 'B'])->assertSessionHasErrors('eyebrow');
        $this->put('/admin/tentang/bagian/background', ['eyebrow' => 'E', 'title' => 'T', 'body' => 'B', 'image' => UploadedFile::fake()->image('k.jpg', 300, 200)])->assertSessionHasErrors('image');

        // Bagian tanpa gambar atau judul kecil menolak kolom itu.
        $this->put('/admin/tentang/bagian/vision', ['title' => 'T', 'body' => 'B', 'eyebrow' => 'x'])->assertSessionHasErrors('eyebrow');
        $this->put('/admin/tentang/bagian/vision', ['title' => 'T', 'body' => 'B', 'image' => UploadedFile::fake()->image('k.jpg', 900, 600)])->assertSessionHasErrors('image');

        $this->get('/admin/tentang/bagian/tidak-ada')->assertNotFound();
    }

    public function test_texts_are_escaped(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/bagian/vision', ['title' => '<script>alert(1)</script>', 'body' => '<b>tebal</b>']);

        $this->get('/tentang-kami')->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('<b>tebal</b>', false);
    }
}
