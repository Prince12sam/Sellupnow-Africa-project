<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListoceanWalletController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    public function index(Request $request): View
    {
        $db = $this->listocean();

        $hasUsersTable = false;
        $hasWalletTable = false;
        $hasHistoryTable = false;

        try {
            $schema = $db->getSchemaBuilder();
            $hasUsersTable = $schema->hasTable('users');
            $hasWalletTable = $schema->hasTable('wallets');
            $hasHistoryTable = $schema->hasTable('wallet_histories');
        } catch (\Throwable $th) {
            $hasUsersTable = false;
            $hasWalletTable = false;
            $hasHistoryTable = false;
        }

        $q = trim((string) $request->query('q', ''));
        $userId = (int) $request->query('user_id', 0);

        $users = collect();
        if ($hasUsersTable && $q !== '') {
            $usersQuery = $db->table('users')->whereNull('deleted_at');
            if (ctype_digit($q)) {
                $usersQuery->where('id', (int) $q);
            } else {
                $usersQuery->where(function ($sub) use ($q) {
                    $sub->where('email', 'like', '%'.$q.'%')
                        ->orWhere('username', 'like', '%'.$q.'%')
                        ->orWhere(DB::raw("COALESCE(first_name,'') || ' ' || COALESCE(last_name,'')"), 'like', '%'.$q.'%');
                });
            }

            $users = $usersQuery
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        $selectedUser = null;
        $wallet = null;
        $walletHistories = collect();

        if ($hasUsersTable && $userId > 0) {
            $selectedUser = $db->table('users')->where('id', $userId)->whereNull('deleted_at')->first();
        }

        if ($selectedUser && $hasWalletTable) {
            $wallet = $db->table('wallets')->where('user_id', (int) $selectedUser->id)->first();
        }

        if ($selectedUser && $hasHistoryTable) {
            $walletHistories = $db->table('wallet_histories')
                ->where('user_id', (int) $selectedUser->id)
                ->orderByDesc('id')
                ->limit(50)
                ->get();
        }

        return view('admin.listocean-wallet.index', [
            'hasUsersTable' => $hasUsersTable,
            'hasWalletTable' => $hasWalletTable,
            'hasHistoryTable' => $hasHistoryTable,
            'q' => $q,
            'users' => $users,
            'selectedUser' => $selectedUser,
            'wallet' => $wallet,
            'walletHistories' => $walletHistories,
        ]);
    }

    public function adjust(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'min:1'],
            'action' => ['required', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $db = $this->listocean();

        try {
            $schema = $db->getSchemaBuilder();
            if (! $schema->hasTable('wallets') || ! $schema->hasTable('wallet_histories')) {
                return back()->withErrors([
                    'wallet' => __('Customer Web wallet tables are missing. Run the customer web wallet migrations first.'),
                ]);
            }
        } catch (\Throwable $th) {
            return back()->withErrors([
                'wallet' => __('Unable to access Customer Web wallet tables. Please check the listocean DB connection.'),
            ]);
        }

        $userId = (int) $data['user_id'];
        $amount = (float) $data['amount'];
        $action = (string) $data['action'];
        $note = isset($data['note']) ? trim((string) $data['note']) : '';

        $admin = $request->user();
        $adminId = (int) ($admin->id ?? 0);

        try {
            $db->transaction(function () use ($db, $userId, $amount, $action, $note, $adminId) {
                $now = now();

                $wallet = $db->table('wallets')->where('user_id', $userId)->lockForUpdate()->first();
                if (! $wallet) {
                    $db->table('wallets')->insert([
                        'user_id' => $userId,
                        'balance' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $wallet = $db->table('wallets')->where('user_id', $userId)->lockForUpdate()->first();
                }

                $previousBalance = (float) ($wallet->balance ?? 0);
                $newBalance = $previousBalance;

                if ($action === 'credit') {
                    $newBalance = $previousBalance + $amount;
                } else {
                    if ($previousBalance < $amount) {
                        throw new \RuntimeException(__('Insufficient wallet balance.'));
                    }
                    $newBalance = $previousBalance - $amount;
                }

                $db->table('wallets')->where('user_id', $userId)->update([
                    'balance' => $newBalance,
                    'updated_at' => $now,
                ]);

                $historyType = $action === 'credit' ? 'admin_credit' : 'admin_debit';
                $metadata = [
                    'admin_id' => $adminId,
                    'note' => $note !== '' ? $note : null,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $newBalance,
                ];

                $db->table('wallet_histories')->insert([
                    'user_id' => $userId,
                    'type' => $historyType,
                    'amount' => $amount,
                    'payment_gateway' => 'admin',
                    'payment_status' => 'complete',
                    'transaction_id' => 'ADMIN-'.Str::upper(Str::random(12)),
                    'metadata' => json_encode($metadata),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
        } catch (\Throwable $th) {
            return back()->withErrors([
                'wallet' => $th->getMessage() ?: __('Unable to update wallet.'),
            ])->withInput();
        }

        return to_route('admin.siteWallet.index', ['user_id' => $userId])
            ->withSuccess(__('Wallet updated successfully.'));
    }
}
