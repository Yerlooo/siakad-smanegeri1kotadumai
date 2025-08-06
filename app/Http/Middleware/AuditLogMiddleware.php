<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $startTime = microtime(true);

        $response = $next($request);

        // Log important actions
        if ($this->shouldLog($request)) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::channel('audit')->info('User Action', [
                'user_id' => $user?->id,
                'user_role' => $user?->role?->name,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'timestamp' => now()->toISOString()
            ]);
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        $sensitiveRoutes = [
            'login', 'logout', 'password', 'register',
            'guru', 'siswa', 'nilai', 'absensi',
            'settings', 'approval'
        ];

        foreach ($sensitiveRoutes as $route) {
            if (str_contains($request->path(), $route)) {
                return true;
            }
        }

        return false;
    }
}
