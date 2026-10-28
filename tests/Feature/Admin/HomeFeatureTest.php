<?php

namespace Tests\Feature\Admin;

use App\Models\HomeFeature;
use App\Models\HomeSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        foreach (['/admin/home/keunggulan', '/admin/home/keunggulan/1'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
            $this->put($url, [])->assertRedirect(route('admin.login'));
        }

        $this->actingAs(User::factory()->create())->get('/admin/home/keunggulan/1')->assertForbidden();
    }

    public function test_home_page_shows_original_content_by_default(): void
    {
        $this->get('/')
            ->assertSee('Kualitas. Keandalan. Pelayanan.')
            ->assertSee('Temukan alasan mengapa berbagai instansi')
            ->assertSee('Solusi Terpadu')
            ->assertSee('Standar Profesional')
            ->assertSee('Dukungan Responsif')
            ->assertSee('photo-1497215728101-856f4ea42174', false);
    }

    public function test_admin_home_lists_section_heading_and_three_cards(): void
    {
        $this->actingAs($this->admin())->get('/admin/home')
            ->assertOk()
            ->assertSee('Kualitas. Keandalan. Pelayanan.')
            ->assertSee('Solusi Terpadu')
            ->assertSee('Standar Profesional')
            ->assertSee('Dukungan Responsif')
            ->assertSee(route('admin.home.features.edit', 2), false)
            ->assertSee(route('admin.home.features.section.edit'), false);

        $this->assertSame(3, HomeFeature::count());
    }

    public function test_admin_can_edit_section_heading(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/home/keunggulan', ['title' => 'Mutu. Harga. Garansi.', 'description' => 'Alasan memilih kami.'])
            ->assertRedirect(route('admin.home.index'))
            ->assertSessionHas('success');

        $this->get('/')->assertSee('Mutu. Harga. Garansi.')->assertSee('Alasan memilih kami.')->assertDontSee('Kualitas. Keandalan. Pelayanan.');
        $this->get('/admin/aktivitas')->assertSee('Memperbarui judul bagian');
        $this->assertSame(1, HomeSection::count());
    }

    public function test_admin_can_edit_a_card_image_title_and_text(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/home/keunggulan/1', [
            'title' => 'Judul Kartu Baru',
            'description' => 'Teks kartu baru.',
            'image' => UploadedFile::fake()->image('kartu.jpg', 1000, 800),
        ])->assertRedirect(route('admin.home.index'))->assertSessionHas('success');

        $feature = HomeFeature::forSlot(1);
        Storage::disk('public')->assertExists($feature->image);

        $this->get('/')
            ->assertSee('Judul Kartu Baru')
            ->assertSee('Teks kartu baru.')
            ->assertSee($feature->image_url, false)
            ->assertDontSee('Solusi Terpadu');
    }

    public function test_replacing_image_deletes_old_one_and_omitting_keeps_it(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $payload = ['title' => 'Judul', 'description' => 'Teks'];

        $this->put('/admin/home/keunggulan/2', $payload + ['image' => UploadedFile::fake()->image('a.jpg', 1000, 800)]);
        $old = HomeFeature::forSlot(2)->image;

        $this->put('/admin/home/keunggulan/2', $payload);
        $this->assertSame($old, HomeFeature::forSlot(2)->image);

        $this->put('/admin/home/keunggulan/2', $payload + ['image' => UploadedFile::fake()->image('b.jpg', 1000, 800)]);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists(HomeFeature::forSlot(2)->image);
    }

    public function test_validation_and_escaping(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/home/keunggulan/1', ['title' => '', 'description' => ''])->assertSessionHasErrors(['title', 'description']);
        $this->put('/admin/home/keunggulan/1', ['title' => 'T', 'description' => 'D', 'image' => UploadedFile::fake()->image('kecil.jpg', 400, 300)])->assertSessionHasErrors('image');
        $this->put('/admin/home/keunggulan', ['title' => '', 'description' => ''])->assertSessionHasErrors(['title', 'description']);
        $this->get('/admin/home/keunggulan/4')->assertNotFound();

        $this->put('/admin/home/keunggulan/3', ['title' => '<script>alert(1)</script>', 'description' => '<b>x</b>']);
        $this->get('/')->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('<b>x</b>', false);
    }
}
