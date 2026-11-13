<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Sambilan',
            'email' => 'admin@sambilan.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk Pengelola');
    }

    public function test_admin_can_authenticate_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@sambilan.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@sambilan.id',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_cannot_authenticate_with_unregistered_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'unregistered@sambilan.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_logout_successfully(): void
    {
        $response = $this->actingAs($this->admin)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_redirected_to_login_when_accessing_admin_routes(): void
    {
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/perusahaans',
            '/admin/kategoris',
            '/admin/lowongans',
            '/admin/lowongans/create',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }

        $this->assertGuest();
    }

    public function test_authenticated_admin_accessing_login_page_is_redirected_to_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/login');

        $response->assertRedirect('/admin/dashboard');
    }
}
