<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Menelusuri seluruh route /admin agar route baru yang lupa diberi middleware langsung ketahuan.
 */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    /** Route admin selain halaman login (yang memang untuk tamu). @return list<array{string, string}> */
    private function protectedRoutes(): array
    {
        $routes = [];

        /** @var LaravelRoute $route */
        foreach (Route::getRoutes() as $route) {
            if (! str_starts_with($route->uri(), 'admin') || in_array($route->getName(), ['admin.login'], true) || $route->uri() === 'admin/login') {
                continue;
            }

            foreach (array_diff($route->methods(), ['HEAD']) as $method) {
                // Parameter diisi nilai apa saja: middleware berjalan sebelum route model binding.
                $uri = '/'.preg_replace('/\{[^}]+\}/', '1', $route->uri());
                $routes[] = [$method, $uri];
            }
        }

        return $routes;
    }

    public function test_every_admin_route_is_registered_with_auth_and_admin_middleware(): void
    {
        $routes = $this->protectedRoutes();
        $this->assertNotEmpty($routes);

        foreach (Route::getRoutes() as $route) {
            if (! str_starts_with($route->uri(), 'admin') || $route->uri() === 'admin/login') {
                continue;
            }

            $middleware = $route->gatherMiddleware();
            $this->assertContains('auth', $middleware, "/{$route->uri()} tidak memakai middleware auth");
            $this->assertContains('admin', $middleware, "/{$route->uri()} tidak memakai middleware admin");
        }
    }

    public function test_guests_are_redirected_to_login_on_every_admin_route(): void
    {
        $leaks = [];

        foreach ($this->protectedRoutes() as [$method, $uri]) {
            $response = $this->call($method, $uri);

            if (! $response->isRedirect(route('admin.login'))) {
                $leaks[] = "{$method} {$uri} => {$response->getStatusCode()}";
            }
        }

        $this->assertSame([], $leaks, 'Route admin yang tidak mengarahkan tamu ke login');
    }

    public function test_non_admin_users_get_403_on_every_admin_route(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $leaks = [];

        foreach ($this->protectedRoutes() as [$method, $uri]) {
            $status = $this->call($method, $uri)->getStatusCode();

            if ($status !== 403) {
                $leaks[] = "{$method} {$uri} => {$status}";
            }
        }

        $this->assertSame([], $leaks, 'Route admin yang tidak menjawab 403 untuk pengguna non-admin');
    }

    public function test_admin_demoted_during_a_session_loses_access_immediately(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get('/admin')->assertOk();

        $admin->update(['is_admin' => false]);

        $this->get('/admin')->assertForbidden();
        $this->get('/admin/pesan')->assertForbidden();
    }

    public function test_logged_in_admin_is_sent_away_from_the_login_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/login')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_non_admin_account_cannot_log_in(): void
    {
        User::factory()->create(['email' => 'biasa@example.com', 'password' => 'rahasia-123', 'is_admin' => false]);

        $this->post('/admin/login', ['email' => 'biasa@example.com', 'password' => 'rahasia-123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_public_pages_do_not_require_login(): void
    {
        foreach (['/', '/layanan', '/tentang-kami', '/galeri'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
