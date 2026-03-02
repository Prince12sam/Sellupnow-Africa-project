<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Blocks the install and update wizard routes in production.
 *
 * INSTALL WIZARD (/install/*):
 *   - Needed once on a fresh VPS to run the web-based setup (configures .env,
 *     runs migrations and seeders).
 *   - Set INSTALLER_ENABLED=true in .env ONLY during initial VPS deployment.
 *   - After installation is complete, set INSTALLER_ENABLED=false (or remove it).
 *   - Default: BLOCKED (returns 403).
 *
 * UPDATE WIZARD (/update/*):
 *   - Accepts file uploads and overwrites application files on disk.
 *   - This is a Remote Code Execution (RCE) vector.
 *   - Always blocked with 404, regardless of any env flag.
 *   - There is no legitimate reason to re-enable this route.
 */
class BlockSetupWizards
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Update wizard: always blocked — file-upload RCE risk, never needed in
        // production. Returns 404 so it reveals nothing about the route's existence.
        if ($path === 'update' || str_starts_with($path, 'update/')) {
            abort(404);
        }

        // Install wizard: blocked unless INSTALLER_ENABLED=true in .env.
        // Enable only on initial VPS deployment, then disable immediately after.
        if ($path === 'install' || str_starts_with($path, 'install/')) {
            if (! env('INSTALLER_ENABLED', false)) {
                abort(403, 'Installer is disabled. Set INSTALLER_ENABLED=true in .env only during initial server setup.');
            }
        }

        return $next($request);
    }
}
