<?php

declare(strict_types=1);

namespace App\Services\Analytics\Contracts;

use App\Services\Analytics\DTO\KpiDTO;

interface KpiServiceInterface
{
    public function getProtocolKpis(): KpiDTO;
}
