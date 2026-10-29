<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\ContactMessage;
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
            ->assertDontSeeText('Jumlah Admin');
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

    public function test_attention_panel_shows_only_drafts_in_a_scrollable_list(): void
    {
        Service::factory()->create(['title' => 'Layanan Terbit Biasa']);
        Service::factory()->draft()->count(7)->create();

        $html = $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk()->getContent();

        $this->assertSame(7, substr_count($html, '>Edit</span>'));
        $this->assertStringContainsString('overflow-y-auto', $html);
        $this->assertStringNotContainsString('layanan draft lainnya', $html);
        $this->assertStringNotContainsString('Layanan Terbit Biasa', substr($html, strpos($html, 'attention-title')));
    }

    public function test_attention_panel_links_to_remaining_drafts_beyond_the_list(): void
    {
        Service::factory()->draft()->count(22)->create();

        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertSee('Dan 2 layanan draft lainnya');
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

    public function test_dashboard_lists_unread_messages_with_sender_and_subject(): void
    {
        $base = ['phone' => '0812345678', 'email' => 'budi@example.com', 'body' => 'Isi pesan yang cukup panjang.'];
        $unread = ContactMessage::create($base + ['name' => 'Budi Santoso', 'subject' => 'Tanya harga']);
        ContactMessage::create($base + ['name' => 'Sudah Dibaca', 'subject' => 'Lama', 'read_at' => now()]);

        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertSee('Pesan Belum Dibaca')
            ->assertSee('Budi Santoso')
            ->assertSee('Tanya harga')
            ->assertSee(route('admin.messages.show', $unread), false)
            ->assertDontSee('Sudah Dibaca')
            ->assertDontSee('lainnya: lihat semua');
    }

    public function test_dashboard_lists_all_unread_messages_in_scrollable_list(): void
    {
        foreach (range(1, 5) as $i) {
            ContactMessage::create(['name' => "Pengirim {$i}", 'phone' => '0812345678', 'email' => 'a@example.com', 'subject' => "Subjek {$i}", 'body' => 'Isi pesan yang cukup panjang.']);
        }

        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertSee('Pengirim 5')
            ->assertSee('Pengirim 3')
            ->assertSee('Pengirim 1')
            ->assertDontSee('lainnya: lihat semua')
            ->assertSee(route('admin.messages.index', ['filter' => 'belum-dibaca']), false);
    }

    public function test_dashboard_links_to_remaining_unread_messages_beyond_the_list(): void
    {
        foreach (range(1, 22) as $i) {
            ContactMessage::create(['name' => "Pengirim {$i}", 'phone' => '0812345678', 'email' => 'a@example.com', 'subject' => "Subjek {$i}", 'body' => 'Isi pesan yang cukup panjang.']);
        }

        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertSee('Dan 2 pesan lainnya')
            ->assertSee('overflow-y-auto', false);
    }

    public function test_dashboard_unread_card_when_all_read(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertSee('Semua pesan sudah dibaca')
            ->assertSee('Pesan Belum Dibaca');
    }
}
