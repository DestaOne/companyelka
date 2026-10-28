<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_home_cards_link_to_service_only_when_published(): void
    {
        Service::factory()->create(['slug' => 'komputer']);
        Service::factory()->draft()->create(['slug' => 'mesin-kantor']);
        // "furniture" sengaja tidak dibuat (dianggap sudah dihapus).

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('layanan.show', 'komputer').'"', $html);
        $this->assertStringNotContainsString('/layanan/mesin-kantor', $html);
        $this->assertStringNotContainsString('/layanan/furniture', $html);
        $this->assertStringContainsString('href="'.route('layanan.index').'"', $html);
    }
}
