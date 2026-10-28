<?php

namespace Tests\Feature\Admin;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'title' => 'Alat Tulis Kantor',
            'summary' => 'Ringkasan layanan.',
            'detail' => "# Cakupan\n- Pulpen\n- Kertas\nParagraf penutup.",
            'status' => 'terbit',
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ], $override);
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        $this->get('/admin/layanan')->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/layanan')->assertForbidden();
    }

    public function test_index_lists_services(): void
    {
        Service::factory()->create(['title' => 'Layanan Contoh']);

        $this->actingAs($this->admin())->get('/admin/layanan')
            ->assertOk()->assertSeeText('Layanan Contoh')->assertSeeText('Tambah Layanan');
    }

    public function test_index_can_be_searched_by_title_or_summary(): void
    {
        Service::factory()->create(['title' => 'Mesin Fotokopi', 'summary' => 'Cetak dokumen.']);
        Service::factory()->create(['title' => 'Kursi Kantor', 'summary' => 'Ergonomis untuk karyawan.']);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/layanan?q=fotokopi')
            ->assertSeeText('Mesin Fotokopi')->assertDontSeeText('Kursi Kantor');

        $this->actingAs($admin)->get('/admin/layanan?q=ergonomis')
            ->assertSeeText('Kursi Kantor')->assertDontSeeText('Mesin Fotokopi');

        $this->actingAs($admin)->get('/admin/layanan?q=100%')
            ->assertSeeText('Tidak ada layanan yang cocok');
    }

    public function test_admin_can_create_service_and_detail_page_is_available(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/layanan', $this->payload())
            ->assertRedirect(route('admin.services.index'));

        $service = Service::firstWhere('slug', 'alat-tulis-kantor');
        $this->assertNotNull($service);
        Storage::disk('public')->assertExists($service->image);

        $this->get('/layanan/alat-tulis-kantor')
            ->assertOk()
            ->assertSeeText('Alat Tulis Kantor')
            ->assertSeeText('Cakupan')
            ->assertSeeText('Pulpen')
            ->assertSeeText('Paragraf penutup.');
    }

    public function test_slug_is_unique_for_same_title(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/layanan', $this->payload());
        $this->actingAs($admin)->post('/admin/layanan', $this->payload());

        $this->assertEqualsCanonicalizing(
            ['alat-tulis-kantor', 'alat-tulis-kantor-2'],
            Service::pluck('slug')->all(),
        );
    }

    public function test_create_requires_valid_input(): void
    {
        $this->actingAs($this->admin())->post('/admin/layanan', [])
            ->assertSessionHasErrors(['title', 'summary', 'detail', 'image']);

        $this->actingAs($this->admin())
            ->post('/admin/layanan', $this->payload(['image' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrors('image');
    }

    public function test_update_keeps_slug_and_replaces_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/layanan', $this->payload());
        $service = Service::first();
        $oldImage = $service->image;

        $this->actingAs($admin)->put("/admin/layanan/{$service->id}", $this->payload([
            'title' => 'Judul Baru',
            'image' => UploadedFile::fake()->image('baru.png'),
        ]))->assertRedirect(route('admin.services.index'));

        $service->refresh();
        $this->assertSame('Judul Baru', $service->title);
        $this->assertSame('alat-tulis-kantor', $service->slug);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($service->image);
    }

    public function test_update_without_new_image_keeps_old_one(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->admin())->put("/admin/layanan/{$service->id}", $this->payload(['image' => null]))
            ->assertSessionHasNoErrors();

        $this->assertSame($service->image, $service->fresh()->image);
    }

    public function test_admin_can_delete_service_and_its_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/layanan', $this->payload());
        $service = Service::first();

        $this->actingAs($admin)->delete("/admin/layanan/{$service->id}")
            ->assertRedirect(route('admin.services.index'));

        $this->assertModelMissing($service);
        Storage::disk('public')->assertMissing($service->image);
        $this->get('/layanan/alat-tulis-kantor')->assertNotFound();
    }

    public function test_detail_content_is_escaped(): void
    {
        $service = Service::factory()->create(['detail' => '- <script>alert(1)</script>']);

        $this->get("/layanan/{$service->slug}")->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_dashboard_counts_services(): void
    {
        Service::factory()->count(3)->create();

        $this->actingAs($this->admin())->get('/admin')->assertSeeText('3');
    }

    public function test_status_is_required_and_must_be_valid(): void
    {
        $this->actingAs($this->admin())->post('/admin/layanan', $this->payload(['status' => 'rahasia']))
            ->assertSessionHasErrors('status');
    }

    public function test_draft_services_are_hidden_from_public_site(): void
    {
        $published = Service::factory()->create(['title' => 'Layanan Terbit']);
        $draft = Service::factory()->draft()->create(['title' => 'Layanan Draft']);

        $this->get('/layanan')->assertSeeText('Layanan Terbit')->assertDontSeeText('Layanan Draft');
        $this->get('/layanan/'.$published->slug)->assertOk();
        $this->get('/layanan/'.$draft->slug)->assertNotFound();
    }

    public function test_service_can_be_switched_between_draft_and_published(): void
    {
        $service = Service::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->put("/admin/layanan/{$service->id}", $this->payload(['status' => 'draft']))
            ->assertRedirect(route('admin.services.index'));
        $this->assertSame('draft', $service->fresh()->status);

        $this->actingAs($admin)->put("/admin/layanan/{$service->id}", $this->payload(['status' => 'terbit']));
        $this->assertSame('terbit', $service->fresh()->status);
    }

    public function test_new_services_are_appended_and_public_pages_follow_position(): void
    {
        $a = Service::factory()->create(['title' => 'Layanan A']);
        $b = Service::factory()->create(['title' => 'Layanan B']);
        $this->assertSame([1, 2], [$a->position, $b->position]);

        $this->actingAs($this->admin())
            ->putJson('/admin/layanan/urutan', ['order' => [$b->id, $a->id]])
            ->assertOk();

        $this->assertSame([$b->id, $a->id], Service::ordered()->pluck('id')->all());
        $this->get('/layanan')->assertSeeInOrder(['Layanan B', 'Layanan A']);
    }

    public function test_reorder_rejects_invalid_input_and_non_admins(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->admin())->putJson('/admin/layanan/urutan', ['order' => [$service->id, 999]])
            ->assertUnprocessable();

        $this->actingAs(User::factory()->create())->putJson('/admin/layanan/urutan', ['order' => [$service->id]])
            ->assertForbidden();
    }

    public function test_index_includes_drag_script_only_when_sortable(): void
    {
        Service::factory()->count(2)->create(['summary' => 'Ringkasan umum']);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/layanan')->assertSee("addEventListener('dragstart'", false);
        $this->actingAs($admin)->get('/admin/layanan?q=ringkasan')->assertDontSee("addEventListener('dragstart'", false);
    }

    public function test_delete_forms_use_confirmation_dialog(): void
    {
        Service::factory()->create(['title' => 'Layanan Hapus']);

        $this->actingAs($this->admin())->get('/admin/layanan')
            ->assertSee('data-confirm-message', false)
            ->assertSee('id="confirm-dialog"', false)
            ->assertDontSee('onsubmit', false);
    }
}
