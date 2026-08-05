<?php

declare(strict_types=1);

namespace App\Services\Research;

use App\Models\BlockchainEvent;
use App\Models\ResearchExport;
use App\Models\WalletFeature;
use App\Models\WalletPosition;
use Illuminate\Database\Eloquent\Collection;

class ResearchExportService
{
    /**
     * Export research dataset in CSV or JSON format.
     *
     * @param string $datasetType
     * @param string $format
     * @return array{content: string, mime_type: string, filename: string, row_count: int}
     */
    public function exportDataset(string $datasetType = 'wallet_behavior', string $format = 'json'): array
    {
        $filename = "research_{$datasetType}_" . now()->format('Ymd_His') . ".{$format}";

        if ($datasetType === 'wallet_behavior') {
            /** @var Collection<int, WalletFeature> $data */
            $data = WalletFeature::query()->orderBy('wallet_address')->get();
            if ($data->isEmpty()) {
                /** @var Collection<int, WalletPosition> $data */
                $data = WalletPosition::query()->orderBy('wallet')->get();
            }
        } else {
            /** @var Collection<int, BlockchainEvent> $data */
            $data = BlockchainEvent::query()->orderByDesc('block_number')->limit(500)->get();
        }

        $rowCount = $data->count();

        ResearchExport::create([
            'dataset_name' => $datasetType,
            'format' => $format,
            'row_count' => $rowCount,
            'file_name' => $filename,
        ]);

        if ($format === 'csv') {
            $arrayData = $data->toArray();
            if (empty($arrayData)) {
                $content = "id,name,value\n";
            } else {
                $header = implode(',', array_keys($arrayData[0])) . "\n";
                $rows = [];
                foreach ($arrayData as $row) {
                    $escaped = array_map(fn ($v) => is_array($v) ? '"' . addslashes((string) json_encode($v)) . '"' : '"' . addslashes((string) $v) . '"', array_values($row));
                    $rows[] = implode(',', $escaped);
                }
                $content = $header . implode("\n", $rows);
            }

            return [
                'content' => $content,
                'mime_type' => 'text/csv',
                'filename' => $filename,
                'row_count' => $rowCount,
            ];
        }

        return [
            'content' => json_encode($data->toArray(), JSON_PRETTY_PRINT),
            'mime_type' => 'application/json',
            'filename' => $filename,
            'row_count' => $rowCount,
        ];
    }
}
