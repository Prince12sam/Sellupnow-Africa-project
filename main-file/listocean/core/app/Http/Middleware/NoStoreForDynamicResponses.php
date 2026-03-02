<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoStoreForDynamicResponses
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $enabled = filter_var(env('NO_STORE_DYNAMIC_RESPONSES', env('APP_DEBUG', false)), FILTER_VALIDATE_BOOL);
        if (! $enabled) {
            return $response;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));
        $isHtml = str_contains($contentType, 'text/html');
        $isJson = str_contains($contentType, 'application/json');

        if (! $isHtml && ! $isJson) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
