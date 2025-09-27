<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Analytics;
use Symfony\Component\HttpFoundation\Response;

class AnalyticsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track session for authenticated users
        if (auth()->check()) {
            Analytics::track(
                'session',
                'backend',
                $request->path(),
                [
                    'user_id' => auth()->id(),
                    'method' => $request->method(),
                    'timestamp' => now()->toISOString()
                ]
            );
        }

        return $response;
    }
}
