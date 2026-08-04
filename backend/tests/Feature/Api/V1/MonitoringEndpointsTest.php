<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\MonitoringAlert;
use App\Models\MonitoringSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_dashboard_endpoint(): void
    {
        $response = $this->getJson('/api/v1/monitoring/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'health_score',
                    'overall_status',
                    'indexer',
                    'queue',
                    'cache',
                    'rpc',
                    'alerts_summary',
                    'protocol_kpis',
                    'timestamp',
                ],
            ]);
    }

    public function test_monitoring_health_endpoint(): void
    {
        $response = $this->getJson('/api/v1/monitoring/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'health_score',
                    'overall_status',
                    'components' => ['indexer', 'rpc', 'queue', 'cache'],
                    'active_alerts_count',
                    'timestamp',
                ],
            ]);
    }

    public function test_monitoring_queues_endpoint(): void
    {
        $response = $this->getJson('/api/v1/monitoring/queues');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['connection', 'pending_jobs', 'failed_jobs', 'throughput_per_minute', 'status'],
            ]);
    }

    public function test_monitoring_cache_endpoint(): void
    {
        $response = $this->getJson('/api/v1/monitoring/cache');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['driver', 'hits', 'misses', 'hit_ratio_percentage', 'status'],
            ]);
    }

    public function test_monitoring_rpc_endpoint(): void
    {
        $response = $this->getJson('/api/v1/monitoring/rpc');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['endpoint', 'chain_id', 'network_name', 'latency_ms', 'is_connected', 'status'],
            ]);
    }

    public function test_monitoring_alerts_endpoints(): void
    {
        /** @var MonitoringAlert $alert */
        $alert = MonitoringAlert::create([
            'rule_name' => 'IndexerLagHigh',
            'severity' => 'critical',
            'component' => 'indexer',
            'message' => 'Test alert',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/monitoring/alerts');
        $response->assertStatus(200);

        $ackResponse = $this->postJson("/api/v1/monitoring/alerts/{$alert->id}/acknowledge");
        $ackResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'acknowledged');

        $rulesResponse = $this->getJson('/api/v1/monitoring/alerts/rules');
        $rulesResponse->assertStatus(200);
    }

    public function test_monitoring_history_endpoint(): void
    {
        MonitoringSnapshot::create([
            'health_score' => 95,
            'indexer_lag' => 0,
            'rpc_latency_ms' => 120.0,
            'queue_pending_jobs' => 0,
            'queue_failed_jobs' => 0,
            'cache_hit_ratio' => 99.0,
            'active_alerts_count' => 0,
            'metrics' => [],
            'timestamp' => now(),
        ]);

        $response = $this->getJson('/api/v1/monitoring/history');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_monitoring_export_events_endpoint(): void
    {
        $jsonResponse = $this->get('/api/v1/monitoring/export/events?format=json');
        $jsonResponse->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');

        $csvResponse = $this->get('/api/v1/monitoring/export/events?format=csv');
        $csvResponse->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_monitoring_export_metrics_endpoint(): void
    {
        $jsonResponse = $this->get('/api/v1/monitoring/export/metrics?format=json');
        $jsonResponse->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');

        $csvResponse = $this->get('/api/v1/monitoring/export/metrics?format=csv');
        $csvResponse->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_monitoring_performance_endpoint(): void
    {
        $response = $this->getJson('/api/v1/monitoring/performance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'api_avg_response_ms',
                    'db_query_avg_execution_ms',
                    'memory_usage_mb',
                ],
            ]);
    }
}
