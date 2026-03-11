<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GeneraleSetting;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $start = max(0, (int) $request->query('start', 0));
        $limit = min(50, (int) $request->query('limit', 20));

        $items = Withdraw::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->offset($start * $limit)
            ->limit($limit)
            ->get([
                'id', 'amount', 'contact_number', 'name',
                'withdraw_method', 'reason', 'status', 'created_at',
            ]);

        return response()->json([
            'status' => true,
            'data'   => $items,
            'start'  => $start,
            'limit'  => $limit,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'          => 'required|numeric|min:1',
            'contact_number'  => 'required|string|max:30',
            'name'            => 'required|string|max:255',
            'withdraw_method' => 'required|string|max:100',
            'reason'          => 'nullable|string|max:500',
        ]);

        $user   = $request->user();
        $amount = (float) $validated['amount'];
        $settings = GeneraleSetting::query()->first();

        $minWithdraw = max(0, (float) ($settings->min_withdraw ?? 0));
        $maxWithdraw = max(0, (float) ($settings->max_withdraw ?? 0));
        $cooldownDays = max(0, (int) ($settings->withdraw_request ?? 0));

        if ($amount < $minWithdraw) {
            return response()->json([
                'status'  => false,
                'message' => 'Minimum withdrawal amount is GH₵' . number_format($minWithdraw, 2) . '.',
            ], 422);
        }

        if ($maxWithdraw > 0 && $amount > $maxWithdraw) {
            return response()->json([
                'status'  => false,
                'message' => 'Maximum withdrawal amount is GH₵' . number_format($maxWithdraw, 2) . '.',
            ], 422);
        }

        if ($cooldownDays > 0) {
            $lastRequest = Withdraw::where('user_id', $user->id)
                ->latest('created_at')
                ->first();

            if ($lastRequest && $lastRequest->created_at) {
                $nextAllowedAt = $lastRequest->created_at->copy()->addDays($cooldownDays);

                if ($nextAllowedAt->isFuture()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'You can submit another withdrawal request on ' . $nextAllowedAt->format('Y-m-d H:i') . '.',
                    ], 422);
                }
            }
        }

        // Check sufficient balance and debit in a transaction
        try {
            $withdraw = DB::transaction(function () use ($user, $amount, $validated) {
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                if (!$wallet) {
                    $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);
                }

                if ($wallet->balance < $amount) {
                    throw new \RuntimeException(
                        'Insufficient wallet balance. You have GH₵' . number_format($wallet->balance, 2)
                        . ' but need GH₵' . number_format($amount, 2) . '.'
                    );
                }

                // Debit wallet
                $wallet->decrement('balance', $amount);
                $wallet->refresh();

                // Create withdraw request
                $withdraw = Withdraw::create([
                    'user_id'         => $user->id,
                    'shop_id'         => $user->shop_id ?? null,
                    'amount'          => $amount,
                    'contact_number'  => $validated['contact_number'],
                    'name'            => $validated['name'],
                    'withdraw_method' => $validated['withdraw_method'],
                    'reason'          => $validated['reason'] ?? null,
                    'status'          => 'pending',
                ]);

                WalletHistory::create([
                    'user_id'        => $user->id,
                    'type'           => 'debit',
                    'amount'         => $amount,
                    'balance_after'  => $wallet->balance,
                    'note'           => 'Withdrawal request #' . $withdraw->id . ' — ' . $validated['withdraw_method'],
                    'reference_type' => 'withdraw',
                    'reference_id'   => $withdraw->id,
                    'transaction_id' => 'withdraw_' . $withdraw->id,
                ]);

                return $withdraw;
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        return response()->json([
            'status'  => true,
            'message' => 'Withdrawal request submitted successfully.',
            'balance' => (float) $wallet->balance,
            'data'    => $withdraw,
        ]);
    }
}
