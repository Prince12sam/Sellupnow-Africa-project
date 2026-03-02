<?php

namespace App\Services;

use App\Models\CommissionRule;
use App\Models\FinancialAudit;

class CommissionService
{
    /**
     * Compute commission for a given amount and context.
     * Context may include category_id, shop_id, etc.
     */
    public static function calculate(float $amount, array $context = []): array
    {
        // naive: find most specific active rule: shop -> category -> global
        $rule = null;

        if (!empty($context['shop_id'])) {
            $rule = CommissionRule::where('scope', 'shop')->where('scope_id', $context['shop_id'])->where('is_active', 1)->first();
        }

        if (!$rule && !empty($context['category_id'])) {
            $rule = CommissionRule::where('scope', 'category')->where('scope_id', $context['category_id'])->where('is_active', 1)->first();
        }

        if (!$rule) {
            $rule = CommissionRule::where('scope', 'global')->where('is_active', 1)->first();
        }

        $percentage = $rule->percentage ?? 0;
        $fixed = $rule->fixed ?? 0;

        $commission = round(($percentage / 100.0) * $amount + $fixed, 2);

        return [
            'commission' => $commission,
            'rule_id' => $rule->id ?? null,
            'percentage' => $percentage,
            'fixed' => $fixed,
        ];
    }

    /**
     * Record a financial audit entry for an action.
     */
    public static function audit(string $action, float $amount, array $meta = [], ?int $createdBy = null): FinancialAudit
    {
        return FinancialAudit::create([
            'related_type' => $meta['related_type'] ?? null,
            'related_id' => $meta['related_id'] ?? null,
            'action' => $action,
            'amount' => $amount,
            'metadata' => $meta,
            'created_by' => $createdBy,
        ]);
    }
}
