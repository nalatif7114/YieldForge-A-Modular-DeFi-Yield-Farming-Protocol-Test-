<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_logout_refresh_me_flow(): void
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'John Dev',
            'email' => 'john@yieldforge.io',
            'password' => bcrypt('SecretPassword123!'),
            'is_active' => true,
        ]);

        // 1. Login
        $loginRes = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@yieldforge.io',
            'password' => 'SecretPassword123!',
        ]);

        $loginRes->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['access_token', 'refresh_token', 'user']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.email', 'john@yieldforge.io');

        $accessToken = $loginRes->json('data.access_token');
        $refreshToken = $loginRes->json('data.refresh_token');

        // 2. GET /me
        $meRes = $this->withHeader('Authorization', "Bearer {$accessToken}")
            ->getJson('/api/v1/auth/me');

        $meRes->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'John Dev')
            ->assertJsonPath('data.email', 'john@yieldforge.io');

        // 3. Refresh Token
        $refreshRes = $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        $refreshRes->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['access_token', 'refresh_token']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.token_type', 'Bearer');

        // 4. Logout
        $logoutRes = $this->withHeader('Authorization', "Bearer {$accessToken}")
            ->postJson('/api/v1/auth/logout');

        $logoutRes->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Logged out successfully.');
    }
}
