<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Attach OWASP-recommended HTTP security headers to every response.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Keep CSP strict in production, but allow local media URLs during local/dev.
        $isLocalEnv = app()->environment(['local', 'development'])
            || in_array($request->getHost(), ['127.0.0.1', 'localhost'], true);
        $localImageSrc = $isLocalEnv
            ? ' http://127.0.0.1:8090 http://localhost:8090 http://127.0.0.1:8000 http://localhost:8000'
            : '';

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(self), payment=()'
        );

        // Content-Security-Policy: restrict resource loading to known-good origins.
        // 'unsafe-inline' on script-src is required because the admin panel uses
        // inline <script> blocks extensively (blade @push('scripts'), onclick handlers).
        // 'unsafe-eval' is needed by some bundled editor components (Quill, Select2).
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'"
                . ' https://www.google.com https://www.gstatic.com'
                . ' https://js.pusher.com https://cdn.jsdelivr.net'
                . ' https://cdnjs.cloudflare.com https://code.jquery.com'
                . ' https://maxcdn.bootstrapcdn.com https://cdn.quilljs.com'
                . ' https://challenges.cloudflare.com',
            "style-src 'self' 'unsafe-inline'"
                . ' https://fonts.googleapis.com https://cdn.jsdelivr.net'
                . ' https://cdnjs.cloudflare.com https://maxcdn.bootstrapcdn.com'
                . ' https://lineicons.com https://cdn.quilljs.com',
            "font-src 'self' data:"
                . ' https://fonts.gstatic.com https://cdn.jsdelivr.net'
                . ' https://cdnjs.cloudflare.com https://maxcdn.bootstrapcdn.com'
                . ' https://lineicons.com',
            "img-src 'self' data: blob:"
                . ' https://placehold.co https://www.google.com https://www.gstatic.com'
                . $localImageSrc,
            "media-src 'self' data: blob:",
            "connect-src 'self' https://js.pusher.com wss://*.pusher.com https://openrouter.ai https://challenges.cloudflare.com https://cdn.jsdelivr.net",
            "frame-src 'self' https://www.google.com https://challenges.cloudflare.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
