<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
      // For buy membership
        '/buy-membership/paytm-ipn',
        '/buy-membership/cashfree-ipn',
        '/buy-membership/payfast-ipn',
        '/buy-membership/cinetpay-ipn',
        '/buy-membership/zitopay-ipn',
        '/buy-membership/paytabs-ipn',
        '/buy-membership/iyzipay-ipn',

        // for renew membership
        '/renew-membership/paytm-ipn',
        '/renew-membership/cashfree-ipn',
        '/renew-membership/payfast-ipn',
        '/renew-membership/cinetpay-ipn',
        '/renew-membership/zitopay-ipn',
        '/renew-membership/paytabs-ipn',
        '/renew-membership/iyzipay-ipn',


        // For user wallet
        '/wallet/paytm-ipn',
        '/wallet/cashfree-ipn',
        '/wallet/payfast-ipn',
        '/wallet/cinetpay-ipn',
        '/wallet/zitopay-ipn',
        '/wallet/toyyibpay-ipn',
        '/wallet/paystack-ipn',
    ];
}
