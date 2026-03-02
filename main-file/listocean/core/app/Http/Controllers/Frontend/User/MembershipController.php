<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Services\MembershipService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function __construct(
        protected MembershipService $membershipService,
        protected WalletService     $walletService
    ) {}

    /**
     * Show all plans + highlight user's current plan.
     */
    public function plans()
    {
        $userId     = Auth::id();
        $plans      = $this->membershipService->getPlans();
        $current    = $this->membershipService->activeMembership($userId);
        $walletBalance = $this->walletService->balance($userId);

        return view('frontend.user.membership.plans', compact('plans', 'current', 'walletBalance'));
    }

    /**
     * Subscribe to a plan using wallet balance.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:membership_plans,id',
        ]);

        try {
            $this->membershipService->subscribe(Auth::id(), (int) $request->plan_id);
            toastr_success(__('You have successfully subscribed to the plan!'));
        } catch (\RuntimeException $e) {
            toastr_error($e->getMessage());
        }

        return redirect()->route('user.membership.plans');
    }

    /**
     * Cancel the active membership.
     */
    public function cancel()
    {
        $this->membershipService->cancel(Auth::id());
        toastr_success(__('Your membership has been cancelled.'));
        return redirect()->route('user.membership.plans');
    }
}
