<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Research;

use App\Http\Controllers\Controller;
use App\Models\FeatureSet;
use App\Models\ResearchDataset;
use App\Models\ResearchExport;
use App\Services\Research\DataQualityEngine;
use Illuminate\Http\JsonResponse;

class ResearchDashboardController extends Controller
{
    public function index(DataQualityEngine $qualityEngine): JsonResponse
    {
        $datasets = ResearchDataset::all();
        $featureSets = FeatureSet::all();
        $latestExports = ResearchExport::orderByDesc('created_at')->limit(5)->get();
        $quality = $qualityEngine->validateDataset();

        return response()->json([
            'status' => 'success',
            'data' => [
                'datasets_count' => $datasets->count(),
                'feature_sets_count' => $featureSets->count(),
                'overall_quality_score' => $quality['quality_score'],
                'dataset_health_status' => $quality['status'],
                'datasets' => $datasets,
                'feature_sets' => $featureSets,
                'recent_exports' => $latestExports,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }
}
