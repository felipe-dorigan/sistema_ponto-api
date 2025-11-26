<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ApiResponse;

class HealthController extends Controller
{
    /**
     * Endpoint de verificação de saúde da aplicação
     * 
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $overallHealth = !in_array(false, $checks, true);
        $status = $overallHealth ? 'healthy' : 'unhealthy';
        $httpCode = $overallHealth ? 200 : 503;

        return ApiResponse::success([
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'checks' => $checks,
            'uptime' => $this->getUptime(),
        ], 'Health check completed', $httpCode);
    }

    /**
     * Verificar conexão com banco de dados
     * 
     * @return bool
     */
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            DB::connection()->select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            \Log::error('Database health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar sistema de cache
     * 
     * @return bool
     */
    private function checkCache(): bool
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'ok';

            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);

            return $retrieved === $testValue;
        } catch (\Exception $e) {
            \Log::error('Cache health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar sistema de arquivos
     * 
     * @return bool
     */
    private function checkStorage(): bool
    {
        try {
            $testFile = storage_path('app/health_check_' . time() . '.tmp');
            $testContent = 'health check test';

            file_put_contents($testFile, $testContent);
            $retrieved = file_get_contents($testFile);
            unlink($testFile);

            return $retrieved === $testContent;
        } catch (\Exception $e) {
            \Log::error('Storage health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obter tempo de atividade aproximado
     * 
     * @return array
     */
    private function getUptime(): array
    {
        $uptimeFile = storage_path('app/uptime.txt');

        if (!file_exists($uptimeFile)) {
            file_put_contents($uptimeFile, time());
        }

        $startTime = (int) file_get_contents($uptimeFile);
        $uptime = time() - $startTime;

        return [
            'seconds' => $uptime,
            'human' => $this->formatUptime($uptime),
        ];
    }

    /**
     * Formatar tempo de atividade de forma legível
     * 
     * @param int $seconds
     * @return string
     */
    private function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($days > 0)
            $parts[] = "{$days}d";
        if ($hours > 0)
            $parts[] = "{$hours}h";
        if ($minutes > 0)
            $parts[] = "{$minutes}m";
        if ($secs > 0 || empty($parts))
            $parts[] = "{$secs}s";

        return implode(' ', $parts);
    }

    /**
     * Endpoint simples para load balancers
     * 
     * @return JsonResponse
     */
    public function ping(): JsonResponse
    {
        return response()->json(['status' => 'ok'], 200);
    }
}