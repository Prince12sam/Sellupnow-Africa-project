<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PurchaseHistory;
use Illuminate\Http\Request;

class PurchaseHistoryController extends Controller
{
    /** Record a completed purchase (called by the app after payment confirmation). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'purchase_type'         => 'required|string|in:subscription,featured_ad,other',
            'package_id'            => 'nullable|integer',
            'amount'                => 'required|numeric|min:0',
            'currency'              => 'nullable|string|max:10',
            'payment_method'        => 'nullable|string|max:50',
            'transaction_reference' => 'nullable|string|max:255',
            'status'                => 'nullable|string|in:pending,completed,failed,refunded',
            'meta'                  => 'nullable|array',
        ]);

        $purchase = PurchaseHistory::create(array_merge($data, [
            'user_id'  => auth('api')->id(),
            'status'   => $data['status'] ?? 'completed',
            'currency' => $data['currency'] ?? 'USD',
        ]));

        return $this->json('Purchase recorded', [
            'purchase_id' => $purchase->id,
            'status'      => $purchase->status,
        ]);
    }

    /** List purchase history for the authenticated user. */
    public function index(Request $request)
    {
        $page    = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 20), 1);
        $skip    = ($page - 1) * $perPage;

        $userId = auth('api')->id();

        $query = PurchaseHistory::where('user_id', $userId)->latest('id');

        $total   = $query->count();
        $history = $query->skip($skip)->take($perPage)->get();

        return $this->json('purchase history', [
            'total'   => $total,
            'history' => $history,
        ]);
    }
}
