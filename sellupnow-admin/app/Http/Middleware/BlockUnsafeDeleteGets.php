<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockUnsafeDeleteGets
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethod('GET') || ! $request->is('admin/*')) {
            return $next($request);
        }

        $path = strtolower($request->path());
        if (preg_match('#/(delete|destroy|remove)(/|$)#', $path)) {
            abort(405, 'Unsafe GET delete endpoints are blocked. Use POST/DELETE with CSRF protection.');
        }

        return $next($request);
    }
}
