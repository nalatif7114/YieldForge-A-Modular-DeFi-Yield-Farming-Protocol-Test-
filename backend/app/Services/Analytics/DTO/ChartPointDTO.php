<?php

declare(strict_types=1);

namespace App\Services\Analytics\DTO;

readonly class ChartPointDTO
{
    public function __construct(
        public string|int $timestamp,
        public float|string $value
    ) {}

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'value' => $this->value,
        ];
    }
}
