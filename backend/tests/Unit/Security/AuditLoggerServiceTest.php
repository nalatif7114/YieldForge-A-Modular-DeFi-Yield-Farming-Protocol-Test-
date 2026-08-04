<?php

declare(strict_types=1);

namespace Tests\Unit\Security;

use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Services\Security\AuditLoggerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLoggerServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuditLoggerService $auditLogger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auditLogger = $this->app->make(AuditLoggerService::class);
    }

    public function test_log_action_creates_audit_log_record(): void
    {
        /** @var User $user */
        $user = User::create(['name' => 'Audit Test User', 'email' => 'audit@yieldforge.io', 'password' => bcrypt('password')]);

        $log = $this->auditLogger->logAction($user, 'TestAuditAction', 'Security', ['key' => 'value'], '127.0.0.1', 'Mozilla/5.0');

        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals('TestAuditAction', $log->action);
        $this->assertDatabaseHas('audit_logs', ['action' => 'TestAuditAction']);
    }

    public function test_log_security_event_creates_event_record(): void
    {
        $event = $this->auditLogger->logSecurityEvent('SuspiciousActivity', 'warning', '10.0.0.1', ['reason' => 'failed_login_burst']);

        $this->assertInstanceOf(SecurityEvent::class, $event);
        $this->assertEquals('SuspiciousActivity', $event->event_type);
        $this->assertEquals('warning', $event->severity);
        $this->assertDatabaseHas('security_events', ['event_type' => 'SuspiciousActivity']);
    }

    public function test_log_login_attempt_records_success_or_failure(): void
    {
        $successAttempt = $this->auditLogger->logLoginAttempt('user@yieldforge.io', '127.0.0.1', true);
        $failedAttempt = $this->auditLogger->logLoginAttempt('hacker@yieldforge.io', '192.168.1.1', false);

        $this->assertInstanceOf(LoginAttempt::class, $successAttempt);
        $this->assertTrue($successAttempt->successful);
        $this->assertFalse($failedAttempt->successful);
        $this->assertDatabaseHas('login_attempts', ['identity' => 'hacker@yieldforge.io', 'successful' => false]);
    }
}
