<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rate-limit public tracking lookups to slow down tracking-number
 * enumeration. Limits are per-IP.
 */
class ThrottleTrackingLookups
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'tracking-lookup:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 30)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->view('public.tracking-error', [
                'message' => "Too many tracking attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
