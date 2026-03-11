<?php

namespace App\Repositories;

use Abedin\Maker\Repositories\Repository;
use App\Http\Requests\WithdrawRequest;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Withdraw::class;
    }

    /**
     * store new withdraw
     */
    public static function storeByRequest(WithdrawRequest $request): Withdraw
    {
        $shop = generaleSetting('shop');

        return self::create([
            'shop_id' => $shop->id,
            'amount' => $request->amount,
            'name' => $request->name ?? auth()->user()->fullName,
            'contact_number' => $request->contact_number ?? auth()->user()->phone,
            'reason' => $request->message,
        ]);
    }

    /**
     * update withdraw
     */
    public static function updateWithdraw(Withdraw $withdraw, Request $request): Withdraw
    {
        return DB::transaction(function () use ($withdraw, $request) {
            $lockedWithdraw = Withdraw::whereKey($withdraw->id)
                ->lockForUpdate()
                ->firstOrFail();

            $previousStatus = $lockedWithdraw->status;
            $nextStatus = $request->status;

            $lockedWithdraw->update([
                'status' => $nextStatus,
                'reason' => $request->reason ?? $lockedWithdraw->reason,
            ]);

            $shouldRefund = $previousStatus !== 'rejected' && $nextStatus === 'rejected';

            if (! $shouldRefund) {
                return $lockedWithdraw;
            }

            $user = $lockedWithdraw->user ?? optional($lockedWithdraw->shop)->user;
            if (! $user) {
                return $lockedWithdraw;
            }

            $refundExists = WalletHistory::where('user_id', $user->id)
                ->where('reference_type', 'withdraw_refund')
                ->where('reference_id', $lockedWithdraw->id)
                ->lockForUpdate()
                ->exists();

            if ($refundExists) {
                return $lockedWithdraw;
            }

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );

            $wallet->increment('balance', $lockedWithdraw->amount);
            $wallet->refresh();

            WalletHistory::create([
                'user_id'        => $user->id,
                'type'           => 'credit',
                'amount'         => $lockedWithdraw->amount,
                'balance_after'  => $wallet->balance,
                'note'           => 'Withdrawal rejected — refund for request #' . $lockedWithdraw->id,
                'reference_type' => 'withdraw_refund',
                'reference_id'   => $lockedWithdraw->id,
                'transaction_id' => 'withdraw_refund_' . $lockedWithdraw->id,
            ]);

            return $lockedWithdraw;
        });
    }
}
