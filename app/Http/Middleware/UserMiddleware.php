<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role == 'user' || Auth::user()->role == 'brocker' || Auth::user()->role == 'trainer') {
            return $next($request);
        } else {
            return response()->json(['error' => 'Unauthorized ,you are not user'], 401);
        }
    }
}
