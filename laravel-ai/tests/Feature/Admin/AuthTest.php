<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_see_login_form(): void
    {
        $response = $this->get(route('admin.login.form'));

        $response
            ->assertOk()
            ->assertViewIs('admin.auth.login');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'admin@example.com',
            'password' => 'invalid-password',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_login_is_rate_limited_after_too_many_attempts(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('admin.login'), [
                'email' => 'admin@example.com',
                'password' => 'invalid-password',
            ])
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        }

        $response = $this->post(route('admin.login'), [
            'email' => 'admin@example.com',
            'password' => 'invalid-password',
        ]);

        $response->assertStatus(429);
        $this->assertGuest();
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }

    public function test_non_admin_cannot_login_into_admin_panel(): void
    {
        User::factory()->create([
            'email' => 'member@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'member@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_legacy_is_admin_without_role_cannot_login_into_admin_panel(): void
    {
        User::factory()->create([
            'email' => 'legacy.admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'role_id' => null,
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => 'legacy.admin@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_non_admin_cannot_access_dashboard_even_when_authenticated(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('admin.dashboard'));

        $response
            ->assertRedirect('/')
            ->assertSessionHas('error');
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login.form'));
    }

    public function test_admin_can_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
