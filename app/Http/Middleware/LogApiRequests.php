<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Auth;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, $response)
    {
        try {
            $path = $request->path();
            $method = $request->method();
            
            // Resolve user using Sanctum guard for API requests
            $userId = Auth::guard('sanctum')->id();

            ApiLog::create([
                'path' => $path,
                'method' => $method,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            // Silently fail to ensure API reliability
        }
    }
}
