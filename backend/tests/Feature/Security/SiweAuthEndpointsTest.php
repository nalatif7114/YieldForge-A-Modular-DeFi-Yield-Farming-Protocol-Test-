<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiweAuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_siwe_nonce_and_verify_flow(): void
    {
        $wallet = '0x86B6346984F6f9380A94bC0d2C006044649f2077';

        // 1. GET Nonce
        $nonceRes = $this->getJson("/api/v1/auth/nonce?wallet_address={$wallet}");
        $nonceRes->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['nonce', 'wallet_address', 'expires_at']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.wallet_address', strtolower($wallet));

        $nonce = $nonceRes->json('data.nonce');
        $dummySig = '0x' . str_repeat('b', 130);

        // 2. Verify SIWE Signature
        $verifyRes = $this->postJson('/api/v1/auth/verify', [
            'wallet_address' => $wallet,
            'signature' => $dummySig,
            'nonce' => $nonce,
        ]);

        $verifyRes->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['access_token', 'refresh_token', 'user']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.wallet_address', strtolower($wallet));
    }
}
