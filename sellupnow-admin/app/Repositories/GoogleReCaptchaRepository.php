<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Models\GoogleReCaptcha;

class GoogleReCaptchaRepository extends Repository
{
    /**
     * Get the model class of the repository.
     *
     * @return string
     */
    public static function model()
    {
        return GoogleReCaptcha::class;
    }

    /**
     * Update the google reCaptcha by request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GoogleReCaptcha|null  $reCaptcha
     */
    public static function updateByRequest($request, $reCaptcha): GoogleReCaptcha
    {
        $provider = $request->input('provider', $reCaptcha?->provider ?? 'google');

        // The underlying table has NOT NULL constraints for google site/secret keys.
        // If the admin is configuring Cloudflare Turnstile, keep the existing google keys
        // instead of overwriting them with null/empty values.
        $googleSiteKey = $request->filled('site_key')
            ? $request->site_key
            : ($reCaptcha?->site_key ?? '');

        $googleSecretKey = $request->filled('secret_key')
            ? $request->secret_key
            : ($reCaptcha?->secret_key ?? '');

        $turnstileSiteKey = $request->filled('turnstile_site_key')
            ? $request->turnstile_site_key
            : ($reCaptcha?->turnstile_site_key ?? null);

        $turnstileSecretKey = $request->filled('turnstile_secret_key')
            ? $request->turnstile_secret_key
            : ($reCaptcha?->turnstile_secret_key ?? null);

        return self::query()->updateOrCreate(
            ['id' => $reCaptcha?->id ?? null],
            [
                'site_key' => $googleSiteKey,
                'secret_key' => $googleSecretKey,
                'provider' => $provider,
                'turnstile_site_key' => $turnstileSiteKey,
                'turnstile_secret_key' => $turnstileSecretKey,
                'is_active' => $request->is_active ? true : false,
            ]
        );
    }
}
