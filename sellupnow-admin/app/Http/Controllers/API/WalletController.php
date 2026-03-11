<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    // ── Balance & recent transactions ────────────────────────────────────

    public function getBalance(Request $request)
    {
        $user   = $request->user();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $rows = WalletHistory::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNull('reference_type')
                  ->orWhere('reference_type', '!=', 'paystack_topup_pending');
            })
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json([
            'status'  => true,
            'balance' => (float) $wallet->balance,
            'data'    => $rows->map(fn ($r) => $this->formatRow($r)),
        ]);
    }

    // ── Paginated transaction history ────────────────────────────────────

    public function getTransactions(Request $request)
    {
        $user   = $request->user();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $start = max(0, (int) $request->query('start', 0));
        $limit = min(50, (int) $request->query('limit', 20));

        $rows = WalletHistory::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNull('reference_type')
                  ->orWhere('reference_type', '!=', 'paystack_topup_pending');
            })
            ->orderBy('created_at', 'desc')
            ->offset($start * $limit)
            ->limit($limit)
            ->get();

        return response()->json([
            'status'  => true,
            'balance' => (float) $wallet->balance,
            'data'    => $rows->map(fn ($r) => $this->formatRow($r)),
            'start'   => $start,
            'limit'   => $limit,
        ]);
    }

    // ── Paystack wallet top-up: initialize ───────────────────────────────

    public function topupInit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
        ]);

        $user   = $request->user();
        $amount = (float) $request->amount;

        $creds = $this->paystackCredentials();
        if (!$creds) {
            return response()->json(['status' => false, 'message' => 'Payment gateway is not configured.'], 422);
        }

        $reference = 'wt_' . strtoupper(Str::random(16)) . '_' . time();
        $currency  = strtoupper(trim($creds['currency'] ?? 'GHS')) ?: 'GHS';

        // Record a pending entry
        WalletHistory::create([
            'user_id'         => $user->id,
            'type'            => 'credit',
            'amount'          => $amount,
            'balance_after'   => (float) Wallet::where('user_id', $user->id)->value('balance') ?? 0,
            'note'            => 'Wallet top-up via Paystack — pending',
            'reference_type'  => 'paystack_topup_pending',
            'transaction_id'  => $reference,
            'payment_status'  => 'pending',
            'payment_gateway' => 'paystack',
        ]);

        $amountSubunit = (int) round($amount * 100);

        $payload = [
            'email'     => $user->email,
            'amount'    => $amountSubunit,
            'currency'  => $currency,
            'reference' => $reference,
            'metadata'  => [
                'user_id' => $user->id,
                'purpose' => 'wallet_topup',
            ],
            'channels'  => $currency === 'GHS'
                ? ['card', 'mobile_money', 'bank']
                : ['card', 'bank', 'ussd', 'mobile_money', 'bank_transfer'],
        ];

        try {
            $response = Http::withToken($creds['secret_key'])
                ->post('https://api.paystack.co/transaction/initialize', $payload);

            if (!$response->successful() || !$response->json('status')) {
                $msg = $response->json('message') ?? 'Paystack initialization failed';
                Log::error('Paystack init failed', ['body' => $response->body()]);
                return response()->json(['status' => false, 'message' => $msg], 422);
            }

            return response()->json([
                'status'            => true,
                'authorization_url' => $response->json('data.authorization_url'),
                'reference'         => $reference,
                'access_code'       => $response->json('data.access_code'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Paystack init exception', ['err' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Payment gateway error.'], 500);
        }
    }

    // ── Paystack wallet top-up: verify ───────────────────────────────────

    public function topupVerify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:191',
        ]);

        $reference = $request->reference;
        $user      = $request->user();

        $alreadyCredited = WalletHistory::where('user_id', $user->id)
            ->where('reference_type', 'paystack_topup')
            ->where('transaction_id', $reference)
            ->exists();

        if ($alreadyCredited) {
            $wallet = Wallet::where('user_id', $user->id)->first();

            return response()->json([
                'status'  => true,
                'message' => 'Wallet already credited for this transaction.',
                'balance' => (float) ($wallet->balance ?? 0),
            ]);
        }

        $pending = WalletHistory::where('reference_type', 'paystack_topup_pending')
            ->where('transaction_id', $reference)
            ->where('user_id', $user->id)
            ->first();

        if (!$pending) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired payment reference.'], 404);
        }

        $creds = $this->paystackCredentials();
        if (!$creds) {
            return response()->json(['status' => false, 'message' => 'Payment gateway not configured.'], 422);
        }

        try {
            $response = Http::withToken($creds['secret_key'])
                ->get('https://api.paystack.co/transaction/verify/' . urlencode($reference));

            if (!$response->successful()) {
                return response()->json(['status' => false, 'message' => 'Verification failed.'], 422);
            }

            $txn = $response->json('data');

            if (($txn['status'] ?? '') !== 'success') {
                return response()->json(['status' => false, 'message' => 'Payment was not successful.'], 422);
            }

            $expectedAmount = (int) round(((float) $pending->amount) * 100);
            $verifiedAmount = (int) ($txn['amount'] ?? 0);
            $verifiedReference = (string) ($txn['reference'] ?? '');
            $verifiedCurrency = strtoupper((string) ($txn['currency'] ?? ''));
            $expectedCurrency = strtoupper(trim($creds['currency'] ?? 'GHS')) ?: 'GHS';
            $verifiedEmail = (string) data_get($txn, 'customer.email', '');

            if ($verifiedReference !== $reference) {
                return response()->json(['status' => false, 'message' => 'Payment reference mismatch.'], 422);
            }

            if ($verifiedAmount !== $expectedAmount) {
                return response()->json(['status' => false, 'message' => 'Verified payment amount does not match the pending top-up.'], 422);
            }

            if ($verifiedCurrency !== '' && $verifiedCurrency !== $expectedCurrency) {
                return response()->json(['status' => false, 'message' => 'Verified payment currency does not match the pending top-up.'], 422);
            }

            if ($verifiedEmail !== '' && strcasecmp($verifiedEmail, (string) $user->email) !== 0) {
                return response()->json(['status' => false, 'message' => 'Verified payment email does not match the authenticated user.'], 422);
            }

            $amountGhs = ($txn['amount'] ?? 0) / 100;
            if ($amountGhs <= 0) {
                return response()->json(['status' => false, 'message' => 'Invalid payment amount.'], 422);
            }

            // Credit wallet
            DB::transaction(function () use ($user, $amountGhs, $reference, $pending) {
                $lockedPending = WalletHistory::where('id', $pending->id)
                    ->where('reference_type', 'paystack_topup_pending')
                    ->lockForUpdate()
                    ->first();

                if (! $lockedPending) {
                    return;
                }

                $alreadyCredited = WalletHistory::where('user_id', $user->id)
                    ->where('reference_type', 'paystack_topup')
                    ->where('transaction_id', $reference)
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyCredited) {
                    return;
                }

                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                if (!$wallet) {
                    $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);
                }
                $wallet->increment('balance', $amountGhs);
                $wallet->refresh();

                // Create confirmed history entry
                WalletHistory::create([
                    'user_id'         => $user->id,
                    'type'            => 'credit',
                    'amount'          => $amountGhs,
                    'balance_after'   => $wallet->balance,
                    'note'            => "Wallet top-up via Paystack (ref: {$reference})",
                    'reference_type'  => 'paystack_topup',
                    'transaction_id'  => $reference,
                    'payment_status'  => 'success',
                    'payment_gateway' => 'paystack',
                ]);

                // Mark pending row
                $lockedPending->update([
                    'reference_type'  => 'paystack_topup',
                    'payment_status'  => 'success',
                    'note'            => "Wallet top-up via Paystack (ref: {$reference})",
                ]);
            });

            $wallet = Wallet::where('user_id', $user->id)->first();

            return response()->json([
                'status'  => true,
                'message' => 'Wallet credited successfully!',
                'balance' => (float) $wallet->balance,
            ]);

        } catch (\Throwable $e) {
            Log::error('Paystack verify exception', ['ref' => $reference, 'err' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Verification error.'], 500);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function formatRow(WalletHistory $r): array
    {
        return [
            'id'             => $r->id,
            'amount'         => (float) $r->amount,
            'type'           => $r->type,
            'purpose'        => $this->humanPurpose($r->reference_type),
            'note'           => $r->note,
            'transaction_id' => $r->transaction_id,
            'created_at'     => $r->created_at,
        ];
    }

    private function humanPurpose(?string $refType): string
    {
        return match ($refType) {
            'paystack_topup'   => 'Wallet Top-up',
            'admin_credit'     => 'Admin Credit',
            'admin_debit'      => 'Admin Debit',
            'escrow_release'   => 'Escrow Release',
            'escrow_fund'      => 'Escrow Payment',
            'commission'       => 'Commission',
            'refund'           => 'Refund',
            'featured_ad'      => 'Featured Ad',
            'boost'            => 'Listing Boost',
            'membership'       => 'Membership',
            'withdraw'         => 'Withdrawal',
            default            => ucwords(str_replace('_', ' ', $refType ?? 'Transaction')),
        };
    }

    private function paystackCredentials(): ?array
    {
        $row = DB::table('payment_gateways')->where('name', 'paystack')->first();
        if (!$row) {
            return null;
        }
        $creds = json_decode((string) $row->credentials, true);
        if (!is_array($creds) || empty($creds['secret_key']) || (int) $row->status !== 1) {
            return null;
        }
        return array_merge($creds, [
            'status'    => (int) $row->status,
            'test_mode' => (int) ($row->test_mode ?? 0),
        ]);
    }
}
