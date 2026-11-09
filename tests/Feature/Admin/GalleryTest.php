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

        $this->assertSame(0, GalleryImage::count());

        // Gambar kecil boleh diunggah; tidak ada syarat lebar minimal.
        Storage::fake('public');
        $this->actingAs($admin)->post('/admin/galeri', [
            'images' => [UploadedFile::fake()->image('kecil.jpg', 300, 200)],
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, GalleryImage::count());
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

    public function test_app_has_no_per_image_size_cap_below_the_server_limit(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        // 5 MB: di atas batas 2 MB yang dulu dipasang aplikasi.
        $this->actingAs($admin)->post('/admin/galeri', [
            'images' => [UploadedFile::fake()->image('besar.jpg', 2000, 1500)->size(5120)],
        ])->assertRedirect(route('admin.gallery.index'))->assertSessionHasNoErrors();

        $this->assertSame(1, GalleryImage::count());

        $label = UploadLimit::label(UploadLimit::serverKilobytes());
        $this->actingAs($admin)->get('/admin/galeri/create')
            ->assertSeeText("Ukuran per gambar maksimal {$label} (batas server)");
    }

    public function test_new_images_are_appended_and_public_page_follows_position(): void
    {
        $first = GalleryImage::factory()->create(['title' => 'Pertama']);
        $second = GalleryImage::factory()->create(['title' => 'Kedua']);
        $third = GalleryImage::factory()->create(['title' => 'Ketiga']);

        $this->assertSame([1, 2, 3], [$first->position, $second->position, $third->position]);

        $this->actingAs($this->admin())->putJson('/admin/galeri/urutan', ['order' => [$third->id, $first->id, $second->id]])
            ->assertOk();

        $this->assertSame(['Ketiga', 'Pertama', 'Kedua'], GalleryImage::ordered()->pluck('title')->all());
        $this->get('/galeri')->assertSeeInOrder(['Ketiga', 'Pertama', 'Kedua']);

        $new = GalleryImage::factory()->create(['title' => 'Baru']);
        $this->assertSame(4, $new->position);
    }

    public function test_admin_preview_follows_position_and_uses_the_public_grid_pattern(): void
    {
        $a = GalleryImage::factory()->create(['title' => 'A']);
        $b = GalleryImage::factory()->create(['title' => 'B']);
        $b->update(['position' => 0]);

        $this->actingAs($this->admin())->get('/admin/galeri')
            ->assertOk()
            ->assertSee('id="bento"', false)
            ->assertSee('md:col-span-5 md:row-span-2', false)
            ->assertSee('data-handle', false)
            ->assertSeeInOrder(['data-id="'.$b->id.'"', 'data-id="'.$a->id.'"'], false);
    }

    public function test_reorder_is_validated_and_admin_only(): void
    {
        $image = GalleryImage::factory()->create();

        $this->putJson('/admin/galeri/urutan', ['order' => [$image->id]])->assertUnauthorized();
        $this->actingAs(User::factory()->create())->putJson('/admin/galeri/urutan', ['order' => [$image->id]])->assertForbidden();

        $admin = $this->admin();
        $this->actingAs($admin)->putJson('/admin/galeri/urutan', [])->assertJsonValidationErrors('order');
        $this->actingAs($admin)->putJson('/admin/galeri/urutan', ['order' => [9999]])->assertJsonValidationErrors('order.0');
        $this->actingAs($admin)->putJson('/admin/galeri/urutan', ['order' => [$image->id, $image->id]])->assertJsonValidationErrors('order.0');
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

    public function test_titles_can_be_set_on_upload_and_edited_later(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/galeri', [
            'images' => [UploadedFile::fake()->image('a.jpg', 1000, 800), UploadedFile::fake()->image('b.jpg', 1000, 800)],
            'titles' => ['Pengadaan Kantor', ''],
        ])->assertRedirect(route('admin.gallery.index'));

        [$a, $b] = GalleryImage::orderBy('id')->get()->all();
        $this->assertSame('Pengadaan Kantor', $a->title);
        $this->assertNull($b->title);

        $this->get('/galeri')->assertSeeText('Pengadaan Kantor');

        $this->put("/admin/galeri/{$b->id}", ['title' => 'Judul Baru'])->assertRedirect();
        $this->assertSame('Judul Baru', $b->fresh()->title);

        $this->put("/admin/galeri/{$b->id}", ['title' => str_repeat('x', 101)])->assertSessionHasErrors('title');
    }
}
