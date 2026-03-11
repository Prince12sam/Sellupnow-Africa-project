<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscrowApiController extends Controller
{
    /**
     * GET /api/client/escrow/getOrders?tab=buyer|seller&page=1&limit=20
     * Returns the authenticated user's escrow orders as buyer or seller.
     */
    public function getOrders(Request $request)
    {
        $user  = $request->user();
        $tab   = $request->query('tab', 'buyer');
        $page  = max(1, (int) $request->query('page', 1));
        $limit = min(50, (int) $request->query('limit', 20));

        $isbuyer = $tab !== 'seller';
        $col     = $isbuyer ? 'buyer_user_id' : 'seller_user_id';
        $counter = $isbuyer ? 'seller_user_id' : 'buyer_user_id';

        $query = DB::table('escrow_transactions as e')
            ->leftJoin('listings as l', 'l.id', '=', 'e.listing_id')
            ->leftJoin('users as cp', "cp.id", '=', "e.{$counter}")
            ->where("e.{$col}", $user->id)
            ->select([
                'e.id',
                'e.listing_id',
                'e.total_amount',
                'e.currency',
                'e.status',
                'e.created_at',
                'e.funded_at',
                'e.released_at',
                'l.title as listing_title',
                'l.slug as listing_slug',
                'cp.name as counterparty_name',
            ])
            ->orderBy('e.created_at', 'desc');

        $total  = $query->count();
        $orders = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'status' => true,
            'tab'    => $tab,
            'total'  => $total,
            'page'   => $page,
            'limit'  => $limit,
            'data'   => $orders,
        ]);
    }

    /**
     * GET /api/client/escrow/getOrderDetail?id=X
     * Returns the full detail of a single escrow order the auth user owns.
     */
    public function getOrderDetail(Request $request)
    {
        $user = $request->user();
        $id   = (int) $request->query('id');

        $tx = DB::table('escrow_transactions as e')
            ->leftJoin('listings as l', 'l.id', '=', 'e.listing_id')
            ->leftJoin('users as buyer',  'buyer.id',  '=', 'e.buyer_user_id')
            ->leftJoin('users as seller', 'seller.id', '=', 'e.seller_user_id')
            ->where('e.id', $id)
            ->where(function ($q) use ($user) {
                $q->where('e.buyer_user_id', $user->id)
                  ->orWhere('e.seller_user_id', $user->id);
            })
            ->select([
                'e.id',
                'e.listing_id',
                'e.buyer_user_id',
                'e.seller_user_id',
                'e.listing_price',
                'e.admin_fee_amount',
                'e.total_amount',
                'e.currency',
                'e.status',
                'e.payment_gateway',
                'e.payment_transaction_id',
                'e.funded_at',
                'e.seller_accepted_at',
                'e.seller_delivered_at',
                'e.buyer_confirmed_at',
                'e.released_at',
                'e.buyer_confirm_deadline_at',
                'e.seller_accept_deadline_at',
                'e.created_at',
                'l.title as listing_title',
                'l.slug as listing_slug',
                'buyer.name as buyer_name',
                'seller.name as seller_name',
            ])
            ->first();

        if (! $tx) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $tx,
        ]);
    }

    /**
     * GET /api/client/escrow/getBreakdown?listing_id=X
     * Returns the escrow price breakdown for a listing (before initiating).
     */
    public function getBreakdown(Request $request)
    {
        $user      = $request->user();
        $listingId = (int) $request->query('listing_id');

        $listing = Listing::where('id', $listingId)->isActive()->first();
        if (! $listing) {
            return response()->json(['status' => false, 'message' => 'Listing not found.'], 404);
        }
        if ((int) $listing->user_id === $user->id) {
            return response()->json(['status' => false, 'message' => 'You cannot buy your own listing.'], 400);
        }
        if (! $listing->escrow_enabled) {
            return response()->json(['status' => false, 'message' => 'Escrow is not enabled for this listing.'], 400);
        }

        $price      = (float) $listing->price;
        $result     = CommissionService::calculate($price, ['category_id' => $listing->category_id ?? 0]);
        $commission = $result['commission'] ?? 0;
        $total      = round($price + $commission, 2);

        $wallet  = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        $balance = (float) $wallet->balance;

        return response()->json([
            'status'          => true,
            'listing_id'      => $listing->id,
            'listing_title'   => $listing->title,
            'listing_price'   => $price,
            'platform_fee'    => $commission,
            'total'           => $total,
            'wallet_balance'  => $balance,
            'can_afford'      => $balance >= $total,
            'currency'        => $wallet->currency ?? 'GHS',
        ]);
    }

    /**
     * POST /api/client/escrow/initiateEscrow
     * Body: { listing_id: int }
     * Debits buyer wallet and creates a funded escrow transaction.
     */
    public function initiateEscrow(Request $request)
    {
        $request->validate(['listing_id' => 'required|integer']);

        $user      = $request->user();
        $listingId = (int) $request->input('listing_id');

        $listing = Listing::where('id', $listingId)->isActive()->first();
        if (! $listing) {
            return response()->json(['status' => false, 'message' => 'Listing not found.'], 404);
        }
        if ((int) $listing->user_id === $user->id) {
            return response()->json(['status' => false, 'message' => 'You cannot buy your own listing.'], 400);
        }
        if (! $listing->escrow_enabled) {
            return response()->json(['status' => false, 'message' => 'Escrow is not enabled for this listing.'], 400);
        }

        $price      = (float) $listing->price;
        $result     = CommissionService::calculate($price, ['category_id' => $listing->category_id ?? 0]);
        $commission = $result['commission'] ?? 0;
        $total      = round($price + $commission, 2);

        Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        return DB::transaction(function () use ($listing, $user, $price, $commission, $total, $listingId) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ((float) $wallet->balance < $total) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Insufficient wallet balance. Please top up first.',
                    'balance' => (float) $wallet->balance,
                    'total'   => $total,
                ], 400);
            }

            // Debit buyer wallet
            $wallet->decrement('balance', $total);

            Transaction::create([
                'wallet_id'      => $wallet->id,
                'amount'         => $total,
                'type'           => 'debit',
                'purpose'        => 'escrow',
                'note'           => 'Escrow payment for: ' . $listing->title,
                'transaction_id' => 'ESC-' . time() . '-' . $listingId,
            ]);

            // Create escrow transaction
            $txId = DB::table('escrow_transactions')->insertGetId([
                'listing_id'                => $listingId,
                'buyer_user_id'             => $user->id,
                'seller_user_id'            => (int) $listing->user_id,
                'listing_price'             => $price,
                'admin_fee_amount'          => $commission,
                'total_amount'              => $total,
                'currency'                  => $wallet->currency ?? 'GHS',
                'status'                    => 'funded',
                'payment_gateway'           => 'wallet',
                'funded_at'                 => now(),
                'seller_accept_deadline_at' => now()->addDays(3),
                'created_at'                => now(),
                'updated_at'                => now(),
            ]);

            // Log event
            DB::table('escrow_events')->insert([
                'escrow_transaction_id' => $txId,
                'event'                 => 'funded',
                'actor_type'            => 'buyer',
                'actor_user_id'         => $user->id,
                'from_status'           => 'payment_pending',
                'to_status'             => 'funded',
                'created_at'            => now(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Escrow funded successfully! The seller has been notified.',
                'data'    => [
                    'escrow_id'    => $txId,
                    'total_amount' => $total,
                    'status'       => 'funded',
                ],
            ]);
        });
    }
}
