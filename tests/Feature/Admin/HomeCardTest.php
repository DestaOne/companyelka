<?php

namespace Tests\Feature\Admin;

use App\Models\HomeCard;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'eyebrow' => 'Judul Kecil Baru',
            'title' => 'Judul Baru',
            'description' => 'Deskripsi baru untuk kotak ini.',
            'service_id' => '',
        ], $override);
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        $this->get('/admin/home')->assertRedirect(route('admin.login'));
        $this->get('/admin/home/1')->assertRedirect(route('admin.login'));
        $this->put('/admin/home/1', $this->payload())->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/home')->assertForbidden();
    }

    public function test_index_shows_four_cards_with_title_and_description(): void
    {
        $this->actingAs($this->admin())->get('/admin/home')
            ->assertOk()
            ->assertSee('01 / IT &amp; Perangkat Lunak', false)
            ->assertSee('Infrastruktur Digital')
            ->assertSee('Pengadaan PC, laptop, server')
            ->assertSee('Efisiensi Dokumen')
            ->assertSee('Ruang Kerja Ergonomis')
            ->assertSee('Eksplorasi Katalog')
            ->assertSee('Lihat layanan lainnya');

        $this->assertSame(4, HomeCard::count());
    }

    public function test_default_destinations_use_existing_services_by_slug(): void
    {
        $komputer = Service::factory()->create(['slug' => 'komputer']);

        $cards = HomeCard::allSlots();

        $this->assertSame($komputer->id, $cards[0]->service_id);
        $this->assertNull($cards[1]->service_id);
        $this->assertNull($cards[3]->service_id);
    }

    public function test_admin_can_edit_a_card_and_home_page_follows(): void
    {
        $target = Service::factory()->create(['title' => 'Layanan Tujuan', 'slug' => 'layanan-tujuan']);
        $this->actingAs($this->admin());

        $this->put('/admin/home/2', $this->payload(['service_id' => $target->id]))
            ->assertRedirect(route('admin.home.index'))
            ->assertSessionHas('success');

        $html = $this->get('/')->assertOk()
            ->assertSee('02 / Judul Kecil Baru')
            ->assertSee('Judul Baru')
            ->assertSee('Deskripsi baru untuk kotak ini.')
            ->assertDontSee('Efisiensi Dokumen')
            ->getContent();

        $this->assertStringContainsString('href="'.route('layanan.show', $target).'"', $html);
        $this->get('/admin/aktivitas')->assertSee('Memperbarui kotak layanan Beranda');
    }

    public function test_draft_or_removed_destination_falls_back_to_all_services(): void
    {
        $draft = Service::factory()->draft()->create(['slug' => 'rahasia']);
        $this->actingAs($this->admin());

        $this->put('/admin/home/1', $this->payload(['service_id' => $draft->id]));
        $this->get('/admin')->assertOk();

        $this->get('/')->assertDontSee('/layanan/rahasia');
        $this->get('/admin/home')->assertSee('Draft: di situs menuju semua layanan');

        $draft->delete();
        $this->assertNull(HomeCard::forSlot(1)->service_id);
        $this->get('/')->assertOk()->assertSee('href="'.route('layanan.index').'"', false);
    }

    public function test_edit_form_lists_existing_services_in_dropdown(): void
    {
        Service::factory()->create(['title' => 'Layanan Terbit Satu']);
        Service::factory()->draft()->create(['title' => 'Layanan Draft Dua']);

        $this->actingAs($this->admin())->get('/admin/home/3')
            ->assertOk()
            ->assertSee('Layanan Terbit Satu')
            ->assertSee('Layanan Draft Dua (draft)')
            ->assertSee('Semua layanan (halaman katalog)');
    }

    public function test_validation(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/home/1', ['eyebrow' => '', 'title' => '', 'description' => '', 'service_id' => ''])
            ->assertSessionHasErrors(['eyebrow', 'title', 'description']);
        $this->put('/admin/home/1', $this->payload(['service_id' => 9999]))->assertSessionHasErrors('service_id');
        $this->put('/admin/home/1', $this->payload(['title' => str_repeat('a', 81)]))->assertSessionHasErrors('title');

        // Kotak 4 (kartu biru) boleh tanpa deskripsi.
        $this->put('/admin/home/4', $this->payload(['description' => '']))->assertSessionHasNoErrors();
        $this->get('/admin/home/5')->assertNotFound();
    }

    public function test_card_text_is_escaped(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/home/1', $this->payload(['title' => '<script>alert(1)</script>']));

        $this->get('/')->assertDontSee('<script>alert(1)</script>', false);
    }
}
