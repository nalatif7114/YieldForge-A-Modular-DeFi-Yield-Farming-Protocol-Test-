<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Contracts;

use App\Services\Blockchain\DTO\NetworkDTO;

interface NetworkServiceInterface
{
    /**
     * Get network details including status, chain ID, and current block number.
     *
     * @return NetworkDTO
     */
    public function getNetworkInfo(): NetworkDTO;

    /**
     * Check if network node is healthy and reachable.
     *
     * @return bool
     */
    public function isHealthy(): bool;
}
