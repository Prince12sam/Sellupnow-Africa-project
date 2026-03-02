<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // ── OAuth ────────────────────────────────────────────────────────────
        config([
            'services.google.client_id'     => get_static_option('google_client_id'),
            'services.google.client_secret' => get_static_option('google_client_secret'),
            'services.google.redirect'      => get_static_option('google_callback_url'),

            'services.facebook.client_id'     => get_static_option('facebook_client_id'),
            'services.facebook.client_secret' => get_static_option('facebook_client_secret'),
            'services.facebook.redirect'      => get_static_option('facebook_callback_url'),
        ]);

        // ── Mail (admin configures via Email Settings panel) ─────────────────
        $host       = get_static_option('site_smtp_mail_host');
        $port       = get_static_option('site_smtp_mail_port');
        $username   = get_static_option('site_smtp_mail_username');
        $password   = get_static_option('site_smtp_mail_password');
        $encryption = get_static_option('site_smtp_mail_encryption');
        $mailer     = get_static_option('site_smtp_mail_mailer') ?: 'smtp';
        $fromAddress = get_static_option('site_global_email') ?: get_static_option('site_email');

        if (!empty($host) && !empty($username)) {
            config([
                'mail.default'                          => $mailer,
                'mail.mailers.smtp.host'                => $host,
                'mail.mailers.smtp.port'                => (int) ($port ?: 587),
                'mail.mailers.smtp.username'            => $username,
                'mail.mailers.smtp.password'            => $password,
                'mail.mailers.smtp.encryption'          => $encryption ?: 'tls',
                'mail.mailers.smtp.timeout'             => 30,
                'mail.from.address'                     => $fromAddress ?: config('mail.from.address'),
                'mail.from.name'                        => get_static_option('site_name') ?: config('mail.from.name'),
            ]);
        }
    }
}
