<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale', 'en');

        if ($request->has('lang')) {
            $locale = $request->input('lang');
            if (in_array($locale, ['en', 'ar'])) {
                if ($request->hasSession()) {
                    session(['locale' => $locale]);
                }
            }
        } elseif ($request->hasSession() && session()->has('locale')) {
            $locale = session('locale');
        } elseif ($request->hasHeader('Accept-Language')) {
            $headerLocale = substr($request->header('Accept-Language'), 0, 2);
            if (in_array($headerLocale, ['en', 'ar'])) {
                $locale = $headerLocale;
            }
        }

        if (in_array($locale, ['en', 'ar'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
