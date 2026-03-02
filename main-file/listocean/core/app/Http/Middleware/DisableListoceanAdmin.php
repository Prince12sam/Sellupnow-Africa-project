<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableListoceanAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $isAdminEnabled = filter_var(env('LISTOCEAN_ADMIN_ENABLED', false), FILTER_VALIDATE_BOOLEAN);

        if (! $isAdminEnabled && ($request->is('admin') || $request->is('admin/*'))) {
            abort(404);
        }

        return $next($request);
    }
}
