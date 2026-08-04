<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Models\WalletNonce;
use App\Services\Security\SiweAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiweAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private SiweAuthService $siweAuthService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->siweAuthService = $this->app->make(SiweAuthService::class);
    }

    public function test_generate_nonce_creates_wallet_nonce_record(): void
    {
        $wallet = '0x86B6346984F6f9380A94bC0d2C006044649f2077';
        $nonce = $this->siweAuthService->generateNonce($wallet);

        $this->assertInstanceOf(WalletNonce::class, $nonce);
        $this->assertEquals(strtolower($wallet), $nonce->wallet_address);
        $this->assertFalse($nonce->used);
        $this->assertDatabaseHas('wallet_nonces', [
            'wallet_address' => strtolower($wallet),
            'nonce' => $nonce->nonce,
        ]);
    }

    public function test_verify_signature_creates_user_and_marks_nonce_used(): void
    {
        $wallet = '0x86B6346984F6f9380A94bC0d2C006044649f2077';
        $nonceRecord = $this->siweAuthService->generateNonce($wallet);
        $dummySignature = '0x' . str_repeat('a', 130);

        $user = $this->siweAuthService->verifySignature($wallet, $dummySignature, $nonceRecord->nonce);

        $this->assertNotNull($user);
        $this->assertEquals(strtolower($wallet), $user->wallet_address);
        $this->assertDatabaseHas('wallet_nonces', [
            'id' => $nonceRecord->id,
            'used' => true,
        ]);

        // Second verification with same nonce fails (replay protection)
        $replayedUser = $this->siweAuthService->verifySignature($wallet, $dummySignature, $nonceRecord->nonce);
        $this->assertNull($replayedUser);
    }
}
