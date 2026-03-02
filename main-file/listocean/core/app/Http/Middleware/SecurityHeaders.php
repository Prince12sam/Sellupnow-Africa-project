<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Attach OWASP-recommended HTTP security headers to every response.
 *
 * Headers applied:
 *   X-Frame-Options            – prevents clickjacking
 *   X-Content-Type-Options     – prevents MIME sniffing
 *   Referrer-Policy            – limits referrer leakage
 *   Permissions-Policy         – restricts browser feature access
 *   Strict-Transport-Security  – enforces HTTPS (only sent over HTTPS)
 *   X-XSS-Protection           – legacy IE guard (belt-and-suspenders)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(self), payment=()'
        );

        // Only send HSTS when the current request is over HTTPS to avoid
        // breaking local HTTP development.
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
