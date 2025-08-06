<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiter
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->is('login')) {
            $key = 'login_attempts_' . $request->ip();
            
            // Max 5 login attempts per 15 minutes
            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);
                
                return response()->json([
                    'message' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'
                ], 429);
            }
            
            // Hit the rate limiter
            RateLimiter::hit($key, 900); // 15 minutes
        }

        return $next($request);
    }
}