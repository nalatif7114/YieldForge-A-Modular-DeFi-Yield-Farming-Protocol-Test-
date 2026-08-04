<?php

declare(strict_types=1);

namespace App\Services\Monitoring\DTO;

use App\Models\MonitoringAlert;

readonly class AlertDTO
{
    public function __construct(
        public int $id,
        public string $ruleName,
        public string $severity,
        public string $component,
        public string $message,
        public string $status,
        public ?array $context = null,
        public ?string $createdAt = null,
        public ?string $acknowledgedAt = null,
        public ?string $resolvedAt = null
    ) {}

    public static function fromModel(MonitoringAlert $model): self
    {
        return new self(
            id: (int) $model->id,
            ruleName: (string) $model->rule_name,
            severity: (string) $model->severity,
            component: (string) $model->component,
            message: (string) $model->message,
            status: (string) $model->status,
            context: $model->context,
            createdAt: $model->created_at?->toIso8601String(),
            acknowledgedAt: $model->acknowledged_at?->toIso8601String(),
            resolvedAt: $model->resolved_at?->toIso8601String()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rule_name' => $this->ruleName,
            'severity' => $this->severity,
            'component' => $this->component,
            'message' => $this->message,
            'status' => $this->status,
            'context' => $this->context,
            'created_at' => $this->createdAt,
            'acknowledged_at' => $this->acknowledgedAt,
            'resolved_at' => $this->resolvedAt,
        ];
    }
}
