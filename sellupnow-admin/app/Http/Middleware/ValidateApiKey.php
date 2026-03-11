<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateApiKey
{
    /**
     * Validate API key for mobile/client API traffic.
     *
    * Behaviour controlled by API_KEY_ENFORCE env variable:
     *   "strict"  — reject requests without a valid key (401)
     *   "log"     — log failures but allow the request through (default)
     */
    public function handle(Request $request, Closure $next)
    {
        $configuredKeys = (array) config('app.api_secret_keys', []);
        $normalizedKeys = array_values(array_filter(array_map(
            static fn ($key) => trim((string) $key),
            $configuredKeys
        )));

        // If no keys configured at all, skip validation entirely
        if (empty($normalizedKeys)) {
            return $next($request);
        }

        $provided = (string) $request->header('key', '');

        $isValid = false;
        foreach ($normalizedKeys as $expected) {
            if ($provided !== '' && hash_equals($expected, $provided)) {
                $isValid = true;
                break;
            }
        }

        if (! $isValid) {
            Log::warning('API key validation failed', [
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'has_key_header' => $provided !== '',
                'provided_key_length' => strlen($provided),
            ]);

            $enforce = config('app.api_key_enforce', 'log');

            if ($enforce === 'strict') {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized API request.',
                ], 401);
            }
            // In "log" mode, let the request through
        }

        return $next($request);
    }
}
