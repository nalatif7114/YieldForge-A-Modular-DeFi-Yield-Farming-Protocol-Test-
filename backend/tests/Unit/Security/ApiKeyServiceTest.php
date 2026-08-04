<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Models\User;
use App\Services\Security\ApiKeyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApiKeyService $apiKeyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiKeyService = $this->app->make(ApiKeyService::class);
    }

    public function test_create_and_authenticate_api_key(): void
    {
        /** @var User $user */
        $user = User::create(['name' => 'API User', 'email' => 'api@yieldforge.io', 'password' => bcrypt('password')]);

        $result = $this->apiKeyService->createApiKey($user, 'Dev Key', ['monitoring.view'], ['127.0.0.1'], 30);

        $this->assertIsArray($result);
        $this->assertStringStartsWith('yf_live_', $result['raw_key']);

        $authenticatedKey = $this->apiKeyService->authenticate($result['raw_key'], '127.0.0.1');

        $this->assertNotNull($authenticatedKey);
        $this->assertEquals($result['model']->id, $authenticatedKey->id);

        // IP restriction check
        $invalidIpKey = $this->apiKeyService->authenticate($result['raw_key'], '192.168.1.100');
        $this->assertNull($invalidIpKey);
    }
}
