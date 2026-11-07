<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        $checks = [
            'database' => 'healthy',
            'cache' => 'healthy',
        ];

        // Pemeriksaan koneksi PostgreSQL
        try {
            DB::select('SELECT 1');
        } catch (Throwable $e) {
            $checks['database'] = 'unhealthy';

            Log::error('health.database_failed', [
                'actor' => 'health_monitor',
                'action' => 'check_database_connection',
                'reason' => 'database_unreachable',
                'result' => 'unhealthy',
            ]);
        }

        // Pemeriksaan tulis dan baca cache Laravel
        $key = 'health_probe_' . bin2hex(random_bytes(8));

        try {
            $value = bin2hex(random_bytes(8));

            Cache::put($key, $value, 30);

            if (Cache::get($key) !== $value) {
                throw new RuntimeException('Cache check failed');
            }
        } catch (Throwable $e) {
            $checks['cache'] = 'unhealthy';

            Log::error('health.cache_failed', [
                'actor' => 'health_monitor',
                'action' => 'check_cache_read_write',
                'reason' => 'cache_unavailable',
                'result' => 'unhealthy',
            ]);
        } finally {
            try {
                Cache::forget($key);
            } catch (Throwable $e) {
                // Tidak menampilkan data sensitif.
            }
        }

        $healthy = !in_array('unhealthy', $checks, true);

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }
}
