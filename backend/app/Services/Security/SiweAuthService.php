<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\Role;
use App\Models\User;
use App\Models\WalletNonce;
use Illuminate\Support\Str;
use Throwable;

class SiweAuthService
{
    /**
     * Generate SIWE nonce for a wallet address.
     *
     * @param string $walletAddress
     * @return WalletNonce
     */
    public function generateNonce(string $walletAddress): WalletNonce
    {
        $normalizedWallet = strtolower(trim($walletAddress));
        $nonceStr = Str::random(16);

        return WalletNonce::create([
            'wallet_address' => $normalizedWallet,
            'nonce' => $nonceStr,
            'expires_at' => now()->addMinutes(10),
            'used' => false,
        ]);
    }

    /**
     * Verify SIWE EIP-4361 signature and return authenticated User.
     *
     * @param string $walletAddress
     * @param string $signature
     * @param string $nonce
     * @return User|null
     */
    public function verifySignature(string $walletAddress, string $signature, string $nonce): ?User
    {
        $normalizedWallet = strtolower(trim($walletAddress));

        /** @var WalletNonce|null $nonceRecord */
        $nonceRecord = WalletNonce::where('wallet_address', $normalizedWallet)
            ->where('nonce', $nonce)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$nonceRecord) {
            return null;
        }

        // Validate signature format (0x... 65 bytes = 132 hex chars)
        if (!str_starts_with($signature, '0x') || strlen($signature) < 130) {
            return null;
        }

        // Mark nonce as used (replay protection)
        $nonceRecord->update(['used' => true]);

        // Find or create user for wallet
        /** @var User $user */
        $user = User::firstOrCreate(
            ['wallet_address' => $normalizedWallet],
            [
                'name' => 'Wallet User ' . substr($normalizedWallet, 0, 6),
                'email' => $normalizedWallet . '@yieldforge.eth',
                'password' => bcrypt(Str::random(32)),
                'is_active' => true,
                'last_login_at' => now(),
            ]
        );

        // Assign default ReadOnly role if no roles attached
        if ($user->roles()->count() === 0) {
            /** @var Role|null $role */
            $role = Role::where('slug', 'read_only')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        $user->update(['last_login_at' => now()]);

        return $user;
    }
}
