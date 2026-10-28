<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_upload_is_recorded_and_shown_with_badge(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create(['name' => 'Admin Elka', 'created_at' => now()->subDay()]);

        $this->actingAs($admin)->post('/admin/galeri', [
            'images' => [UploadedFile::fake()->image('a.jpg', 1000, 700), UploadedFile::fake()->image('b.jpg', 1000, 700)],
        ])->assertRedirect();

        $this->assertDatabaseHas('activities', ['user_id' => $admin->id, 'description' => 'menambahkan 2 gambar ke galeri']);

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Admin Elka')
            ->assertSee('menambahkan 2 gambar ke galeri')
            ->assertSee('id="activity-badge"', false);
    }

    public function test_opening_notifications_marks_them_read(): void
    {
        $admin = User::factory()->admin()->create(['created_at' => now()->subDay()]);
        $this->actingAs($admin);
        Activity::record('memperbarui informasi kontak');

        $this->assertSame(1, Activity::unreadCountFor($admin));

        $this->post('/admin/notifikasi/baca')->assertNoContent();

        $this->assertSame(0, Activity::unreadCountFor($admin->fresh()));
        $this->get('/admin')->assertDontSee('id="activity-badge"', false);
    }

    public function test_activity_from_other_admin_is_unread_for_me(): void
    {
        $me = User::factory()->admin()->create();
        $other = User::factory()->admin()->create(['name' => 'Rekan Admin']);

        $this->actingAs($me)->post('/admin/notifikasi/baca');
        $this->travel(5)->seconds();
        $this->actingAs($other)->put('/admin/tentang/kontak', ['address' => 'Jl. Baru 1', 'whatsapp' => '08123456789', 'email' => 'a@b.com']);

        $this->assertSame(1, Activity::unreadCountFor($me->fresh()));
        $this->actingAs($me)->get('/admin')->assertSee('Rekan Admin')->assertSee('memperbarui informasi kontak');
    }

    public function test_activity_page_lists_who_what_and_when_in_local_time(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Elka']);
        $this->actingAs($admin);
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-20 06:30:15', 'UTC'));
        Activity::record('menambahkan 2 gambar ke galeri');

        $this->get('/admin/aktivitas')
            ->assertOk()
            ->assertSee('Admin Elka')
            ->assertSee('Menambahkan 2 gambar ke galeri')
            ->assertSee('20 Oktober 2026')
            ->assertSee('14:30:15')
            ->assertSee('WITA');
    }

    public function test_activity_page_keeps_all_history_and_paginates(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        foreach (range(1, 105) as $i) {
            Activity::record("aktivitas nomor {$i}");
        }

        $this->assertSame(105, Activity::count());
        $this->get('/admin/aktivitas')->assertSee('Aktivitas nomor 105')->assertDontSee('Aktivitas nomor 80')->assertSee('Halaman 1 dari 5');
        $this->get('/admin/aktivitas?page=5')->assertSee('Aktivitas nomor 5<', false);
    }

    public function test_activity_page_requires_admin(): void
    {
        $this->get('/admin/aktivitas')->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/aktivitas')->assertForbidden();
    }

    public function test_guest_and_non_admin_cannot_mark_read(): void
    {
        $this->post('/admin/notifikasi/baca')->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->post('/admin/notifikasi/baca')->assertForbidden();
    }
}
