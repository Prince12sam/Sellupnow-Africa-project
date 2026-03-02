<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockSetupWizards
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        if ($path === '_ignition' || str_starts_with($path, '_ignition/')) {
            abort(404);
        }

        if ($path === 'update' || str_starts_with($path, 'update/')) {
            abort(404);
        }

        if ($path === 'install' || str_starts_with($path, 'install/')) {
            if (! env('INSTALLER_ENABLED', false)) {
                abort(403, 'Installer is disabled. Set INSTALLER_ENABLED=true only during initial server setup.');
            }
        }

        return $next($request);
    }
}