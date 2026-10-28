<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'phone' => '0811 3383 679',
            'email' => 'budi@example.com',
            'subject' => 'Penawaran printer',
            'body' => "Halo, saya butuh 5 printer.\nMohon penawarannya.",
        ], $overrides);
    }

    private function admin(): User
    {
        return User::factory()->admin()->create(['created_at' => now()->subDay()]);
    }

    public function test_about_page_shows_the_form_instead_of_the_map(): void
    {
        $this->get('/tentang-kami')
            ->assertOk()
            ->assertSee('Kirim Pesan')
            ->assertSee('action="'.route('pesan.store').'"', false)
            ->assertDontSee('<iframe', false);
    }

    public function test_visitor_can_send_a_message(): void
    {
        $this->post('/kirim-pesan', $this->payload())
            ->assertRedirect(url('/tentang-kami').'#kontak')
            ->assertSessionHas('message_sent');

        $message = ContactMessage::sole();
        $this->assertSame('Budi Santoso', $message->name);
        $this->assertNull($message->read_at);

        $this->get('/tentang-kami')->assertSee('pesan Anda sudah terkirim');
    }

    public function test_message_is_validated(): void
    {
        // Rate limit diuji tersendiri; di sini pengirimannya lebih dari batas per menit.
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $this->post('/kirim-pesan', [])->assertSessionHasErrors(['name', 'phone', 'email', 'subject', 'body']);
        $this->post('/kirim-pesan', $this->payload(['phone' => 'abc']))->assertSessionHasErrors('phone');
        $this->post('/kirim-pesan', $this->payload(['email' => 'bukan-email']))->assertSessionHasErrors('email');
        $this->post('/kirim-pesan', $this->payload(['body' => 'pendek']))->assertSessionHasErrors('body');
        $this->post('/kirim-pesan', $this->payload(['subject' => str_repeat('a', 151)]))->assertSessionHasErrors('subject');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_validation_failure_returns_to_the_form_section(): void
    {
        $this->from('/tentang-kami')->post('/kirim-pesan', $this->payload(['email' => 'x']))
            ->assertRedirect(url('/tentang-kami').'#kontak');
    }

    public function test_honeypot_submissions_are_discarded_silently(): void
    {
        $this->post('/kirim-pesan', $this->payload(['website' => 'http://spam.example']))
            ->assertRedirect(url('/tentang-kami').'#kontak')
            ->assertSessionHas('message_sent');

        $this->assertSame(0, ContactMessage::count());
        $this->assertSame(0, Activity::count());
    }

    public function test_sending_is_limited_to_the_configured_number_per_hour(): void
    {
        $this->assertSame(10, config('contact.max_per_hour'));

        foreach (range(1, 10) as $i) {
            $this->post('/kirim-pesan', $this->payload())->assertRedirect();
        }

        $this->post('/kirim-pesan', $this->payload())->assertStatus(429);
        $this->assertSame(10, ContactMessage::count());

        // Satu jam kemudian pengunjung boleh mengirim lagi.
        $this->travel(61)->minutes();
        $this->post('/kirim-pesan', $this->payload())->assertRedirect();
    }

    public function test_the_limit_follows_the_configured_value(): void
    {
        config(['contact.max_per_hour' => 2]);

        $this->post('/kirim-pesan', $this->payload())->assertRedirect();
        $this->post('/kirim-pesan', $this->payload())->assertRedirect();
        $this->post('/kirim-pesan', $this->payload())->assertStatus(429);
    }

    public function test_new_message_appears_in_the_bell_and_links_to_the_message(): void
    {
        $this->post('/kirim-pesan', $this->payload());
        $message = ContactMessage::sole();

        $this->actingAs($this->admin())->get('/admin')
            ->assertOk()
            ->assertSee('id="activity-badge"', false)
            ->assertSee('Pesan baru dari Budi Santoso: Penawaran printer')
            ->assertSee(route('admin.messages.show', $message), false);
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        $message = ContactMessage::create($this->payload());

        $this->get('/admin/pesan')->assertRedirect(route('admin.login'));
        $this->get("/admin/pesan/{$message->id}")->assertRedirect(route('admin.login'));
        $this->delete("/admin/pesan/{$message->id}")->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create())->get('/admin/pesan')->assertForbidden();
        $this->assertNotNull(ContactMessage::find($message->id));
    }

    public function test_index_lists_messages_with_unread_count_and_filter(): void
    {
        ContactMessage::create($this->payload(['subject' => 'Belum dibaca satu']));
        ContactMessage::create($this->payload(['subject' => 'Sudah dibaca dua', 'read_at' => now()]));

        $this->actingAs($this->admin());

        $this->get('/admin/pesan')->assertOk()->assertSee('Belum dibaca satu')->assertSee('Sudah dibaca dua')->assertSee('Belum dibaca (1)');
        $this->get('/admin/pesan?filter=belum-dibaca')->assertOk()->assertSee('Belum dibaca satu')->assertDontSee('Sudah dibaca dua');
    }

    public function test_opening_a_message_marks_it_as_read(): void
    {
        $message = ContactMessage::create($this->payload());
        $this->actingAs($this->admin());

        $this->get("/admin/pesan/{$message->id}")
            ->assertOk()
            ->assertSee('Penawaran printer')
            ->assertSee('budi@example.com')
            ->assertSee('Mohon penawarannya.')
            ->assertSee('https://wa.me/628113383679', false);

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_message_content_is_escaped(): void
    {
        $message = ContactMessage::create($this->payload(['body' => '<script>alert(1)</script> halo semua']));

        $this->actingAs($this->admin())->get("/admin/pesan/{$message->id}")
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_admin_can_delete_a_message(): void
    {
        $this->post('/kirim-pesan', $this->payload());
        $message = ContactMessage::sole();

        $this->actingAs($this->admin())->delete("/admin/pesan/{$message->id}")
            ->assertRedirect(route('admin.messages.index'))
            ->assertSessionHas('success');

        $this->assertSame(0, ContactMessage::count());
        // Aktivitasnya tetap ada, hanya tautan ke pesannya yang dilepas.
        $this->assertNull(Activity::where('description', 'like', 'Pesan baru%')->sole()->contact_message_id);
        $this->get('/admin')->assertOk();
    }

    public function test_sidebar_shows_the_unread_message_count(): void
    {
        ContactMessage::create($this->payload());
        ContactMessage::create($this->payload());

        $this->actingAs($this->admin())->get('/admin')->assertSee('Pesan Masuk')->assertSeeInOrder(['Pesan Masuk', '>2<'], false);
    }

    public function test_detail_shows_new_badge_once_and_navigates_between_messages(): void
    {
        $oldest = ContactMessage::create($this->payload(['subject' => 'Pesan paling lama']));
        $middle = ContactMessage::create($this->payload(['subject' => 'Pesan tengah']));
        $newest = ContactMessage::create($this->payload(['subject' => 'Pesan terbaru']));

        $this->actingAs($this->admin());

        $this->get("/admin/pesan/{$middle->id}")
            ->assertOk()
            ->assertSee('Baru')
            ->assertSee(route('admin.messages.show', $oldest), false)
            ->assertSee(route('admin.messages.show', $newest), false);

        $this->get("/admin/pesan/{$middle->id}")->assertSee('Sudah dibaca')->assertDontSee('>Baru<', false);

        $this->get("/admin/pesan/{$newest->id}")->assertDontSee('aria-label="Pesan berikutnya"', false);
        $this->get("/admin/pesan/{$oldest->id}")->assertDontSee('aria-label="Pesan sebelumnya"', false);
    }

    public function test_admin_can_delete_from_the_detail_page_and_whatsapp_reply_is_prefilled(): void
    {
        $message = ContactMessage::create($this->payload());
        $this->actingAs($this->admin());

        $this->get("/admin/pesan/{$message->id}")
            ->assertSee('action="'.route('admin.messages.destroy', $message).'"', false)
            ->assertSee('https://wa.me/628113383679?text=', false)
            ->assertSee('href="tel:08113383679"', false);
    }
}
