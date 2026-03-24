<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_a_sanctum_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-suite',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Đăng nhập thành công.',
                'data' => [
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                    ],
                ],
            ])
            ->assertJsonPath('data.user.name', $user->name);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_returns_error_response_when_credentials_are_invalid(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Thông tin đăng nhập không chính xác.',
                'errors' => [
                    'email' => ['Thông tin đăng nhập không chính xác.'],
                ],
            ]);
    }

    public function test_logout_requires_sanctum_authentication_and_returns_standard_error_response(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Chưa được xác thực.',
                'errors' => [],
            ]);
    }

    public function test_authenticated_user_can_logout_and_revoke_current_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-suite',
        ]);

        $token = $loginResponse->json('data.access_token');

        $logoutResponse = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout');

        $logoutResponse
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Đăng xuất thành công.',
                'data' => [],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
