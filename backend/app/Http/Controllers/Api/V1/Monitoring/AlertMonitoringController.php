<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\AlertEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertMonitoringController extends Controller
{
    public function index(Request $request, AlertEngineService $alertEngine): JsonResponse
    {
        $status = $request->query('status');
        $severity = $request->query('severity');

        $statusStr = is_string($status) ? $status : null;
        $severityStr = is_string($severity) ? $severity : null;

        $alerts = $alertEngine->getAlerts($statusStr, $severityStr);

        return response()->json([
            'status' => 'success',
            'data' => array_map(fn ($dto) => $dto->toArray(), $alerts),
        ]);
    }

    public function acknowledge(int $id, AlertEngineService $alertEngine): JsonResponse
    {
        $alert = $alertEngine->acknowledgeAlert($id);

        if (!$alert) {
            return response()->json([
                'status' => 'error',
                'message' => "Alert ID #{$id} not found.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $alert->toArray(),
        ]);
    }

    public function rules(AlertEngineService $alertEngine): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $alertEngine->getRules(),
        ]);
    }
}
