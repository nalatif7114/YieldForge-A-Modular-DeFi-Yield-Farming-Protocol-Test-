<?php

declare(strict_types=1);

namespace App\Services\Analytics\Contracts;

use App\Services\Analytics\DTO\BenchmarkDTO;

interface BenchmarkCalculatorInterface
{
    public function getBenchmarks(): BenchmarkDTO;
}
