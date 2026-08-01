<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StatsResource;
use App\Models\ProtocolStatistic;

class StatsController extends Controller
{
    public function index()
    {
        /** @var ProtocolStatistic $stat */
        $stat = ProtocolStatistic::firstOrCreate(
            ['id' => 1],
            [
                'total_value_locked_raw' => '0',
                'total_value_locked_formatted' => '0',
                'total_stakers_count' => 0,
                'total_events_processed' => 0,
                'total_tokens_minted_raw' => '0',
                'total_tokens_burned_raw' => '0',
                'latest_indexed_block' => 0,
            ]
        );

        return (new StatsResource($stat))->response()->setStatusCode(200);
    }
}
