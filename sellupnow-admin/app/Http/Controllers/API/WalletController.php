<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * GET /api/client/wallet/getBalance
     * Returns the authenticated user's wallet balance and recent transactions.
     */
    public function getBalance(Request $request)
    {
        $user   = $request->user();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $transactions = Transaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get(['id', 'amount', 'type', 'purpose', 'note', 'transaction_id', 'created_at']);

        return response()->json([
            'status'  => true,
            'balance' => (float) $wallet->balance,
            'data'    => $transactions,
        ]);
    }

    /**
     * GET /api/client/wallet/getTransactions
     * Full paginated transaction list.
     */
    public function getTransactions(Request $request)
    {
        $user   = $request->user();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $start = max(0, (int) $request->query('start', 0));
        $limit = min(50, (int) $request->query('limit', 20));

        $transactions = Transaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->offset($start * $limit)
            ->limit($limit)
            ->get(['id', 'amount', 'type', 'purpose', 'note', 'transaction_id', 'created_at']);

        return response()->json([
            'status'  => true,
            'balance' => (float) $wallet->balance,
            'data'    => $transactions,
            'start'   => $start,
            'limit'   => $limit,
        ]);
    }
}
