<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
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
            ->orderBy('sort_order')
            ->get();

        return $this->json('subscription plans', SubscriptionPlanResource::collection($plans)->toArray($request));
    }
}
