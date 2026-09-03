<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PanelAccessMiddleware
{
    private const PANEL_PERMISSIONS = [
        'view-contacts',
        'view-pipeline',
        'view-crm-tasks',
        'view-crm-reports',
        'view-deals',
        'view-inventory',
        'view-collections',
        'view-leads',
        'view-message-templates',
        'view-marketing-agencies',
        'view-agency-workspace',
        'view-developer-portal',
        'view-after-sales',
        'view-unit-matching',
        'manage-developer-brokers',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        if (in_array($user->role, ['brocker', 'agency', 'developer'], true)) {
            foreach (self::PANEL_PERMISSIONS as $permission) {
                if ($user->can($permission)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
