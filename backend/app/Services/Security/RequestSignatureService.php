<?php

declare(strict_types=1);

namespace App\Services\Security;

use Illuminate\Support\Facades\Cache;

class RequestSignatureService
{
    /**
     * Verify HMAC-SHA256 signature, timestamp freshness, and nonce replay.
     *
     * @param string $secret
     * @param string $signature
     * @param string $timestamp
     * @param string $nonce
     * @param string $body
     * @param int $toleranceSeconds
     * @return bool
     */
    public function verify(
        string $secret,
        string $signature,
        string $timestamp,
        string $nonce,
        string $body = '',
        int $toleranceSeconds = 300
    ): bool {
        $currentTime = time();
        $ts = (int) $timestamp;

        // 1. Timestamp Freshness Check
        if (abs($currentTime - $ts) > $toleranceSeconds) {
            return false;
        }

        // 2. Nonce Replay Check
        $cacheKey = "request_signature_nonce:{$nonce}";
        if (Cache::has($cacheKey)) {
            return false;
        }

        // 3. HMAC-SHA256 Signature Verification
        $payload = "{$timestamp}.{$nonce}.{$body}";
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, strtolower($signature))) {
            return false;
        }

        // Cache nonce for tolerance duration
        Cache::put($cacheKey, true, $toleranceSeconds);

        return true;
    }
}
