<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Backend\Listing;
use App\Services\CommissionService;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EscrowController extends Controller
{
    private EscrowService $escrow;

    public function __construct()
    {
        $this->middleware(['auth', 'userEmailVerify', 'globalVariable', 'maintains_mode', 'setlang']);
        $this->escrow = new EscrowService();
    }

    // ─── Buyer: Start ────────────────────────────────────────────────────────

    /**
     * Show the escrow price breakdown for a listing.
     * GET /user/escrow/start/{slug}
     */
    public function start(string $slug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('status', 1)
            ->where('is_published', 1)
            ->firstOrFail();

        if ($listing->user_id === Auth::id()) {
            return back()->with('error', __('You cannot buy your own listing.'));
        }

        $price      = (float) $listing->price;
        $categoryId = (int) ($listing->category_id ?? 0);
        $commission = CommissionService::calculate($price, $categoryId);
        $total      = round($price + $commission, 2);

        return view('frontend.user.escrow.start', compact('listing', 'price', 'commission', 'total'));
    }

    /**
     * Process the escrow checkout (debit wallet + create transaction).
     * POST /user/escrow/checkout
     */
    public function checkout(Request $request)
    {
        $request->validate(['listing_id' => 'required|integer|exists:listings,id']);

        try {
            $txId = $this->escrow->fund(
                (int) $request->listing_id,
                Auth::id()
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('user.escrow.detail', $txId)
            ->with('success', __('Escrow funded! The seller has been notified.'));
    }

    // ─── Shared: Order list ───────────────────────────────────────────────────

    /**
     * GET /user/escrow/orders
     * Shows orders where the current user is buyer or seller.
     */
    public function orders(Request $request)
    {
        $userId = Auth::id();
        $tab    = $request->query('tab', 'buyer'); // 'buyer' or 'seller'

        $buyerOrders = DB::table('escrow_transactions as e')
            ->join('listings as l', 'l.id', '=', 'e.listing_id')
            ->leftJoin('users as seller', 'seller.id', '=', 'e.seller_user_id')
            ->where('e.buyer_user_id', $userId)
            ->select('e.*', 'l.title as listing_title', 'l.slug as listing_slug', 'l.image as listing_image',
                     'seller.name as counterparty_name')
            ->orderByDesc('e.id')
            ->paginate(10, ['*'], 'buyer_page')
            ->withQueryString();

        $sellerOrders = DB::table('escrow_transactions as e')
            ->join('listings as l', 'l.id', '=', 'e.listing_id')
            ->leftJoin('users as buyer', 'buyer.id', '=', 'e.buyer_user_id')
            ->where('e.seller_user_id', $userId)
            ->select('e.*', 'l.title as listing_title', 'l.slug as listing_slug', 'l.image as listing_image',
                     'buyer.name as counterparty_name')
            ->orderByDesc('e.id')
            ->paginate(10, ['*'], 'seller_page')
            ->withQueryString();

        $activeTab = in_array($tab, ['buyer', 'seller']) ? $tab : 'buyer';

        return view('frontend.user.escrow.orders', compact('buyerOrders', 'sellerOrders', 'activeTab'));
    }

    /**
     * GET /user/escrow/{id}
     */
    public function detail(int $id)
    {
        $tx = DB::table('escrow_transactions as e')
            ->join('listings as l', 'l.id', '=', 'e.listing_id')
            ->leftJoin('users as b', 'b.id', '=', 'e.buyer_user_id')
            ->leftJoin('users as s', 's.id', '=', 'e.seller_user_id')
            ->where('e.id', $id)
            ->select('e.*',
                'l.title as listing_title', 'l.slug as listing_slug', 'l.image as listing_image',
                'b.name as buyer_name',
                's.name as seller_name')
            ->first();

        $userId = Auth::id();

        if (!$tx || ((int) $tx->buyer_user_id !== $userId && (int) $tx->seller_user_id !== $userId)) {
            abort(404);
        }

        $events   = $this->escrow->events($id);
        $isBuyer  = (int) $tx->buyer_user_id === $userId;
        $isSeller = (int) $tx->seller_user_id === $userId;

        return view('frontend.user.escrow.detail', compact('tx', 'events', 'isBuyer', 'isSeller'));
    }

    // ─── Seller actions ───────────────────────────────────────────────────────

    /** POST /user/escrow/{id}/accept */
    public function accept(int $id)
    {
        try {
            $this->escrow->accept($id, Auth::id());
            return redirect()->route('user.escrow.detail', $id)
                ->with('success', __('Order accepted. Please deliver and mark as delivered.'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /user/escrow/{id}/deliver */
    public function deliver(int $id)
    {
        try {
            $this->escrow->deliver($id, Auth::id());
            return redirect()->route('user.escrow.detail', $id)
                ->with('success', __('Marked as delivered. Waiting for buyer confirmation.'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ─── Buyer actions ────────────────────────────────────────────────────────

    /** POST /user/escrow/{id}/confirm */
    public function confirm(int $id)
    {
        try {
            $this->escrow->confirm($id, Auth::id());
            return redirect()->route('user.escrow.detail', $id)
                ->with('success', __('Confirmed! Payment has been released to the seller.'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** POST /user/escrow/{id}/dispute */
    public function dispute(Request $request, int $id)
    {
        $request->validate(['note' => 'nullable|string|max:1000']);

        try {
            $this->escrow->dispute($id, Auth::id(), $request->input('note'));
            return redirect()->route('user.escrow.detail', $id)
                ->with('success', __('Dispute raised. Our team will review and get back to you.'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
