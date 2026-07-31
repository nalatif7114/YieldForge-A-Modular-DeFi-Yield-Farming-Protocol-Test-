<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RewardResource;
use App\Services\Blockchain\Contracts\ContractServiceInterface;

class RewardController extends Controller
{
    public function __construct(
        private readonly ContractServiceInterface $contractService
    ) {}

    public function show(string $wallet): RewardResource
    {
        return new RewardResource($this->contractService->getRewardInfo($wallet));
    }
}
