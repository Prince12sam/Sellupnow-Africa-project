<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ReportReason;
use Illuminate\Http\Request;

class ReportReasonController extends Controller
{
    /**
     * Return all active report reasons.
     */
    public function index(Request $request)
    {
        $reasons = ReportReason::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->json('report reasons', [
            'total'   => $reasons->count(),
            'reasons' => $reasons,
        ]);
    }
}
