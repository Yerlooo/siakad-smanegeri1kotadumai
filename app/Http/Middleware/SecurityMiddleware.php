<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Rate Limiting untuk mencegah brute force
        $this->handleRateLimit($request);

        // 2. Security Headers
        $response = $next($request);
        
        if ($response instanceof \Illuminate\Http\Response || $response instanceof \Illuminate\Http\JsonResponse) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        return $response;
    }

    private function handleRateLimit(Request $request)
    {
        $key = 'security_' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 60)) { // 60 requests per minute
            abort(429, 'Too Many Requests');
        }
        
        RateLimiter::hit($key, 60);
    }
}