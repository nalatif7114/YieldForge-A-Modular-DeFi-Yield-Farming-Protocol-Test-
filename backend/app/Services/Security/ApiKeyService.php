<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Support\Str;

class ApiKeyService
{
    /**
     * Create a new API key for a user.
     *
     * @param User $user
     * @param string $name
     * @param array $scopes
     * @param array|null $ipAllowlist
     * @param int|null $expiresInDays
     * @return array{model: ApiKey, raw_key: string}
     */
    public function createApiKey(
        User $user,
        string $name,
        array $scopes = ['monitoring.view', 'analytics.view'],
        ?array $ipAllowlist = null,
        ?int $expiresInDays = 90
    ): array {
        $secret = Str::random(32);
        $rawKey = "yf_live_{$secret}";
        $prefix = substr($rawKey, 0, 12);
        $keyHash = hash('sha256', $rawKey);

        $expiresAt = $expiresInDays ? now()->addDays($expiresInDays) : null;

        /** @var ApiKey $model */
        $model = ApiKey::create([
            'user_id' => $user->id,
            'name' => $name,
            'key_prefix' => $prefix,
            'key_hash' => $keyHash,
            'scopes' => $scopes,
            'ip_allowlist' => $ipAllowlist,
            'expires_at' => $expiresAt,
        ]);

        return [
            'model' => $model,
            'raw_key' => $rawKey,
        ];
    }

    /**
     * Authenticate an API key string.
     *
     * @param string $rawKey
     * @param string|null $clientIp
     * @return ApiKey|null
     */
    public function authenticate(string $rawKey, ?string $clientIp = null): ?ApiKey
    {
        $keyHash = hash('sha256', trim($rawKey));

        /** @var ApiKey|null $apiKey */
        $apiKey = ApiKey::where('key_hash', $keyHash)->first();
        if (!$apiKey) {
            return null;
        }

        if ($apiKey->expires_at && now()->greaterThan($apiKey->expires_at)) {
            return null;
        }

        if (!empty($apiKey->ip_allowlist) && $clientIp !== null) {
            if (!in_array($clientIp, $apiKey->ip_allowlist, true)) {
                return null;
            }
        }

        $apiKey->update(['last_used_at' => now()]);

        return $apiKey;
    }
}
