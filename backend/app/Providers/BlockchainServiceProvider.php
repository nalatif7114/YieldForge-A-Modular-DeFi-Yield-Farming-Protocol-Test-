<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Blockchain\AbiLoader;
use App\Services\Blockchain\ContractService;
use App\Services\Blockchain\Contracts\AbiLoaderInterface;
use App\Services\Blockchain\Contracts\ContractServiceInterface;
use App\Services\Blockchain\Contracts\EventServiceInterface;
use App\Services\Blockchain\Contracts\NetworkServiceInterface;
use App\Services\Blockchain\Contracts\RpcClientInterface;
use App\Services\Blockchain\EventService;
use App\Services\Blockchain\NetworkService;
use App\Services\Blockchain\RpcClient;
use App\Services\Blockchain\Support\EthereumCodec;
use Illuminate\Support\ServiceProvider;

class BlockchainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EthereumCodec::class, fn () => new EthereumCodec());

        $this->app->singleton(RpcClientInterface::class, RpcClient::class);
        $this->app->singleton(AbiLoaderInterface::class, AbiLoader::class);
        $this->app->singleton(NetworkServiceInterface::class, NetworkService::class);
        $this->app->singleton(ContractServiceInterface::class, ContractService::class);
        $this->app->singleton(EventServiceInterface::class, EventService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
