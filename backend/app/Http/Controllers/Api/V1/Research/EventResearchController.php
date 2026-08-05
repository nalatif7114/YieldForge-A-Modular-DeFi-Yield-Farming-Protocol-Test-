<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Research;

use App\Http\Controllers\Controller;
use App\Models\BlockchainEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventResearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $eventName = $request->query('event_name');
        $limit = (int) $request->query('limit', 50);

        $query = BlockchainEvent::query()->orderByDesc('block_number');
        if ($eventName && is_string($eventName)) {
            $query->where('event_name', $eventName);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_events' => BlockchainEvent::count(),
                'events' => $query->limit($limit)->get(),
            ],
        ]);
    }
}
