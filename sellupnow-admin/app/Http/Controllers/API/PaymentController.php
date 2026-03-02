<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * PaymentController
 * Handles Paystack and PayPal payments for subscription/feature-ad packages.
 *
 * ⚠  Set these in your .env:
 *     PAYSTACK_SECRET_KEY=sk_live_...
 *     PAYPAL_CLIENT_ID=...
 *     PAYPAL_CLIENT_SECRET=...
 *     PAYPAL_BASE_URL=https://api-m.paypal.com   (or sandbox)
 */
class PaymentController extends Controller
{
    // ─── Paystack ────────────────────────────────────────────────────────────

    public function paystackInit(Request $request)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|min:1',
            'email'     => 'required|email',
            'plan_id'   => 'nullable|integer',
            'reference' => 'nullable|string',
        ]);

        $secret = config('services.paystack.secret_key', env('PAYSTACK_SECRET_KEY'));
        if (! $secret) {
            return $this->json('Paystack not configured', [], 503);
        }

        $response = Http::withToken($secret)
            ->post('https://api.paystack.co/transaction/initialize', [
                'email'     => $data['email'],
                'amount'    => (int) ($data['amount'] * 100), // kobo
                'reference' => $data['reference'] ?? null,
                'metadata'  => ['plan_id' => $data['plan_id'] ?? null],
            ]);

        if (! $response->successful()) {
            return $this->json('Paystack initialization failed', [], 422);
        }

        $body = $response->json();

        return $this->json('payment initialized', [
            'access_code'      => $body['data']['access_code'] ?? null,
            'authorization_url' => $body['data']['authorization_url'] ?? null,
            'reference'        => $body['data']['reference'] ?? null,
        ]);
    }

    public function paystackVerify(Request $request)
    {
        $data = $request->validate([
            'reference' => 'required|string',
        ]);

        $secret = config('services.paystack.secret_key', env('PAYSTACK_SECRET_KEY'));
        if (! $secret) {
            return $this->json('Paystack not configured', [], 503);
        }

        $response = Http::withToken($secret)
            ->get("https://api.paystack.co/transaction/verify/{$data['reference']}");

        if (! $response->successful()) {
            return $this->json('Paystack verification failed', [], 422);
        }

        $body   = $response->json();
        $status = $body['data']['status'] ?? 'failed';

        return $this->json('payment verification', [
            'status'    => $status,
            'paid'      => $status === 'success',
            'reference' => $data['reference'],
            'amount'    => ($body['data']['amount'] ?? 0) / 100,
            'currency'  => $body['data']['currency'] ?? null,
        ]);
    }

    // ─── PayPal ──────────────────────────────────────────────────────────────

    private function paypalAccessToken(): ?string
    {
        $clientId     = env('PAYPAL_CLIENT_ID');
        $clientSecret = env('PAYPAL_CLIENT_SECRET');
        $base         = env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com');

        if (! $clientId || ! $clientSecret) {
            return null;
        }

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post("{$base}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        return $response->json('access_token');
    }

    public function paypalCreateOrder(Request $request)
    {
        $data = $request->validate([
            'amount'   => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'plan_id'  => 'nullable|integer',
        ]);

        $token = $this->paypalAccessToken();
        if (! $token) {
            return $this->json('PayPal not configured', [], 503);
        }

        $base     = env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com');
        $response = Http::withToken($token)
            ->post("{$base}/v2/checkout/orders", [
                'intent'        => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($data['currency'] ?? 'USD'),
                        'value'         => number_format((float) $data['amount'], 2, '.', ''),
                    ],
                ]],
            ]);

        if (! $response->successful()) {
            return $this->json('PayPal order creation failed', [], 422);
        }

        $body = $response->json();

        return $this->json('order created', [
            'order_id' => $body['id'] ?? null,
            'status'   => $body['status'] ?? null,
            'links'    => $body['links'] ?? [],
        ]);
    }

    public function paypalCaptureOrder(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|string',
        ]);

        $token = $this->paypalAccessToken();
        if (! $token) {
            return $this->json('PayPal not configured', [], 503);
        }

        $base     = env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com');
        $response = Http::withToken($token)
            ->post("{$base}/v2/checkout/orders/{$data['order_id']}/capture");

        if (! $response->successful()) {
            return $this->json('PayPal capture failed', [], 422);
        }

        $body   = $response->json();
        $status = $body['status'] ?? 'failed';

        return $this->json('payment captured', [
            'order_id' => $body['id'] ?? $data['order_id'],
            'status'   => $status,
            'paid'     => $status === 'COMPLETED',
        ]);
    }
}
