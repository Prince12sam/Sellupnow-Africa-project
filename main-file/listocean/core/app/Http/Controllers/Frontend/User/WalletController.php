<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Frontend\Wallet;
use App\Models\Frontend\WalletHistory;
use App\Services\PaystackService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService   $walletService,
        protected PaystackService $paystack,
    ) {}

    // ── Balance & history ────────────────────────────────────────────────────

    public function index()
    {
        $userId  = Auth::id();
        $wallet  = $this->walletService->getOrCreate($userId);
        $history = $this->walletService->history($userId, 20);

        return view('frontend.user.wallet.index', compact('wallet', 'history'));
    }

    // ── Top-up form ──────────────────────────────────────────────────────────

    public function topupForm()
    {
        $wallet           = $this->walletService->getOrCreate(Auth::id());
        $paystackEnabled  = $this->paystack->isEnabled();
        $paystackPublicKey = $this->paystack->publicKey();

        return view('frontend.user.wallet.topup', compact('wallet', 'paystackEnabled', 'paystackPublicKey'));
    }

    // ── Submit: redirect to Paystack ─────────────────────────────────────────

    public function topupSubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
        ]);

        $user   = Auth::user();
        $amount = (float) $request->amount;

        if (!$this->paystack->isEnabled()) {
            return back()->withErrors(['amount' => __('Payment gateway is currently unavailable. Please contact the administrator.')]);
        }

        // Unique reference for this transaction
        $reference = 'wt_' . strtoupper(Str::random(16)) . '_' . time();

        // Store pending so we can track / deduplicate on callback
        WalletHistory::create([
            'user_id'        => $user->id,
            'type'           => 'credit',
            'amount'         => $amount,
            'balance_after'  => $this->walletService->balance($user->id),
            'note'           => __('Wallet top-up via Paystack — pending'),
            'reference_type' => 'paystack_topup_pending',
            'transaction_id' => $reference,
            'payment_status' => 'pending',
            'payment_gateway'=> 'paystack',
        ]);

        try {
            $callbackUrl   = route('user.wallet.paystack.callback') . '?ref=' . $reference . '&uid=' . $user->id;
            $authUrl       = $this->paystack->initialize($user->email, $amount, $reference, $callbackUrl);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()->away($authUrl);
    }

    // ── Paystack callback ────────────────────────────────────────────────────

    public function paystackCallback(Request $request)
    {
        $reference = $request->query('ref') ?? $request->query('reference');

        if (!$reference) {
            toastr_error(__('Invalid payment reference.'));
            return redirect()->route('user.wallet.index');
        }

        $pending = WalletHistory::where('reference_type', 'paystack_topup_pending')
            ->where('transaction_id', $reference)
            ->first();

        if (!$pending) {
            toastr_error(__('Invalid or expired payment transaction.'));
            return redirect()->route('user.wallet.index');
        }

        $userId = (int) $pending->user_id;

        // Idempotency guard — already credited?
        $alreadyCredited = WalletHistory::where('reference_type', 'paystack_topup')
            ->where('transaction_id', $reference)
            ->exists();

        if ($alreadyCredited) {
            toastr_success(__('Your wallet has already been credited for this transaction.'));
            return redirect()->route('user.wallet.index');
        }

        // Verify with Paystack
        $txn = $this->paystack->verify($reference);

        if (!$txn || ($txn->status ?? '') !== 'success') {
            Log::warning('Paystack callback: verification failed', [
                'ref'    => $reference,
                'userId' => $userId,
                'status' => $txn->status ?? 'null',
            ]);
            toastr_error(__('Payment could not be verified. If you were charged, please contact support with reference: ') . $reference);
            return redirect()->route('user.wallet.index');
        }

        $expectedAmount = (int) round(((float) $pending->amount) * \App\Services\PaystackService::CURRENCY_SUBUNIT);
        $verifiedAmount = (int) ($txn->amount ?? 0);
        $verifiedReference = (string) ($txn->reference ?? '');
        $verifiedCurrency = strtoupper((string) ($txn->currency ?? ''));
        $expectedCurrency = strtoupper($this->paystack->currency());
        $userEmail = (string) DB::table('users')->where('id', $userId)->value('email');
        $verifiedEmail = (string) data_get((array) $txn, 'customer.email', '');

        if ($verifiedReference !== $reference) {
            toastr_error(__('Payment reference mismatch. Please contact support with reference: ') . $reference);
            return redirect()->route('user.wallet.index');
        }

        if ($verifiedAmount !== $expectedAmount) {
            toastr_error(__('Verified payment amount does not match the pending top-up. Please contact support with reference: ') . $reference);
            return redirect()->route('user.wallet.index');
        }

        if ($verifiedCurrency !== '' && $verifiedCurrency !== $expectedCurrency) {
            toastr_error(__('Verified payment currency does not match the pending top-up. Please contact support with reference: ') . $reference);
            return redirect()->route('user.wallet.index');
        }

        if ($verifiedEmail !== '' && $userEmail !== '' && strcasecmp($verifiedEmail, $userEmail) !== 0) {
            toastr_error(__('Verified payment email does not match the wallet owner. Please contact support with reference: ') . $reference);
            return redirect()->route('user.wallet.index');
        }

        // Amount from Paystack is in pesewas (GHS smallest unit); convert to Ghana Cedis
        $amountGhs = ($txn->amount ?? 0) / \App\Services\PaystackService::CURRENCY_SUBUNIT;

        if ($amountGhs <= 0) {
            toastr_error(__('Invalid payment amount received from gateway.'));
            return redirect()->route('user.wallet.index');
        }

        try {
            DB::transaction(function () use ($pending, $reference, $userId, $amountGhs) {
                $lockedPending = WalletHistory::where('id', $pending->id)
                    ->where('reference_type', 'paystack_topup_pending')
                    ->lockForUpdate()
                    ->first();

                if (! $lockedPending) {
                    return;
                }

                $alreadyCredited = WalletHistory::where('user_id', $userId)
                    ->where('reference_type', 'paystack_topup')
                    ->where('transaction_id', $reference)
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyCredited) {
                    return;
                }

                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
                if (! $wallet) {
                    $wallet = Wallet::create(['user_id' => $userId, 'balance' => 0.00, 'currency' => 'USD']);
                }

                $wallet->increment('balance', $amountGhs);
                $wallet->refresh();

                WalletHistory::create([
                    'user_id'         => $userId,
                    'type'            => 'credit',
                    'amount'          => $amountGhs,
                    'balance_after'   => $wallet->balance,
                    'note'            => __('Wallet top-up via Paystack (ref: :ref)', ['ref' => $reference]),
                    'reference_type'  => 'paystack_topup',
                    'transaction_id'  => $reference,
                    'payment_status'  => 'success',
                    'payment_gateway' => 'paystack',
                ]);

                $lockedPending->update([
                    'reference_type' => 'paystack_topup',
                    'payment_status' => 'success',
                    'note'           => __('Wallet top-up via Paystack (ref: :ref)', ['ref' => $reference]),
                ]);
            });

            toastr_success(__('Wallet credited with :amount successfully!', [
                'amount' => amount_with_currency_symbol($amountGhs),
            ]));
        } catch (\Throwable $e) {
            Log::error('Wallet credit failed after Paystack callback', [
                'ref'    => $reference,
                'userId' => $userId,
                'error'  => $e->getMessage(),
            ]);
            toastr_error(__('Payment verified but wallet credit failed. Please contact support with reference: ') . $reference);
        }

        return redirect()->route('user.wallet.index');
    }
}

