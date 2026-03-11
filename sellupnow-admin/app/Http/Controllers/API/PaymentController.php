<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /* ─── helpers ──────────────────────────────────────────── */

    private function paystackSecret(): ?string
    {
        $gw = PaymentGateway::where('name', 'paystack')
                ->where('is_active', true)
                ->first();

        $config = $gw ? json_decode($gw->config, true) : [];

        return $config['secret_key'] ?? null;
    }

    private function activateSubscription(int $userId, SubscriptionPlan $plan, string $gateway, ?string $transactionId = null): int
    {
        // Expire any existing active membership
        DB::table('user_memberships')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->update(['status' => 0]);

        $expiresAt = $plan->duration_days > 0
            ? now()->addDays($plan->duration_days)
            : null;

        $featuredCount = (int) ($plan->auto_feature_count ?? 0);
        $features      = is_array($plan->features) ? $plan->features : [];

        return DB::table('user_memberships')->insertGetId([
            'user_id'                  => $userId,
            'membership_id'            => $plan->id,
            'price'                    => $plan->price,
            'payment_gateway'          => $gateway,
            'payment_status'           => 'completed',
            'transaction_id'           => $transactionId,
            'expire_date'              => $expiresAt,
            'status'                   => 1,
            'listing_limit'            => (int) ($plan->listing_quota ?? 0),
            'gallery_images'           => 0,
            'initial_gallery_images'   => 0,
            'featured_listing'         => $featuredCount,
            'initial_featured_listing' => $featuredCount,
            'enquiry_form'             => (int) data_get($features, 'enquiry_form', 0),
            'business_hour'            => (int) data_get($features, 'business_hour', 0),
            'membership_badge'         => (int) data_get($features, 'membership_badge', 0),
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);
    }

    /* ─── Paystack Init ───────────────────────────────────── */

    public function paystackInit(Request $request)
    {
        $data = $request->validate([
            'packageId'   => 'required',
            'packageType' => 'required|string',
        ]);

        $user = $request->user();
        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $plan = SubscriptionPlan::where('id', $data['packageId'])
                    ->active()
                    ->first();

        if (! $plan) {
            return response()->json(['status' => false, 'message' => 'Plan not found or inactive.']);
        }

        // ── Free plan: activate immediately ──────────────────────
        if ((float) $plan->price <= 0) {
            $this->activateSubscription($user->id, $plan, 'free');
            return response()->json([
                'status'  => true,
                'message' => 'Free plan activated successfully.',
                'data'    => ['free' => true],
            ]);
        }

        // ── Paid plan: initialize Paystack ───────────────────────
        $secret = $this->paystackSecret();
        if (! $secret) {
            return response()->json(['status' => false, 'message' => 'Paystack is not configured.']);
        }

        $reference   = 'SUB_' . Str::upper(Str::random(20));
        $callbackUrl = rtrim(config('app.url'), '/') . '/paystack/callback';

        $response = Http::withToken($secret)
            ->post('https://api.paystack.co/transaction/initialize', [
                'email'        => $user->email,
                'amount'       => (int) ($plan->price * 100),
                'reference'    => $reference,
                'callback_url' => $callbackUrl,
                'metadata'     => [
                    'user_id'      => $user->id,
                    'plan_id'      => $plan->id,
                    'package_type' => $data['packageType'],
                ],
            ]);

        if (! $response->successful()) {
            return response()->json(['status' => false, 'message' => 'Failed to initialize Paystack payment.']);
        }

        $body = $response->json();

        if (! ($body['status'] ?? false)) {
            return response()->json(['status' => false, 'message' => $body['message'] ?? 'Paystack returned an error.']);
        }

        // Store pending subscription
        DB::table('membership_subscriptions')->insert([
            'user_id'    => $user->id,
            'plan_id'    => $plan->id,
            'status'     => 'pending',
            'metadata'   => json_encode([
                'reference'    => $reference,
                'gateway'      => 'paystack',
                'package_type' => $data['packageType'],
                'expected_amount' => (int) round($plan->price * 100),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment initialized.',
            'data'    => [
                'authorizationUrl' => $body['data']['authorization_url'],
                'callbackUrl'      => $callbackUrl,
                'reference'        => $reference,
            ],
        ]);
    }

    /* ─── Paystack Verify ─────────────────────────────────── */

    public function paystackVerify(Request $request)
    {
        $data = $request->validate([
            'reference' => 'required|string|max:191',
        ]);

        $user = $request->user();
        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $secret = $this->paystackSecret();
        if (! $secret) {
            return response()->json(['status' => false, 'message' => 'Paystack is not configured.']);
        }

        $alreadyActivated = DB::table('user_memberships')
            ->where('user_id', $user->id)
            ->where('transaction_id', $data['reference'])
            ->exists();

        if ($alreadyActivated) {
            return response()->json([
                'status' => true,
                'message' => 'Subscription already activated for this transaction.',
            ]);
        }

        $pending = DB::table('membership_subscriptions')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get()
            ->first(function ($row) use ($data) {
                $metadata = json_decode($row->metadata ?? '{}', true);

                return ($metadata['reference'] ?? null) === $data['reference'];
            });

        if (! $pending) {
            return response()->json(['status' => false, 'message' => 'No pending subscription found for this reference.']);
        }

        $plan = SubscriptionPlan::find($pending->plan_id);
        if (! $plan) {
            return response()->json(['status' => false, 'message' => 'Subscription plan no longer exists.']);
        }

        $refSafe = preg_replace('/[^A-Za-z0-9_\-]/', '', $data['reference']);
        $response = Http::withToken($secret)
            ->get("https://api.paystack.co/transaction/verify/{$refSafe}");

        if (! $response->successful()) {
            return response()->json(['status' => false, 'message' => 'Paystack verification failed.']);
        }

        $body   = $response->json();
        $txn    = $body['data'] ?? [];
        $status = $txn['status'] ?? 'failed';

        if ($status !== 'success') {
            return response()->json(['status' => false, 'message' => 'Payment was not successful.']);
        }

        $expectedAmount = (int) round($plan->price * 100);
        $verifiedAmount = (int) ($txn['amount'] ?? 0);
        $verifiedReference = (string) ($txn['reference'] ?? '');
        $verifiedUserId = (int) data_get($txn, 'metadata.user_id', 0);
        $verifiedPlanId = (int) data_get($txn, 'metadata.plan_id', 0);
        $verifiedEmail = (string) data_get($txn, 'customer.email', '');

        if ($verifiedReference !== $data['reference']) {
            return response()->json(['status' => false, 'message' => 'Reference mismatch.']);
        }

        if ($verifiedAmount !== $expectedAmount) {
            return response()->json(['status' => false, 'message' => 'Verified payment amount does not match the selected plan.']);
        }

        if ($verifiedUserId !== 0 && $verifiedUserId !== (int) $user->id) {
            return response()->json(['status' => false, 'message' => 'Payment verification user mismatch.']);
        }

        if ($verifiedPlanId !== 0 && $verifiedPlanId !== (int) $plan->id) {
            return response()->json(['status' => false, 'message' => 'Payment verification plan mismatch.']);
        }

        if ($verifiedEmail !== '' && strcasecmp($verifiedEmail, (string) $user->email) !== 0) {
            return response()->json(['status' => false, 'message' => 'Payment verification email mismatch.']);
        }

        // Activate the subscription
        DB::transaction(function () use ($user, $plan, $pending, $data) {
            $lockedPending = DB::table('membership_subscriptions')
                ->where('id', $pending->id)
                ->lockForUpdate()
                ->first();

            if (! $lockedPending || $lockedPending->status !== 'pending') {
                return;
            }

            $this->activateSubscription($user->id, $plan, 'paystack', $data['reference']);

            DB::table('membership_subscriptions')
                ->where('id', $lockedPending->id)
                ->update([
                    'status'     => 'active',
                    'starts_at'  => now(),
                    'ends_at'    => $plan->duration_days > 0
                                       ? now()->addDays($plan->duration_days)
                                       : null,
                    'updated_at' => now(),
                ]);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Subscription activated successfully!',
        ]);
    }

    /* ─── Stripe ──────────────────────────────────────────── */

    public function stripeCreatePaymentIntent(Request $request)
    {
        $gateway = PaymentGateway::where('name', 'stripe')->where('is_active', true)->first();
        $config  = $gateway ? json_decode($gateway->config, true) : [];
        $secretKey = $config['secret_key'] ?? env('STRIPE_SECRET_KEY', '');

        if (empty($secretKey)) {
            return response()->json([
                'status'  => false,
                'message' => 'Stripe is not configured.',
            ], 400);
        }

        $amount      = (int) $request->input('amount', 0);
        $currency    = strtolower($request->input('currency', 'usd'));
        $description = $request->input('description', '');

        if ($amount <= 0) {
            return response()->json(['status' => false, 'message' => 'Invalid amount.'], 400);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
        ])->asForm()->post('https://api.stripe.com/v1/payment_intents', [
            'amount'                            => $amount,
            'currency'                          => $currency,
            'description'                       => $description,
            'automatic_payment_methods[enabled]' => 'true',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'status'  => true,
                'message' => 'Payment intent created.',
                'data'    => [
                    'clientSecret'    => $data['client_secret'],
                    'paymentIntentId' => $data['id'],
                ],
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Failed to create payment intent.',
            'error'   => $response->json(),
        ], 400);
    }

    public function paypalCreateOrder(Request $request)
    {
        return response()->json([
            'status' => false,
            'message' => 'PayPal package payments are not enabled for this mobile API.',
        ], 410);
    }

    public function paypalCaptureOrder(Request $request)
    {
        return response()->json([
            'status' => false,
            'message' => 'PayPal package payments are not enabled for this mobile API.',
        ], 410);
    }
}
