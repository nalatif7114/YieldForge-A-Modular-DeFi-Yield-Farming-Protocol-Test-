<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_key_creation_listing_and_deletion(): void
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'API Admin',
            'email' => 'api_admin@yieldforge.io',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        /** @var JwtService $jwtService */
        $jwtService = $this->app->make(JwtService::class);
        $token = $jwtService->issueAccessToken($user);

        // 1. Create API Key
        $createRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/security/api-keys', [
                'name' => 'Production Metrics Key',
                'scopes' => ['monitoring.view'],
            ]);

        $createRes->assertStatus(201)
            ->assertJsonStructure(['status', 'data' => ['api_key', 'raw_secret_key']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.api_key.name', 'Production Metrics Key');

        $keyId = $createRes->json('data.api_key.id');

        // 2. List API Keys
        $listRes = $this->getJson('/api/v1/security/api-keys');
        $listRes->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data');

        // 3. Delete API Key
        $deleteRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/security/api-keys/{$keyId}");

        $deleteRes->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', "API Key ID #{$keyId} revoked successfully.");
    }
}
