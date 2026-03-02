<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LockLegacyMembershipIpn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isLegacyMembershipIpnRequest($request)) {
            return $next($request);
        }

        if ((bool) env('LEGACY_MEMBERSHIP_IPN_ENABLED', false) === true) {
            return $next($request);
        }

        return response('Legacy membership IPN endpoint is disabled.', 410);
    }

    private function isLegacyMembershipIpnRequest(Request $request): bool
    {
        return $request->is('buy-membership/*') || $request->is('renew-membership/*');
    }
}
