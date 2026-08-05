<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Research;

use App\Http\Controllers\Controller;
use App\Services\Research\ResearchExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResearchExportController extends Controller
{
    public function export(string $type, Request $request, ResearchExportService $exportService): Response
    {
        $format = (string) $request->query('format', 'json');

        $result = $exportService->exportDataset($type, $format);

        return response($result['content'], 200, [
            'Content-Type' => $result['mime_type'],
            'Content-Disposition' => "attachment; filename=\"{$result['filename']}\"",
        ]);
    }
}
