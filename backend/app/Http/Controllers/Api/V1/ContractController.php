<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Services\Blockchain\Contracts\ContractServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContractController extends Controller
{
    public function __construct(
        private readonly ContractServiceInterface $contractService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ContractResource::collection($this->contractService->getContracts());
    }
}
