<?php

namespace App\Services;

use App\Models\Frontend\Wallet;
use App\Models\Frontend\WalletHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    /**
     * Get or create the wallet for a user.
     */
    public function getOrCreate(int $userId): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0.00, 'currency' => 'USD']
        );
    }

    /**
     * Get the current balance for a user.
     */
    public function balance(int $userId): float
    {
        return (float) $this->getOrCreate($userId)->balance;
    }

    /**
     * Credit the wallet.
     *
     * @param  int     $userId
     * @param  float   $amount
     * @param  string  $note        Human-readable description
     * @param  string|null $referenceType  e.g. 'topup', 'escrow_release'
     * @param  int|null    $referenceId
     * @return WalletHistory
     */
    public function credit(int $userId, float $amount, string $note, ?string $referenceType = null, ?int $referenceId = null): WalletHistory
    {
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be positive.');
        }

        return DB::transaction(function () use ($userId, $amount, $note, $referenceType, $referenceId) {
            $wallet = $this->getOrCreate($userId);
            $wallet->increment('balance', $amount);
            $wallet->refresh();

            return WalletHistory::create([
                'user_id'        => $userId,
                'type'           => 'credit',
                'amount'         => $amount,
                'balance_after'  => $wallet->balance,
                'note'           => $note,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ]);
        });
    }

    /**
     * Debit the wallet. Throws if insufficient balance.
     *
     * @throws RuntimeException
     */
    public function debit(int $userId, float $amount, string $note, ?string $referenceType = null, ?int $referenceId = null): WalletHistory
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be positive.');
        }

        return DB::transaction(function () use ($userId, $amount, $note, $referenceType, $referenceId) {
            $this->getOrCreate($userId);
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();

            if ($wallet->balance < $amount) {
                throw new RuntimeException(
                    __('Insufficient wallet balance. You need :needed but only have :have.', [
                        'needed' => number_format($amount, 2),
                        'have'   => number_format($wallet->balance, 2),
                    ])
                );
            }

            $wallet->decrement('balance', $amount);
            $wallet->refresh();

            return WalletHistory::create([
                'user_id'        => $userId,
                'type'           => 'debit',
                'amount'         => $amount,
                'balance_after'  => $wallet->balance,
                'note'           => $note,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
            ]);
        });
    }

    /**
     * Transfer from one user to another (debit + credit in one transaction).
     */
    public function transfer(int $fromUserId, int $toUserId, float $amount, string $note, ?string $referenceType = null, ?int $referenceId = null): array
    {
        return DB::transaction(function () use ($fromUserId, $toUserId, $amount, $note, $referenceType, $referenceId) {
            $debitEntry  = $this->debit($fromUserId, $amount, $note, $referenceType, $referenceId);
            $creditEntry = $this->credit($toUserId,  $amount, $note, $referenceType, $referenceId);
            return [$debitEntry, $creditEntry];
        });
    }

    /**
     * Get paginated transaction history for a user.
     * Excludes pending (unconfirmed) Paystack top-up rows.
     */
    public function history(int $userId, int $perPage = 15)
    {
        return WalletHistory::where('user_id', $userId)
            ->where('reference_type', '!=', 'paystack_topup_pending')
            ->latest()
            ->paginate($perPage);
    }
}
