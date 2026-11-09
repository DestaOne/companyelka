<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
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

    public function test_visitor_is_offered_an_email_with_the_same_subject_and_body(): void
    {
        $this->post('/kirim-pesan', $this->payload());

        $url = session('mailto_url');
        $this->assertStringStartsWith('mailto:'.Contact::current()->email.'?subject=', $url);
        $this->assertStringContainsString('subject='.rawurlencode('Penawaran printer'), $url);
        $this->assertStringContainsString(rawurlencode('Mohon penawarannya.'), $url);
        $this->assertStringContainsString(rawurlencode('budi@example.com'), $url);

        $gmail = session('gmail_url');
        $this->assertStringStartsWith('https://mail.google.com/mail/?view=cm&fs=1&to='.rawurlencode(Contact::current()->email), $gmail);
        $this->assertStringContainsString('su='.rawurlencode('Penawaran printer'), $gmail);
        $this->assertStringContainsString(rawurlencode('Mohon penawarannya.'), $gmail);
        $this->assertStringStartsWith('https://outlook.live.com/mail/0/deeplink/compose?to=', session('outlook_url'));

        $this->get('/tentang-kami')
            ->assertSee('id="mailto-link"', false)
            ->assertSee('id="gmail-link"', false)
            ->assertSee('mailto:', false)
            ->assertSee('mail.google.com', false);
    }

    public function test_spam_message_gets_no_email_link(): void
    {
        $this->post('/kirim-pesan', $this->payload(['website' => 'http://spam.test']))
            ->assertSessionHas('message_sent')
            ->assertSessionMissing('mailto_url')
            ->assertSessionMissing('gmail_url');
    }

    public function test_message_is_validated(): void
    {
        // Rate limit diuji tersendiri; di sini pengirimannya lebih dari batas per menit.
        $this->withoutMiddleware(ThrottleRequests::class);

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
        $activity = Activity::where('description', 'like', 'Pesan baru%')->sole();
        $this->assertNull($activity->contact_message_id);
        $this->assertTrue($activity->isVisitorMessage());

        // Tetap tampil sebagai pesan pengunjung (ikon amplop, tanpa tautan ke pesan yang sudah hilang).
        $this->get('/admin')->assertOk()
            ->assertSee($activity->description)
            ->assertDontSee('>?</span>', false);
        $this->get('/admin/aktivitas')->assertOk()->assertSee('Pengunjung');
    }

    public function test_dashboard_and_activity_log_use_envelope_icon_for_visitor_messages(): void
    {
        $this->post('/kirim-pesan', $this->payload());

        $this->actingAs($this->admin())->get('/admin')->assertOk()->assertDontSee('>?</span>', false);
        $this->get('/admin/aktivitas')->assertOk()->assertDontSee('>?</span>', false);
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

    public function test_admin_can_delete_several_messages_at_once(): void
    {
        $keep = ContactMessage::create($this->payload(['name' => 'Tetap']));
        $a = ContactMessage::create($this->payload(['name' => 'Hapus A']));
        $b = ContactMessage::create($this->payload(['name' => 'Hapus B']));
        $this->actingAs($this->admin());

        $this->get('/admin/pesan')
            ->assertSee('id="select-all"', false)
            ->assertSee('name="ids[]" value="'.$a->id.'"', false)
            ->assertSee('action="'.route('admin.messages.bulk-destroy').'"', false);

        $this->delete('/admin/pesan', ['ids' => [$a->id, $b->id]])
            ->assertRedirect(route('admin.messages.index'))
            ->assertSessionHas('success', '2 pesan berhasil dihapus.');

        $this->assertSame([$keep->id], ContactMessage::pluck('id')->all());
        $this->assertDatabaseHas('activities', ['description' => 'menghapus 2 pesan masuk sekaligus']);
    }

    public function test_bulk_delete_keeps_the_unread_filter_and_ignores_unknown_ids(): void
    {
        $message = ContactMessage::create($this->payload());
        $this->actingAs($this->admin());

        $this->delete('/admin/pesan', ['ids' => [$message->id, 99999], 'filter' => 'belum-dibaca'])
            ->assertRedirect(route('admin.messages.index', ['filter' => 'belum-dibaca']))
            ->assertSessionHas('success', '1 pesan berhasil dihapus.');

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_bulk_delete_is_validated_and_admin_only(): void
    {
        $message = ContactMessage::create($this->payload());

        $this->delete('/admin/pesan', ['ids' => [$message->id]])->assertRedirect(route('admin.login'));
        $this->assertSame(1, ContactMessage::count());

        $this->actingAs($this->admin());
        $this->delete('/admin/pesan', [])->assertSessionHasErrors('ids');
        $this->delete('/admin/pesan', ['ids' => ['abc']])->assertSessionHasErrors('ids.0');
        $this->delete('/admin/pesan', ['ids' => range(1, 101)])->assertSessionHasErrors('ids');
        $this->assertSame(1, ContactMessage::count());
    }
}
