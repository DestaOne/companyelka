<?php

namespace Tests\Feature\Admin;

use App\Models\GalleryImage;
use App\Models\User;
use App\Support\UploadLimit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        $this->get('/admin/galeri')->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/galeri')->assertForbidden();
    }

    public function test_index_lists_images(): void
    {
        GalleryImage::factory()->count(2)->create();

        $this->actingAs($this->admin())->get('/admin/galeri')
            ->assertOk()->assertSeeText('2 gambar')->assertSeeText('Tambah Gambar');
    }

    public function test_admin_can_upload_multiple_images_as_resized_webp(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/galeri', [
            'images' => [
                UploadedFile::fake()->image('a.jpg', 4000, 3000),
                UploadedFile::fake()->image('b.png', 1200, 800),
            ],
        ])->assertRedirect(route('admin.gallery.index'));

        $this->assertSame(2, GalleryImage::count());

        $first = GalleryImage::orderBy('id')->first();
        Storage::disk('public')->assertExists($first->image);
        Storage::disk('public')->assertExists($first->thumbnail);

        [$fullWidth] = getimagesizefromstring(Storage::disk('public')->get($first->image));
        [$thumbWidth] = getimagesizefromstring(Storage::disk('public')->get($first->thumbnail));
        $this->assertSame(2400, $fullWidth);
        $this->assertSame(1000, $thumbWidth);

        $second = GalleryImage::orderByDesc('id')->first();
        [$smallWidth] = getimagesizefromstring(Storage::disk('public')->get($second->image));
        $this->assertSame(1200, $smallWidth, 'gambar kecil tidak boleh diperbesar');
    }

    public function test_upload_validation(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/galeri', [])->assertSessionHasErrors('images');

        $this->actingAs($admin)->post('/admin/galeri', [
            'images' => [UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')],
        ])->assertSessionHasErrors('images.0');

        $this->actingAs($admin)->post('/admin/galeri', [
            'images' => [UploadedFile::fake()->image('kecil.jpg', 300, 200)],
        ])->assertSessionHasErrors('images.0');

        $this->assertSame(0, GalleryImage::count());
    }

    public function test_admin_can_delete_image_and_its_files(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/galeri', ['images' => [UploadedFile::fake()->image('a.jpg', 1000, 800)]]);
        $image = GalleryImage::first();

        $this->actingAs($admin)->delete("/admin/galeri/{$image->id}")->assertRedirect(route('admin.gallery.index'));

        $this->assertModelMissing($image);
        Storage::disk('public')->assertMissing($image->image);
        Storage::disk('public')->assertMissing($image->thumbnail);
    }

    public function test_public_gallery_keeps_bento_grid_and_zoom_source(): void
    {
        GalleryImage::factory()->count(3)->create();

        $this->get('/galeri')
            ->assertOk()
            ->assertSee('md:grid-cols-12', false)
            ->assertSee('md:col-span-5 md:row-span-2', false)
            ->assertSee('data-zoomable', false)
            ->assertSee('data-full=', false);
    }

    public function test_public_gallery_shows_empty_state(): void
    {
        $this->get('/galeri')->assertOk()->assertSeeText('Belum ada dokumentasi');
    }

    public function test_dashboard_counts_gallery_images(): void
    {
        GalleryImage::factory()->count(4)->create();

        $this->actingAs($this->admin())->get('/admin')->assertSeeText('Gambar terunggah');
    }

    public function test_upload_limit_text_and_validation_follow_server_limit(): void
    {
        $maxKb = UploadLimit::kilobytes(\App\Http\Requests\Admin\GalleryRequest::APP_MAX_KB);
        $label = UploadLimit::label($maxKb);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/galeri/create')
            ->assertSeeText("maksimal {$label} per gambar");

        $this->actingAs($admin)->post('/admin/galeri', [
            'images' => [UploadedFile::fake()->image('besar.jpg', 2000, 1500)->size($maxKb + 1)],
        ])->assertSessionHasErrors(['images.0' => "Ukuran tiap gambar maksimal {$label}."]);
    }

    public function test_request_larger_than_post_max_size_shows_clear_413_page(): void
    {
        $limit = UploadLimit::postMaxBytes();
        $this->assertGreaterThan(0, $limit);

        $this->actingAs($this->admin())
            ->call('POST', '/admin/galeri', [], [], [], ['CONTENT_LENGTH' => $limit + 1])
            ->assertStatus(413)
            ->assertSeeText('Ukuran unggahan terlalu besar')
            ->assertSeeText(UploadLimit::label((int) floor($limit / 1024)));
    }
}
