<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\User;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Throwable;

class JwtService
{
    private string $secret;

    public function __construct(
        private readonly ConfigRepository $config
    ) {
        $this->secret = (string) $this->config->get('app.key', 'YieldForgeSecretKey32BytesLongMin');
    }

    /**
     * Generate JWT access token for user.
     *
     * @param User $user
     * @param int $ttlMinutes
     * @return string
     */
    public function issueAccessToken(User $user, int $ttlMinutes = 60): string
    {
        $now = time();
        $exp = $now + ($ttlMinutes * 60);

        $roles = $user->roles->pluck('slug')->all();
        $permissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $perm) {
                $permissions[] = $perm->slug;
            }
        }

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $payload = [
            'sub' => (string) $user->id,
            'email' => $user->email,
            'wallet' => $user->wallet_address,
            'roles' => array_values(array_unique($roles)),
            'permissions' => array_values(array_unique($permissions)),
            'iat' => $now,
            'exp' => $exp,
        ];

        return $this->encode($header, $payload);
    }

    /**
     * Decode and validate JWT access token string.
     *
     * @param string $token
     * @return array|null
     */
    public function validateToken(string $token): ?array
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return null;
            }

            [$headerB64, $payloadB64, $signatureB64] = $parts;

            $signature = $this->base64UrlDecode($signatureB64);
            $expectedSignature = hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $this->secret, true);

            if (!hash_equals($expectedSignature, $signature)) {
                return null;
            }

            $payload = json_decode($this->base64UrlDecode($payloadB64), true);
            if (!is_array($payload)) {
                return null;
            }

            if (isset($payload['exp']) && time() >= (int) $payload['exp']) {
                return null;
            }

            return $payload;
        } catch (Throwable) {
            return null;
        }
    }

    private function encode(array $header, array $payload): string
    {
        $headerB64 = $this->base64UrlEncode((string) json_encode($header));
        $payloadB64 = $this->base64UrlEncode((string) json_encode($payload));
        $signature = hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $this->secret, true);
        $signatureB64 = $this->base64UrlEncode($signature);

        return "{$headerB64}.{$payloadB64}.{$signatureB64}";
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }
}
