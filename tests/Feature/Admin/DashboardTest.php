<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSeeText('Total Layanan')
            ->assertSeeText('Total Gambar Galeri')
            ->assertSeeText('Jumlah Admin');
    }

    public function test_logged_in_admin_is_redirected_away_from_login(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/login')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_has_quick_access_to_add_service_and_photo(): void
    {
        $this->actingAs(User::factory()->admin()->create(['name' => 'Budi Admin']))->get('/admin')
            ->assertOk()
            ->assertSee('Akses Cepat')
            ->assertSee('Halo, Budi Admin')
            ->assertSee('href="'.route('admin.services.create').'"', false)
            ->assertSee('href="'.route('admin.gallery.create').'"', false)
            ->assertSee('Tambah Layanan')
            ->assertSee('Tambah Foto');
    }

    public function test_attention_panel_lists_draft_services_with_edit_links(): void
    {
        $draft = Service::factory()->draft()->create(['title' => 'Layanan Rancangan', 'summary' => 'Ringkasan rancangan.']);
        Service::factory()->count(2)->create();

        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertOk()
            ->assertSee('2 terbit')
            ->assertSee('1 draft')
            ->assertSee('Perlu Perhatian')
            ->assertSee('Layanan Rancangan')
            ->assertSee('Ringkasan rancangan.')
            ->assertSee('href="'.route('admin.services.edit', $draft).'"', false);
    }

    public function test_attention_panel_shows_only_drafts_and_caps_the_list(): void
    {
        Service::factory()->create(['title' => 'Layanan Terbit Biasa']);
        Service::factory()->draft()->count(7)->create();

        $html = $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk()->getContent();

        $this->assertSame(5, substr_count($html, '>Edit</span>'));
        $this->assertStringContainsString('Dan 2 layanan draft lainnya', $html);
        $this->assertStringNotContainsString('Layanan Terbit Biasa', substr($html, strpos($html, 'attention-title')));
    }

    public function test_attention_panel_says_all_published_when_no_drafts(): void
    {
        Service::factory()->create();

        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertSee('Tidak ada layanan draft')
            ->assertDontSee('lainnya: lihat semua layanan');
    }

    public function test_dashboard_lists_recent_activities(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Elka']);
        $this->actingAs($admin);
        foreach (range(1, 6) as $i) {
            Activity::record("melakukan aktivitas nomor {$i}");
        }

        $this->get('/admin')
            ->assertSee('Aktivitas Terbaru')
            ->assertSee('melakukan aktivitas nomor 6')
            ->assertSee('melakukan aktivitas nomor 2')
            ->assertSee('WITA');
    }
}
