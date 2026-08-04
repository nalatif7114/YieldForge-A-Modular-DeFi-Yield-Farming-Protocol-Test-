<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Services\Security\RequestSignatureService;
use Tests\TestCase;

class RequestSignatureServiceTest extends TestCase
{
    private RequestSignatureService $signatureService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signatureService = $this->app->make(RequestSignatureService::class);
    }

    public function test_verify_valid_signature_succeeds(): void
    {
        $secret = 'SecretKey32BytesLongMinimum';
        $timestamp = (string) time();
        $nonce = 'nonce_12345';
        $body = '{"action":"sync"}';

        $payload = "{$timestamp}.{$nonce}.{$body}";
        $signature = hash_hmac('sha256', $payload, $secret);

        $valid = $this->signatureService->verify($secret, $signature, $timestamp, $nonce, $body);

        $this->assertTrue($valid);

        // Replay check fails
        $replayed = $this->signatureService->verify($secret, $signature, $timestamp, $nonce, $body);
        $this->assertFalse($replayed);
    }
}
