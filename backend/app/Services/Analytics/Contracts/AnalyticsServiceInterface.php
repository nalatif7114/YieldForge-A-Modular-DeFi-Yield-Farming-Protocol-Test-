<?php

declare(strict_types=1);

namespace App\Services\Analytics\Contracts;

use App\Services\Analytics\DTO\APYHistoryDTO;
use App\Services\Analytics\DTO\PoolAnalyticsDTO;
use App\Services\Analytics\DTO\ProtocolAnalyticsDTO;
use App\Services\Analytics\DTO\RewardAnalyticsDTO;
use App\Services\Analytics\DTO\TVLHistoryDTO;
use App\Services\Analytics\DTO\WalletAnalyticsDTO;

interface AnalyticsServiceInterface
{
    public function getOverview(): array;

    public function getTvlHistory(string $window = '30d'): TVLHistoryDTO;

    public function getApyHistory(string $window = '30d'): APYHistoryDTO;

    public function getProtocolAnalytics(): ProtocolAnalyticsDTO;

    public function getPoolAnalytics(?string $poolId = null): PoolAnalyticsDTO|array;

    public function getWalletAnalytics(string $wallet): WalletAnalyticsDTO;

    public function getRewardAnalytics(): RewardAnalyticsDTO;
}
