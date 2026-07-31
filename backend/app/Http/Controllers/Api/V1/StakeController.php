<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StakeResource;
use App\Services\Blockchain\Contracts\ContractServiceInterface;

class StakeController extends Controller
{
    public function __construct(
        private readonly ContractServiceInterface $contractService
    ) {}

    public function show(string $wallet): StakeResource
    {
        return new StakeResource($this->contractService->getStakeInfo($wallet));
    }
}
