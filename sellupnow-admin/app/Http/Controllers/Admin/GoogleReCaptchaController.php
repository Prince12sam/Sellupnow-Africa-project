<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleReCaptchaRequest;
use App\Models\GoogleReCaptcha;
use App\Repositories\GoogleReCaptchaRepository;
use Illuminate\Support\Facades\DB;

class GoogleReCaptchaController extends Controller
{
    public function index()
    {
        $reCaptcha = GoogleReCaptcha::first();

        // Always keep frontend in sync with admin settings on page load.
        if ($reCaptcha) {
            $this->syncListoceanCaptchaOptions($reCaptcha);
        }

        // Read back the live frontend value for the status badge.
        $frontendCaptchaEnabled = (bool) DB::connection('listocean')
            ->table('static_options')
            ->where('option_name', 'site_google_captcha_enable')
            ->value('option_value');

        return view('admin.google-recaptcha', compact('reCaptcha', 'frontendCaptchaEnabled'));
    }

    public function resync()
    {
        $reCaptcha = GoogleReCaptcha::first();
        if ($reCaptcha) {
            $this->syncListoceanCaptchaOptions($reCaptcha);
        }
        return back()->withSuccess(__('Captcha settings re-synced to frontend.'));
    }

    public function update(GoogleReCaptchaRequest $request)
    {
        $reCaptcha = GoogleReCaptcha::first();

        $updated = GoogleReCaptchaRepository::updateByRequest($request, $reCaptcha);

        $this->syncListoceanCaptchaOptions($updated);

        return back()->withSuccess(__('ReCaptcha updated successfully'));
    }

    private function syncListoceanCaptchaOptions(GoogleReCaptcha $captcha): void
    {
        // Listocean frontend reads captcha settings from its static_options table.
        // Sellupnow admin is the source of truth.
        try {
            $enabled = (bool) $captcha->is_active;
            $provider = in_array($captcha->provider, ['google', 'cloudflare'], true) ? $captcha->provider : 'google';

            $this->upsertListoceanStaticOption('site_google_captcha_enable', $enabled ? 'on' : null);
            $this->upsertListoceanStaticOption('captcha_provider', $enabled ? $provider : null);

            if ($provider === 'cloudflare') {
                $this->upsertListoceanStaticOption('cloudflare_turnstile_site_key', $captcha->turnstile_site_key);
                $this->upsertListoceanStaticOption('cloudflare_turnstile_secret_key', $captcha->turnstile_secret_key);

                // Clear google v2 keys to avoid confusion.
                $this->upsertListoceanStaticOption('recaptcha_2_site_key', null);
                $this->upsertListoceanStaticOption('recaptcha_2_secret_key', null);
            } else {
                $this->upsertListoceanStaticOption('recaptcha_2_site_key', $captcha->site_key);
                $this->upsertListoceanStaticOption('recaptcha_2_secret_key', $captcha->secret_key);

                // Clear turnstile keys to avoid confusion.
                $this->upsertListoceanStaticOption('cloudflare_turnstile_site_key', null);
                $this->upsertListoceanStaticOption('cloudflare_turnstile_secret_key', null);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function upsertListoceanStaticOption(string $optionName, ?string $optionValue): void
    {
        $conn = DB::connection('listocean');
        $now = now();

        $exists = $conn->table('static_options')
            ->where('option_name', $optionName)
            ->exists();

        if ($exists) {
            $conn->table('static_options')
                ->where('option_name', $optionName)
                ->update([
                    'option_value' => $optionValue,
                    'updated_at' => $now,
                ]);
            return;
        }

        $conn->table('static_options')->insert([
            'option_name' => $optionName,
            'option_value' => $optionValue,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
