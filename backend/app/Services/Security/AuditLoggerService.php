<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\SecurityEvent;
use App\Models\User;

class AuditLoggerService
{
    public function logAction(
        ?User $user,
        string $action,
        string $resource,
        array $payload = [],
        ?string $ip = null,
        ?string $userAgent = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'resource' => $resource,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'payload' => $payload,
        ]);
    }

    public function logSecurityEvent(
        string $eventType,
        string $severity = 'info',
        ?string $ip = null,
        array $details = []
    ): SecurityEvent {
        return SecurityEvent::create([
            'event_type' => $eventType,
            'severity' => $severity,
            'ip_address' => $ip,
            'details' => $details,
        ]);
    }

    public function logLoginAttempt(
        string $identity,
        ?string $ip = null,
        bool $successful = true
    ): LoginAttempt {
        return LoginAttempt::create([
            'identity' => $identity,
            'ip_address' => $ip,
            'successful' => $successful,
        ]);
    }
}
