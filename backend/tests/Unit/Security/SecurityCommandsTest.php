<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_audit_command_executes_successfully(): void
    {
        $this->artisan('security:audit')
            ->expectsOutputToContain('YieldForge Security Audit Report')
            ->assertExitCode(0);
    }

    public function test_security_cleanup_command_executes_successfully(): void
    {
        $this->artisan('security:cleanup')
            ->expectsOutputToContain('Purging expired security records')
            ->assertExitCode(0);
    }

    public function test_security_apikey_rotate_command_executes_successfully(): void
    {
        /** @var User $user */
        $user = User::create(['name' => 'Rotate User', 'email' => 'rotate@yieldforge.io', 'password' => bcrypt('password')]);
        $apiKey = ApiKey::create([
            'user_id' => $user->id,
            'name' => 'Old Key',
            'key_prefix' => 'yf_live_old',
            'key_hash' => hash('sha256', 'yf_live_old_secret'),
        ]);

        $this->artisan('security:apikey:rotate', ['id' => $apiKey->id])
            ->expectsOutputToContain('rotated successfully')
            ->assertExitCode(0);
    }
}
