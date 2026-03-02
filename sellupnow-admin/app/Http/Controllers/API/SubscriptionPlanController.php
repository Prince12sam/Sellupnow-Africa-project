<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Return all active subscription plans.
     */
    public function index(Request $request)
    {
        $plans = SubscriptionPlan::query()
            ->active()
            ->orderBy('price')
            ->get();

        return $this->json('subscription plans', [
            'total' => $plans->count(),
            'plans' => $plans,
        ]);
    }
}
