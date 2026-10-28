<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $attributes = []): User
    {
        return User::factory()->admin()->create(array_merge(['password' => 'password-lama'], $attributes));
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        $this->get('/admin/profil')->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/profil')->assertForbidden();
    }

    public function test_admin_can_view_profile(): void
    {
        $admin = $this->admin(['name' => 'Budi Admin']);

        $this->actingAs($admin)->get('/admin/profil')
            ->assertOk()->assertSee('Budi Admin')->assertSeeText('Ubah password');
    }

    public function test_admin_can_update_username_and_email_without_password(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put('/admin/profil', ['name' => 'Nama Baru', 'email' => 'Baru@Example.com'])
            ->assertRedirect(route('admin.profile.edit'))->assertSessionHas('success');

        $admin->refresh();
        $this->assertSame('Nama Baru', $admin->name);
        $this->assertSame('baru@example.com', $admin->email);
        $this->assertTrue(Hash::check('password-lama', $admin->password));
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'dipakai@example.com']);

        $this->actingAs($this->admin())->put('/admin/profil', ['name' => 'X', 'email' => 'dipakai@example.com'])
            ->assertSessionHasErrors('email');
    }

    public function test_password_is_not_changed_unless_option_is_chosen(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put('/admin/profil', [
            'name' => $admin->name, 'email' => $admin->email,
            'password' => 'password-baru-123', 'password_confirmation' => 'password-baru-123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('password-lama', $admin->fresh()->password));
    }

    public function test_password_can_be_changed_with_correct_current_password(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put('/admin/profil', [
            'name' => $admin->name, 'email' => $admin->email, 'change_password' => 1,
            'current_password' => 'password-lama',
            'password' => 'password-baru-123', 'password_confirmation' => 'password-baru-123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.profile.edit'));

        $this->assertTrue(Hash::check('password-baru-123', $admin->fresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put('/admin/profil', [
            'name' => $admin->name, 'email' => $admin->email, 'change_password' => 1,
            'current_password' => 'salah',
            'password' => 'password-baru-123', 'password_confirmation' => 'password-baru-123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password-lama', $admin->fresh()->password));
    }

    public function test_new_password_must_be_confirmed_long_enough_and_different(): void
    {
        $admin = $this->admin();
        $base = ['name' => $admin->name, 'email' => $admin->email, 'change_password' => 1, 'current_password' => 'password-lama'];

        $this->actingAs($admin)->put('/admin/profil', $base + ['password' => 'password-baru-123', 'password_confirmation' => 'beda'])
            ->assertSessionHasErrors('password');
        $this->actingAs($admin)->put('/admin/profil', $base + ['password' => 'pendek', 'password_confirmation' => 'pendek'])
            ->assertSessionHasErrors('password');
        $this->actingAs($admin)->put('/admin/profil', $base + ['password' => 'password-lama', 'password_confirmation' => 'password-lama'])
            ->assertSessionHasErrors('password');
    }

    public function test_admin_can_upload_a_square_webp_profile_photo(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->put('/admin/profil', [
            'name' => $admin->name,
            'email' => $admin->email,
            'photo' => UploadedFile::fake()->image('foto.jpg', 800, 400),
        ])->assertRedirect(route('admin.profile.edit'));

        $admin->refresh();
        Storage::disk('public')->assertExists($admin->photo);
        $this->assertStringEndsWith('.webp', $admin->photo);
        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($admin->photo));
        $this->assertSame([256, 256], [$width, $height]);

        $this->get('/admin/profil')->assertSee($admin->photo_url, false);
    }

    public function test_replacing_and_removing_the_photo_deletes_the_old_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $data = ['name' => $admin->name, 'email' => $admin->email];

        $this->actingAs($admin)->put('/admin/profil', $data + ['photo' => UploadedFile::fake()->image('a.png', 400, 400)]);
        $first = $admin->refresh()->photo;
        $this->put('/admin/profil', $data + ['photo' => UploadedFile::fake()->image('b.png', 400, 400)]);
        $second = $admin->refresh()->photo;

        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        $this->put('/admin/profil', $data + ['remove_photo' => 1])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($second);
        $this->assertNull($admin->refresh()->photo);
    }

    public function test_saving_without_a_new_photo_keeps_the_current_one(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $data = ['name' => $admin->name, 'email' => $admin->email];

        $this->actingAs($admin)->put('/admin/profil', $data + ['photo' => UploadedFile::fake()->image('a.png', 400, 400)]);
        $path = $admin->refresh()->photo;
        $this->put('/admin/profil', ['name' => 'Nama Lain'] + $data);

        $this->assertSame($path, $admin->refresh()->photo);
        Storage::disk('public')->assertExists($path);
    }

    public function test_photo_is_validated(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $data = ['name' => $admin->name, 'email' => $admin->email];

        $this->actingAs($admin);
        $this->put('/admin/profil', $data + ['photo' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])->assertSessionHasErrors('photo');
        $this->put('/admin/profil', $data + ['photo' => UploadedFile::fake()->image('kecil.png', 100, 100)])->assertSessionHasErrors('photo');
        $this->assertNull($admin->refresh()->photo);
    }

    public function test_topbar_shows_initial_without_photo_and_photo_with_one(): void
    {
        Storage::fake('public');
        $admin = $this->admin(['name' => 'Budi']);

        $this->actingAs($admin)->get('/admin')->assertDontSee('alt="Foto Budi"', false);

        $this->put('/admin/profil', ['name' => 'Budi', 'email' => $admin->email, 'photo' => UploadedFile::fake()->image('a.png', 400, 400)]);
        $this->get('/admin')->assertSee('alt="Foto Budi"', false);
    }
}
