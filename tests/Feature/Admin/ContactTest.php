<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'address' => 'Jl. Raya Baru No. 9, Ubud, Gianyar',
            'whatsapp' => '0812-3456-7890',
            'email' => 'Halo@Contoh.com',
        ], $override);
    }

    public function test_guest_and_non_admin_cannot_access(): void
    {
        $this->get('/admin/tentang/kontak')->assertRedirect(route('admin.login'));
        $this->put('/admin/tentang/kontak', $this->payload())->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get('/admin/tentang/kontak')->assertForbidden();
    }

    public function test_admin_sees_default_contact_in_form(): void
    {
        $this->actingAs($this->admin())->get('/admin/tentang/kontak')
            ->assertOk()
            ->assertSee('elkamandiri.cv@gmail.com')
            ->assertSee('08113383679');
    }

    public function test_one_edit_changes_about_page_and_footer(): void
    {
        $this->actingAs($this->admin())->put('/admin/tentang/kontak', $this->payload())
            ->assertRedirect(route('admin.contact.edit'))
            ->assertSessionHas('success');

        $contact = Contact::current();
        $this->assertSame('6281234567890', $contact->whatsapp);
        $this->assertSame('halo@contoh.com', $contact->email);

        foreach (['/tentang-kami', '/'] as $url) {
            $this->get($url)
                ->assertSee('Jl. Raya Baru No. 9, Ubud, Gianyar')
                ->assertSee('halo@contoh.com')
                ->assertSee('081234567890')
                ->assertSee('wa.me/6281234567890', false)
                ->assertDontSee('elkamandiri.cv@gmail.com');
        }

        $this->get('/tentang-kami')->assertSee('Jl.%20Raya%20Baru%20No.%209%2C%20Ubud%2C%20Gianyar', false);
    }

    public function test_whatsapp_accepts_international_format(): void
    {
        $this->actingAs($this->admin())->put('/admin/tentang/kontak', $this->payload(['whatsapp' => '+62 811 3383 679']))
            ->assertSessionHasNoErrors();

        $this->assertSame('628113383679', Contact::current()->whatsapp);
    }

    public function test_validation(): void
    {
        $this->actingAs($this->admin())->put('/admin/tentang/kontak', ['address' => '', 'whatsapp' => '', 'email' => ''])
            ->assertSessionHasErrors(['address', 'whatsapp', 'email']);

        $this->put('/admin/tentang/kontak', $this->payload(['whatsapp' => '12345']))->assertSessionHasErrors('whatsapp');
        $this->put('/admin/tentang/kontak', $this->payload(['whatsapp' => '+1 415 555 0100']))->assertSessionHasErrors('whatsapp');
        $this->put('/admin/tentang/kontak', $this->payload(['email' => 'bukan-email']))->assertSessionHasErrors('email');
    }

    public function test_contact_values_are_escaped(): void
    {
        $this->actingAs($this->admin())->put('/admin/tentang/kontak', $this->payload(['address' => '<script>alert(1)</script>']));

        $this->get('/tentang-kami')->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_maps_embed_link_or_iframe_code_is_used_for_the_map(): void
    {
        $embed = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1d3944';
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => '<iframe src="'.$embed.'" width="600"></iframe>']))
            ->assertSessionHasNoErrors();
        $this->assertSame($embed, Contact::current()->maps_link);

        $this->assertSame($embed, Contact::current()->maps_embed_url);
    }

    public function test_place_link_embeds_by_place_name_and_empty_link_falls_back_to_address(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => 'https://www.google.com/maps/place/Toko+Elka/@-8.6,115.2,17z']));
        $this->assertStringContainsString('maps?q=Toko%20Elka', Contact::current()->maps_embed_url);

        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => '']));
        $this->assertNull(Contact::current()->maps_link);
        $this->assertStringContainsString('maps?q=Jl.%20Raya%20Baru', Contact::current()->maps_embed_url);
    }

    public function test_maps_link_must_be_a_google_maps_https_link(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => 'https://contoh.com/peta']))->assertSessionHasErrors('maps_link');
        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => 'http://www.google.com/maps/embed?pb=1']))->assertSessionHasErrors('maps_link');
        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => 'javascript:alert(1)']))->assertSessionHasErrors('maps_link');
    }

    public function test_custom_whatsapp_and_email_links_are_used_and_empty_falls_back_to_generated(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/kontak', $this->payload([
            'whatsapp_link' => 'https://wa.me/6281234567890?text=Halo%20Elka',
            'email_link' => 'mailto:halo@contoh.com?subject=Konsultasi',
        ]))->assertSessionHasNoErrors();

        foreach (['/tentang-kami', '/'] as $url) {
            $this->get($url)
                ->assertSee('href="https://wa.me/6281234567890?text=Halo%20Elka"', false)
                ->assertSee('href="mailto:halo@contoh.com?subject=Konsultasi"', false);
        }

        $this->put('/admin/tentang/kontak', $this->payload(['whatsapp_link' => '', 'email_link' => '']));
        $contact = Contact::current();
        $this->assertNull($contact->whatsapp_link);
        $this->assertNull($contact->email_link);
        $this->assertSame('mailto:halo@contoh.com', $contact->email_url);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $contact->whatsapp_url);
    }

    public function test_whatsapp_and_email_links_are_restricted_to_safe_schemes_and_hosts(): void
    {
        $this->actingAs($this->admin());

        foreach (['https://contoh.com/wa', 'http://wa.me/628123', 'javascript:alert(1)'] as $bad) {
            $this->put('/admin/tentang/kontak', $this->payload(['whatsapp_link' => $bad]))->assertSessionHasErrors('whatsapp_link');
        }

        foreach (['halo@contoh.com', 'https://contoh.com', 'javascript:alert(1)', 'mailto:bukan-email'] as $bad) {
            $this->put('/admin/tentang/kontak', $this->payload(['email_link' => $bad]))->assertSessionHasErrors('email_link');
        }
    }

    public function test_short_maps_link_is_resolved_so_address_link_and_map_use_the_same_location(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'maps.app.goo.gl/*' => \Illuminate\Support\Facades\Http::response('', 302, ['Location' => 'https://www.google.com/maps/place/Toko+Elka+Mandiri/@-8.68,115.21,17z/data=!3m1']),
        ]);
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => 'https://maps.app.goo.gl/abc123']))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Kontak berhasil diperbarui.');

        $this->get('/tentang-kami')
            ->assertSee('href="https://maps.app.goo.gl/abc123"', false);
        $this->assertStringContainsString('maps?q=Toko%20Elka%20Mandiri', Contact::current()->maps_embed_url);
    }

    public function test_short_link_pointing_outside_google_is_not_followed(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'maps.app.goo.gl/*' => \Illuminate\Support\Facades\Http::response('', 302, ['Location' => 'http://169.254.169.254/latest']),
        ]);
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => 'https://maps.app.goo.gl/abc123']))
            ->assertSessionHas('success', fn ($m) => str_contains($m, 'peta memakai teks alamat'));

        \Illuminate\Support\Facades\Http::assertSentCount(1);
        $this->assertNull(Contact::current()->maps_resolved);
        $this->assertStringContainsString('maps?q=Jl.%20Raya%20Baru', Contact::current()->maps_embed_url);
    }

    public function test_coordinates_in_a_maps_link_are_used_for_the_map(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/tentang/kontak', $this->payload(['maps_link' => 'https://www.google.com/maps/@-8.6705,115.2126,15z']));

        $this->assertStringContainsString('maps?q=-8.6705%2C115.2126', Contact::current()->maps_embed_url);
    }
}
