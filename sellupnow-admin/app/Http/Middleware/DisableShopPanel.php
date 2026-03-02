<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableShopPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('shop') || $request->is('shop/*')) {
            abort(404);
        }

        return $next($request);
    }
}
