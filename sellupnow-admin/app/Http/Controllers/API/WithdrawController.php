<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WithdrawController extends Controller
{
    /**
     * GET /api/client/withdraw/getWithdrawRequests
     * Returns paginated list of the user's withdrawal requests.
     */
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

    /**
     * POST /api/client/withdraw/submitWithdrawRequest
     * Creates a new withdrawal request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'          => 'required|numeric|min:1',
            'contact_number'  => 'required|string|max:30',
            'name'            => 'required|string|max:255',
            'withdraw_method' => 'required|string|max:100',
            'reason'          => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        $withdraw = Withdraw::create([
            'user_id'         => $user->id,
            'shop_id'         => $user->shop_id ?? null,
            'amount'          => $validated['amount'],
            'contact_number'  => $validated['contact_number'],
            'name'            => $validated['name'],
            'withdraw_method' => $validated['withdraw_method'],
            'reason'          => $validated['reason'] ?? null,
            'status'          => 'pending',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Withdrawal request submitted successfully.',
            'data'    => $withdraw,
        ]);
    }
}
