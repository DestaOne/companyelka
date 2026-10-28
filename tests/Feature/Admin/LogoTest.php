<?php

namespace Tests\Feature\Admin;

use App\Models\Logo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        $this->get('/admin/logo')->assertRedirect(route('admin.login'));
        $this->put('/admin/logo/navbar', [])->assertRedirect(route('admin.login'));
        $this->delete('/admin/logo/navbar')->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/logo')->assertForbidden();
    }

    public function test_public_site_uses_default_logos(): void
    {
        $this->get('/')
            ->assertSee(asset('images/logo.png'), false)
            ->assertSee(asset('images/elkamandiri.jpeg'), false);
    }

    public function test_index_lists_both_logos_and_unknown_key_is_404(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/logo')->assertOk()->assertSee('Logo Navbar')->assertSee('Logo Footer');
        $this->get('/admin/logo/navbar')->assertOk();
        $this->get('/admin/logo/tidak-ada')->assertNotFound();
    }

    public function test_admin_can_replace_navbar_logo_and_footer_stays_default(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/logo/navbar', ['image' => UploadedFile::fake()->image('baru.png', 400, 140)])
            ->assertRedirect(route('admin.logo.index'));

        $logo = Logo::for(Logo::NAVBAR);
        Storage::disk('public')->assertExists($logo->image);
        $this->get('/')
            ->assertSee($logo->image_url, false)
            ->assertSee(asset('images/elkamandiri.jpeg'), false);
    }

    public function test_replacing_deletes_the_old_file_and_reset_restores_default(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/logo/footer', ['image' => UploadedFile::fake()->image('a.png', 400, 140)]);
        $first = Logo::for(Logo::FOOTER)->image;
        $this->put('/admin/logo/footer', ['image' => UploadedFile::fake()->image('b.png', 400, 140)]);

        Storage::disk('public')->assertMissing($first);
        $second = Logo::for(Logo::FOOTER)->image;
        Storage::disk('public')->assertExists($second);

        $this->delete('/admin/logo/footer')->assertRedirect(route('admin.logo.index'));
        Storage::disk('public')->assertMissing($second);
        $this->assertNull(Logo::for(Logo::FOOTER)->image);
    }

    public function test_upload_is_validated(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->put('/admin/logo/navbar', [])->assertSessionHasErrors('image');
        $this->put('/admin/logo/navbar', ['image' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])->assertSessionHasErrors('image');
        $this->put('/admin/logo/navbar', ['image' => UploadedFile::fake()->image('kecil.png', 100, 50)])->assertSessionHasErrors('image');
    }
}
