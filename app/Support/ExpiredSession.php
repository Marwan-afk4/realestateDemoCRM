<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ExpiredSession
{
    public const MESSAGE = 'Your session expired. Please log in again.';

    public static function forgetRememberCookie(?Request $request = null): void
    {
        $request ??= request();
        $name = Auth::guard('web')->getRecallerName();

        $request->cookies->remove($name);
        Cookie::queue(Cookie::forget($name));
    }

    public static function hadBrowserSession(Request $request): bool
    {
        return $request->hasCookie((string) config('session.cookie'))
            || $request->cookies->has(Auth::guard('web')->getRecallerName());
    }

    public static function redirectToLogin(Request $request): JsonResponse|RedirectResponse
    {
        self::forgetRememberCookie($request);

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->expectsJson() || $request->header('X-Livewire')) {
            return response()->json([
                'message' => self::MESSAGE,
                'redirect' => route('login'),
            ], 419);
        }

        return redirect()->route('login')->with('error', self::MESSAGE);
    }
}
