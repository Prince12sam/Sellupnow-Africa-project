<?php

namespace App\Services;

use App\Models\Backend\Listing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EscrowService
{
    private WalletService $wallet;

    public function __construct()
    {
        $this->wallet = new WalletService();
    }

    // ─── Queries ──────────────────────────────────────────────────────────────

    public function findTransaction(int $id): ?object
    {
        return DB::table('escrow_transactions')->where('id', $id)->first();
    }

    public function transactionWithListing(int $id): ?object
    {
        return DB::table('escrow_transactions as e')
            ->join('listings as l', 'l.id', '=', 'e.listing_id')
            ->where('e.id', $id)
            ->select('e.*', 'l.title as listing_title', 'l.slug as listing_slug', 'l.image as listing_image')
            ->first();
    }

    public function events(int $transactionId): \Illuminate\Support\Collection
    {
        return DB::table('escrow_events')
            ->where('escrow_transaction_id', $transactionId)
            ->orderBy('created_at')
            ->get();
    }

    // ─── State transitions ────────────────────────────────────────────────────

    /**
     * Fund the escrow — called after successful wallet debit.
     */
    public function fund(int $listingId, int $buyerUserId): int
    {
        $listing = Listing::findOrFail($listingId);

        if ($listing->user_id === $buyerUserId) {
            throw new RuntimeException(__('You cannot buy your own listing.'));
        }

        $sellerId   = (int) $listing->user_id;
        $price      = (float) $listing->price;
        $categoryId = (int) ($listing->category_id ?? 0);
        $commission = CommissionService::calculate($price, $categoryId);
        $total      = round($price + $commission, 2);

        // Debit buyer wallet
        $this->wallet->debit(
            $buyerUserId,
            $total,
            __('Escrow payment for listing: :title', ['title' => $listing->title]),
            'escrow',
        );

        // Create escrow transaction
        $txId = DB::table('escrow_transactions')->insertGetId([
            'listing_id'              => $listingId,
            'buyer_user_id'           => $buyerUserId,
            'seller_user_id'          => $sellerId,
            'listing_price'           => $price,
            'admin_fee_amount'        => $commission,
            'total_amount'            => $total,
            'currency'                => get_admin_default_currency()->code,
            'status'                  => 'funded',
            'payment_gateway'         => 'wallet',
            'payment_transaction_id'  => null,
            'funded_at'               => now(),
            'seller_accept_deadline_at' => now()->addDays(3),
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        $this->logEvent($txId, 'funded', 'buyer', $buyerUserId, 'payment_pending', 'funded');

        return $txId;
    }

    /**
     * Seller accepts the order.
     */
    public function accept(int $txId, int $sellerUserId): void
    {
        $tx = $this->findTransaction($txId);
        $this->assertOwner($tx, $sellerUserId, 'seller');
        $this->assertStatus($tx, 'funded');

        DB::table('escrow_transactions')->where('id', $txId)->update([
            'status'                   => 'seller_confirmed',
            'seller_accepted_at'       => now(),
            'buyer_confirm_deadline_at' => now()->addDays(7),
            'updated_at'               => now(),
        ]);

        $this->logEvent($txId, 'seller_accepted', 'seller', $sellerUserId, 'funded', 'seller_confirmed');
    }

    /**
     * Seller marks the order as delivered.
     */
    public function deliver(int $txId, int $sellerUserId): void
    {
        $tx = $this->findTransaction($txId);
        $this->assertOwner($tx, $sellerUserId, 'seller');
        $this->assertStatus($tx, 'seller_confirmed');

        DB::table('escrow_transactions')->where('id', $txId)->update([
            'status'                    => 'seller_delivered',
            'seller_delivered_at'       => now(),
            'buyer_confirm_deadline_at' => now()->addDays(7),
            'updated_at'                => now(),
        ]);

        $this->logEvent($txId, 'seller_delivered', 'seller', $sellerUserId, 'seller_confirmed', 'seller_delivered');
    }

    /**
     * Buyer confirms receipt → release funds to seller.
     */
    public function confirm(int $txId, int $buyerUserId): void
    {
        $tx = $this->findTransaction($txId);
        $this->assertOwner($tx, $buyerUserId, 'buyer');
        $this->assertStatus($tx, 'seller_delivered');

        $this->release($tx, 'buyer_confirmed', $buyerUserId);
    }

    /**
     * Buyer opens a dispute.
     */
    public function dispute(int $txId, int $buyerUserId, ?string $note = null): void
    {
        $tx = $this->findTransaction($txId);
        $this->assertOwner($tx, $buyerUserId, 'buyer');

        $allowedStatuses = ['funded', 'seller_confirmed', 'seller_delivered'];
        if (!in_array($tx->status, $allowedStatuses, true)) {
            throw new RuntimeException(__('This order cannot be disputed at its current stage.'));
        }

        DB::table('escrow_transactions')->where('id', $txId)->update([
            'status'     => 'disputed',
            'updated_at' => now(),
        ]);

        $this->logEvent($txId, 'disputed', 'buyer', $buyerUserId, $tx->status, 'disputed', $note);
    }

    /**
     * Release funds to seller (after confirmation or auto-release).
     */
    public function release(object $tx, string $triggerEvent, int $actorUserId): void
    {
        if ($tx->status === 'released') return;

        $sellerAmount = max(0.0, (float) $tx->listing_price - (float) $tx->admin_fee_amount);

        $this->wallet->credit(
            (int) $tx->seller_user_id,
            $sellerAmount,
            __('Escrow released for order #:id', ['id' => $tx->id]),
            'escrow_release',
            (int) $tx->id,
        );

        // Credit platform commission to first admin user (id=1 or configurable)
        if ((float) $tx->admin_fee_amount > 0) {
            $platformUserId = (int) (get_static_option('escrow_platform_user_id') ?? 1);
            try {
                $this->wallet->credit(
                    $platformUserId,
                    (float) $tx->admin_fee_amount,
                    __('Commission for escrow order #:id', ['id' => $tx->id]),
                    'escrow_commission',
                    (int) $tx->id,
                );
            } catch (\Throwable) {
                // Platform wallet credit failure should not block release
            }
        }

        DB::table('escrow_transactions')->where('id', $tx->id)->update([
            'status'              => 'released',
            'buyer_confirmed_at'  => now(),
            'released_at'         => now(),
            'updated_at'          => now(),
        ]);

        $this->logEvent((int) $tx->id, $triggerEvent, 'buyer', $actorUserId, $tx->status, 'released');
    }

    /**
     * Refund the total_amount to the buyer (admin or auto-cancel).
     */
    public function refundToBuyer(object $tx, string $reason, int $actorUserId = 0): void
    {
        if (in_array($tx->status, ['released', 'refunded'], true)) return;

        $this->wallet->credit(
            (int) $tx->buyer_user_id,
            (float) $tx->total_amount,
            __('Escrow refund for order #:id — :reason', ['id' => $tx->id, 'reason' => $reason]),
            'escrow_refund',
            (int) $tx->id,
        );

        DB::table('escrow_transactions')->where('id', $tx->id)->update([
            'status'     => 'refunded',
            'updated_at' => now(),
        ]);

        $actorType = $actorUserId > 0 ? 'admin' : 'system';
        $this->logEvent((int) $tx->id, 'refunded', $actorType, $actorUserId, $tx->status, 'refunded', $reason);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function logEvent(int $txId, string $event, string $actorType, int $actorUserId, ?string $from, ?string $to, ?string $note = null): void
    {
        DB::table('escrow_events')->insert([
            'escrow_transaction_id' => $txId,
            'event'                 => $event,
            'actor_type'            => $actorType,
            'actor_user_id'         => $actorUserId,
            'from_status'           => $from,
            'to_status'             => $to,
            'note'                  => $note,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
    }

    private function assertOwner(object $tx, int $userId, string $role): void
    {
        $colName = $role . '_user_id';
        if (!isset($tx->$colName) || (int) $tx->$colName !== $userId) {
            throw new RuntimeException(__('You are not authorised to perform this action.'));
        }
    }

    private function assertStatus(object $tx, string ...$allowed): void
    {
        if (!in_array($tx->status, $allowed, true)) {
            throw new RuntimeException(__('This action is not valid for the current order status.'));
        }
    }
}
