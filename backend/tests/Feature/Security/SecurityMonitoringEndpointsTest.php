<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityMonitoringEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_dashboard_endpoint(): void
    {
        $res = $this->getJson('/api/v1/security/dashboard');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['total_users', 'active_api_keys', 'active_sessions', 'failed_logins_24h', 'status']])
            ->assertJsonPath('status', 'success');
    }

    public function test_security_audit_endpoint(): void
    {
        $res = $this->getJson('/api/v1/security/audit');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data'])
            ->assertJsonPath('status', 'success');
    }

    public function test_security_rate_limit_endpoint(): void
    {
        $res = $this->getJson('/api/v1/security/rate-limit');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['anonymous_limit', 'authenticated_limit', 'admin_limit']])
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.anonymous_limit', 60);
    }

    public function test_security_sessions_endpoint(): void
    {
        $res = $this->getJson('/api/v1/security/sessions');
        $res->assertStatus(200)
            ->assertJsonStructure(['status', 'data'])
            ->assertJsonPath('status', 'success');
    }
}
