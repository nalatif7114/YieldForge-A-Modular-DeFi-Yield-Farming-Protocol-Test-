<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Monitoring;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\ExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExportMonitoringController extends Controller
{
    public function events(Request $request, ExportService $exportService): Response
    {
        $format = (string) $request->query('format', 'json');
        $limit = (int) $request->query('limit', 500);

        $result = $exportService->exportEvents($format, $limit);

        return response($result['content'], 200, [
            'Content-Type' => $result['mime_type'],
            'Content-Disposition' => "attachment; filename=\"{$result['filename']}\"",
        ]);
    }

    public function metrics(Request $request, ExportService $exportService): Response
    {
        $format = (string) $request->query('format', 'json');
        $limit = (int) $request->query('limit', 500);

        $result = $exportService->exportMetrics($format, $limit);

        return response($result['content'], 200, [
            'Content-Type' => $result['mime_type'],
            'Content-Disposition' => "attachment; filename=\"{$result['filename']}\"",
        ]);
    }
}
