<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NetworkResource;
use App\Services\Blockchain\Contracts\NetworkServiceInterface;

class NetworkController extends Controller
{
    public function __construct(
        private readonly NetworkServiceInterface $networkService
    ) {}

    public function index(): NetworkResource
    {
        return new NetworkResource($this->networkService->getNetworkInfo());
    }
}
