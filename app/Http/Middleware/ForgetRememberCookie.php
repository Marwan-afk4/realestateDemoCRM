<?php

namespace App\Http\Middleware;

use App\Support\ExpiredSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForgetRememberCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        ExpiredSession::forgetRememberCookie($request);

        return $next($request);
    }
}
