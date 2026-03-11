<?php

namespace App\Rules;

use App\Models\GoogleReCaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class CaptchaValidate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $reCaptcha = GoogleReCaptcha::first();

        if ($reCaptcha?->provider === 'cloudflare') {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $reCaptcha->turnstile_secret_key,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
        } else {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $reCaptcha?->secret_key,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
        }

        $captchaResponse = $response->json();

        if (! $captchaResponse['success']) {
            $fail('Captcha verification failed, please try again.');
        }
    }
}
