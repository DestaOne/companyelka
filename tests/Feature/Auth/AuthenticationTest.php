<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_can_authenticate(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_non_admin_can_not_authenticate(): void
    {
        $user = User::factory()->create();

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_admin_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->admin()->create();

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_admin_can_logout(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    private function failLogin(User $user): \Illuminate\Testing\TestResponse
    {
        return $this->post('/admin/login', ['email' => $user->email, 'password' => 'salah']);
    }

    public function test_third_failed_attempt_locks_login_for_ten_seconds(): void
    {
        $user = User::factory()->admin()->create();

        $this->failLogin($user)->assertSessionHasErrors('email');
        $this->failLogin($user)->assertSessionHasErrors('email');
        $this->failLogin($user)->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan login. Coba lagi dalam 10 detik.']);

        // Selama terkunci, password yang benar pun ditolak.
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->travel(11)->seconds();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_attempt_counter_resets_after_lockout_and_after_successful_login(): void
    {
        $user = User::factory()->admin()->create();

        $this->failLogin($user);
        $this->failLogin($user);
        $this->failLogin($user);
        $this->travel(11)->seconds();

        // Setelah penguncian berakhir, hitungan dimulai dari nol: gagal ke-1 belum mengunci.
        $this->failLogin($user)->assertSessionHasErrors(['email' => 'Email atau password salah. Sisa percobaan: 2.']);

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password']);
        $this->post('/admin/logout');

        $this->failLogin($user)->assertSessionHasErrors(['email' => 'Email atau password salah. Sisa percobaan: 2.']);
    }

    public function test_login_button_is_disabled_while_locked_out(): void
    {
        $user = User::factory()->admin()->create();

        $this->failLogin($user);
        $this->get('/admin/login')->assertDontSee('data-seconds="10"', false);

        $this->failLogin($user);
        $this->failLogin($user);

        $this->get('/admin/login')
            ->assertSee('data-seconds="10"', false)
            ->assertSee('disabled', false)
            ->assertSeeText('Coba lagi dalam 10 detik');

        $this->travel(11)->seconds();
        $this->get('/admin/login')->assertDontSee('data-seconds="10"', false);
    }
}
