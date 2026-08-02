<?php

declare(strict_types=1);

namespace App\Services\Analytics\Contracts;

interface AnalyticsCacheInterface
{
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed;

    public function invalidateAll(): void;
}
