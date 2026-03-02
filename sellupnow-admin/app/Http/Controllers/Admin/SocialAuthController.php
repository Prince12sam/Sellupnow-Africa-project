<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialAuthController extends Controller
{
    public function index()
    {
        $this->ensureDefaultProviders();

        $socials = SocialAuth::query()
            ->orderByRaw("CASE provider WHEN 'google' THEN 1 WHEN 'facebook' THEN 2 WHEN 'apple' THEN 3 ELSE 99 END")
            ->orderBy('id')
            ->get();

        return view('admin.social-auth.index', compact('socials'));
    }

    private function ensureDefaultProviders(): void
    {
        $defaults = [
            'google' => [
                'name' => 'Google',
                'client_id' => null,
                'client_secret' => null,
                'redirect' => 'postmessage',
                'logo' => 'assets/social/google.svg',
                'is_active' => false,
            ],
            'facebook' => [
                'name' => 'Facebook',
                'client_id' => null,
                'client_secret' => null,
                'redirect' => null,
                'logo' => 'assets/social/facebook.svg',
                'is_active' => false,
            ],
            'apple' => [
                'name' => 'Apple',
                'client_id' => null,
                'client_secret' => null,
                'redirect' => null,
                'logo' => 'assets/social/apple.svg',
                'is_active' => false,
            ],
        ];

        foreach ($defaults as $provider => $data) {
            SocialAuth::query()->firstOrCreate(
                ['provider' => $provider],
                array_merge($data, ['provider' => $provider])
            );
        }
    }

    public function update(SocialAuth $socialAuth, Request $request)
    {
        $socialAuth->update([
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'redirect' => $request->redirect,
        ]);

        $this->syncListoceanFrontendSocialLoginOptions();

        return back()->with('success', __('Updated Successfully'));
    }

    public function toggle(SocialAuth $socialAuth)
    {
        $socialAuth->update([
            'is_active' => ! $socialAuth->is_active,
        ]);

        $this->syncListoceanFrontendSocialLoginOptions();

        return back()->with('success', __('Status updated successfully'));
    }

    private function syncListoceanFrontendSocialLoginOptions(): void
    {
        // Keep Listocean (customer web) as a pure frontend: it reads social login config
        // from its own static_options, but the source of truth is Sellupnow admin.
        try {
            $customerWebUrl = rtrim((string) config('app.customer_web_url', env('CUSTOMER_WEB_URL', '')), '/');

            $google = SocialAuth::query()->where('provider', 'google')->first();
            $facebook = SocialAuth::query()->where('provider', 'facebook')->first();

            $anyActive = (bool) (($google?->is_active ?? false) || ($facebook?->is_active ?? false));

            $this->upsertListoceanStaticOption('register_page_social_login_show_hide', $anyActive ? 'on' : null);

            // Google
            if ($google) {
                $this->upsertListoceanStaticOption('enable_google_login', $google->is_active ? 'on' : null);
                $this->upsertListoceanStaticOption('google_client_id', $google->client_id);
                $this->upsertListoceanStaticOption('google_client_secret', $google->client_secret);
                $this->upsertListoceanStaticOption(
                    'google_callback_url',
                    $customerWebUrl !== '' ? ($customerWebUrl . '/google/callback') : null
                );
            }

            // Facebook
            if ($facebook) {
                $this->upsertListoceanStaticOption('enable_facebook_login', $facebook->is_active ? 'on' : null);
                $this->upsertListoceanStaticOption('facebook_client_id', $facebook->client_id);
                $this->upsertListoceanStaticOption('facebook_client_secret', $facebook->client_secret);
                $this->upsertListoceanStaticOption(
                    'facebook_callback_url',
                    $customerWebUrl !== '' ? ($customerWebUrl . '/facebook/callback') : null
                );
            }
        } catch (\Throwable $e) {
            // Best-effort sync only. Avoid breaking admin flows if the Listocean DB isn't available.
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
