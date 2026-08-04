<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Models\User;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JwtServiceTest extends TestCase
{
    use RefreshDatabase;

    private JwtService $jwtService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jwtService = $this->app->make(JwtService::class);
    }

    public function test_issue_access_token_and_validate_payload(): void
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Alice Dev',
            'email' => 'alice@yieldforge.io',
            'password' => bcrypt('password'),
            'wallet_address' => '0x1111111111111111111111111111111111111111',
            'is_active' => true,
        ]);

        $token = $this->jwtService->issueAccessToken($user, 60);

        $this->assertIsString($token);
        $this->assertCount(3, explode('.', $token));

        $payload = $this->jwtService->validateToken($token);

        $this->assertIsArray($payload);
        $this->assertEquals((string) $user->id, $payload['sub']);
        $this->assertEquals('alice@yieldforge.io', $payload['email']);
        $this->assertEquals('0x1111111111111111111111111111111111111111', $payload['wallet']);
    }

    public function test_expired_or_tampered_token_validation_fails(): void
    {
        /** @var User $user */
        $user = User::create([
            'name' => 'Bob Dev',
            'email' => 'bob@yieldforge.io',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $token = $this->jwtService->issueAccessToken($user, -5); // Already expired

        $payload = $this->jwtService->validateToken($token);
        $this->assertNull($payload);

        $validToken = $this->jwtService->issueAccessToken($user, 60);
        $tamperedToken = $validToken . 'tampered';
        $this->assertNull($this->jwtService->validateToken($tamperedToken));
    }
}
