<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Find the most specific applicable commission rule.
     *
     * Priority: category-specific → membership-tier-specific → global
     *
     * Since commission_rules live in the admin DB, the frontend reads them via
     * the sellupnow_admin connection, or falls back to the escrow_commission_percent
     * static_option stored in listocean_db.
     */
    public static function applicableRule(int $categoryId, ?int $membershipTier = null): object
    {
        // Try the admin commission_rules DB (if configured and reachable)
        try {
            $adminConnection = config('database.connections.sellupnow_admin') ? 'sellupnow_admin' : null;
            if ($adminConnection) {
                // Category-specific rule
                $rule = DB::connection($adminConnection)
                    ->table('commission_rules')
                    ->where('scope', 'category')
                    ->where('scope_id', $categoryId)
                    ->where('is_active', 1)
                    ->first();

                if ($rule) return $rule;

                // Membership-tier-specific rule
                if ($membershipTier !== null) {
                    $rule = DB::connection($adminConnection)
                        ->table('commission_rules')
                        ->where('scope', 'membership_tier')
                        ->where('scope_id', $membershipTier)
                        ->where('is_active', 1)
                        ->first();

                    if ($rule) return $rule;
                }

                // Global default
                $rule = DB::connection($adminConnection)
                    ->table('commission_rules')
                    ->where('scope', 'global')
                    ->where('is_active', 1)
                    ->orderBy('id')
                    ->first();

                if ($rule) return $rule;
            }
        } catch (\Throwable $e) {
            Log::warning('CommissionService: could not read admin commission_rules — falling back to static_option.', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback: read from frontend static_options
        $percent = (float) (get_static_option('escrow_commission_percent') ?? 5);
        return (object) [
            'id'         => 0,
            'scope'      => 'global',
            'scope_id'   => null,
            'percentage' => $percent,
            'fixed'      => 0,
            'is_active'  => 1,
        ];
    }

    /**
     * Calculate the commission amount for a given listing price.
     */
    public static function calculate(float $listingPrice, int $categoryId, ?int $tier = null): float
    {
        $rule = self::applicableRule($categoryId, $tier);

        $commission = 0.0;

        if (!empty($rule->percentage) && $rule->percentage > 0) {
            $commission += round($listingPrice * ((float) $rule->percentage / 100), 2);
        }

        if (!empty($rule->fixed) && $rule->fixed > 0) {
            $commission += (float) $rule->fixed;
        }

        return max(0.0, $commission);
    }
}
