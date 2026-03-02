<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Handles Paystack payment initialization and verification.
 * Currency is configured per-merchant in the admin panel (payment_gateways table).
 * Defaults to NGN if not set. Paystack supported currencies: NGN, GHS, ZAR, USD, KES.
 */
class PaystackService
{
    private const BASE_URL = 'https://api.paystack.co';

    /** Fallback currency if none is saved in credentials. */
    public const DEFAULT_CURRENCY = 'NGN';

    /** All Paystack currencies use 100 subunits (kobo, pesewas, cents, etc.). */
    public const CURRENCY_SUBUNIT = 100;

    // ── Credential helpers ───────────────────────────────────────────────────

    public function credentials(): ?array
    {
        $row = DB::table('payment_gateways')->where('name', 'paystack')->first();
        if (!$row) {
            return null;
        }
        $creds = json_decode((string) $row->credentials, true);
        if (!is_array($creds)) {
            return null;
        }
        return array_merge($creds, [
            'status'    => (int) $row->status,
            'test_mode' => (int) $row->test_mode,
        ]);
    }

    public function isEnabled(): bool
    {
        $creds = $this->credentials();
        return $creds && (int) $creds['status'] === 1
            && !empty($creds['secret_key'])
            && !empty($creds['public_key']);
    }

    public function publicKey(): ?string
    {
        return $this->credentials()['public_key'] ?? null;
    }

    /** Returns the ISO currency code configured for this merchant (e.g. NGN, GHS, ZAR, USD, KES). */
    public function currency(): string
    {
        $creds = $this->credentials();
        $c = strtoupper(trim($creds['currency'] ?? ''));
        return $c ?: self::DEFAULT_CURRENCY;
    }

    /**
     * Returns payment channels array for the Paystack initialize call.
     * GHS always includes mobile_money (MTN, Vodafone, AirtelTigo).
     * Falls back to admin-configured channels, or Paystack defaults.
     */
    public function channels(): ?array
    {
        $creds = $this->credentials();
        $saved = $creds['channels'] ?? null;

        // If admin set explicit channels, use those
        if (!empty($saved) && is_array($saved)) {
            return $saved;
        }

        // GHS: enable card + mobile_money by default so customers see MTN/Vodafone/AirtelTigo
        if ($this->currency() === 'GHS') {
            return ['card', 'mobile_money', 'bank'];
        }

        // NGN: all standard channels
        if ($this->currency() === 'NGN') {
            return ['card', 'bank', 'ussd', 'mobile_money', 'bank_transfer'];
        }

        return null; // let Paystack use its own defaults for other currencies
    }

    private function secretKey(): ?string
    {
        return $this->credentials()['secret_key'] ?? null;
    }

    // ── API calls ────────────────────────────────────────────────────────────

    /**
     * Initialise a Paystack transaction.
     *
     * @param  string $email        Payer's email address
     * @param  float  $amountGhs   Amount in Ghana Cedis (GHS)
     * @param  string $reference   Unique transaction reference we generate
     * @param  string $callbackUrl URL Paystack redirects the user to after payment
     * @return string              authorization_url to redirect the user to
     * @throws \RuntimeException   When gateway is disabled or API returns an error
     */
    public function initialize(
        string $email,
        float  $amountGhs,
        string $reference,
        string $callbackUrl
    ): string {
        $secret = $this->secretKey();

        if (!$secret) {
            throw new \RuntimeException('Paystack is not configured. Please contact the administrator.');
        }

        // Paystack requires amounts in the smallest currency unit (100 subunits per major unit).
        $amountSubunit = (int) round($amountGhs * self::CURRENCY_SUBUNIT);

        $payload = [
            'email'        => $email,
            'amount'       => $amountSubunit,
            'currency'     => $this->currency(),
            'reference'    => $reference,
            'callback_url' => $callbackUrl,
            'metadata'     => [
                'cancel_action' => url('/user/wallet/topup'),
            ],
        ];

        // Add payment channels if configured (required for GHS mobile money)
        $channels = $this->channels();
        if ($channels !== null) {
            $payload['channels'] = $channels;
        }

        $response = Http::withToken($secret)
            ->post(self::BASE_URL . '/transaction/initialize', $payload);

        if (!$response->successful() || !$response->json('status')) {
            $msg = $response->json('message') ?? 'Paystack initialization failed';
            Log::error('Paystack init failed', ['body' => $response->body()]);
            throw new \RuntimeException('Payment gateway error: ' . $msg);
        }

        return $response->json('data.authorization_url');
    }

    /**
     * Verify a Paystack transaction by reference.
     *
     * @return object|null  Transaction data (data.status = 'success' means paid)
     */
    public function verify(string $reference): ?object
    {
        $secret = $this->secretKey();

        if (!$secret) {
            return null;
        }

        try {
            $response = Http::withToken($secret)
                ->get(self::BASE_URL . '/transaction/verify/' . urlencode($reference));

            if (!$response->successful()) {
                Log::warning('Paystack verify HTTP error', [
                    'ref'  => $reference,
                    'code' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();
            return $json['status'] ? (object) $json['data'] : null;
        } catch (\Throwable $e) {
            Log::error('Paystack verify exception', ['ref' => $reference, 'err' => $e->getMessage()]);
            return null;
        }
    }
}
