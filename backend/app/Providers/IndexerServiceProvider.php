<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Indexer\BlockCursor;
use App\Services\Indexer\BlockchainIndexer;

use App\Services\Indexer\Contracts\BlockchainIndexerInterface;
use App\Services\Indexer\Contracts\EventDispatcherInterface;
use App\Services\Indexer\Contracts\ProjectionRegistryInterface;
use App\Services\Indexer\Contracts\ReorgDetectorInterface;
use App\Services\Indexer\Contracts\ReplayEngineInterface;
use App\Services\Indexer\Contracts\SyncManagerInterface;
use App\Services\Indexer\DomainEvents\DomainEventFactory;

use App\Services\Indexer\EventDispatcher;
use App\Services\Indexer\IndexerHealthService;
use App\Services\Indexer\IndexerMetricsService;
use App\Services\Indexer\LogProcessor;
use App\Services\Indexer\ProjectionRegistry;
use App\Services\Indexer\ReorgDetector;
use App\Services\Indexer\ReplayEngine;
use App\Services\Indexer\SyncManager;

use App\Services\Projection\BlockProjection;
use App\Services\Projection\PoolProjection;
use App\Services\Projection\ProtocolProjection;
use App\Services\Projection\RewardProjection;
use App\Services\Projection\WalletProjection;

use Illuminate\Support\ServiceProvider;

class IndexerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BlockCursor::class);
        $this->app->singleton(DomainEventFactory::class);
        $this->app->singleton(IndexerMetricsService::class);
        $this->app->singleton(IndexerHealthService::class);
        $this->app->singleton(LogProcessor::class);

        $this->app->singleton(SyncManagerInterface::class, SyncManager::class);
        $this->app->singleton(ReorgDetectorInterface::class, ReorgDetector::class);

        $this->app->singleton(ProjectionRegistryInterface::class, function ($app) {
            $registry = new ProjectionRegistry($app->make('log'));
            $registry->register($app->make(BlockProjection::class));
            $registry->register($app->make(WalletProjection::class));
            $registry->register($app->make(PoolProjection::class));
            $registry->register($app->make(RewardProjection::class));
            $registry->register($app->make(ProtocolProjection::class));

            return $registry;
        });

        $this->app->singleton(EventDispatcherInterface::class, EventDispatcher::class);
        $this->app->singleton(BlockchainIndexerInterface::class, BlockchainIndexer::class);
        $this->app->singleton(ReplayEngineInterface::class, ReplayEngine::class);
    }

    public function boot(): void
    {
        //
    }
}
