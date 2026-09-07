<?php

namespace App\Http\Controllers;

use App\Support\ExpiredSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionHeartbeatController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $guard = Auth::guard('web');
        $authenticated = ($request->hasSession() && $request->session()->has($guard->getName()))
            || ($guard->hasUser() && ! $guard->viaRemember());

        if (! $authenticated) {
            ExpiredSession::forgetRememberCookie($request);
        }

        return response()->json([
            'csrf' => csrf_token(),
            'authenticated' => $authenticated,
        ]);
    }
}
