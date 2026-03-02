<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableCommerceAdminModules
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((bool) env('ENABLE_COMMERCE_MODULES', false)) {
            return $next($request);
        }

        $blockedPaths = [
            'admin/shop',
            'admin/shop/*',
            'admin/rider',
            'admin/rider/*',
            'admin/withdraw',
            'admin/withdraw/*',
            'admin/subscription-plan',
            'admin/subscription-plan/*',
            'admin/business-setting/shop',
            'admin/business-setting/shop/*',
        ];

        foreach ($blockedPaths as $blockedPath) {
            if ($request->is($blockedPath)) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'Commerce modules (shop/rider/vendor) are disabled for this deployment.',
                    ], 404);
                }

                abort(404);
            }
        }

        return $next($request);
    }
}
