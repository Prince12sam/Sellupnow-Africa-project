<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\CommissionService;

class ShadowCommissionReport extends Command
{
    protected $signature = 'shadow:commission {--limit=200} {--audit : write shadow audits into financial_audits}';
    protected $description = 'Run a shadow commission calculation for recent delivered+paid orders';

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $writeAudit = (bool) $this->option('audit');

        $this->info("Running shadow commission report for up to {$limit} orders...");

        $orders = Order::query()
            ->where('order_status', 'delivered')
            ->where('payment_status', 'paid')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $rows = [];
        foreach ($orders as $order) {
            $calc = CommissionService::calculate((float) $order->total_amount, ['shop_id' => $order->shop_id]);
            $stored = (float) ($order->admin_commission ?? 0);
            $calculated = (float) ($calc['commission'] ?? 0);
            $delta = round($calculated - $stored, 2);

            $rows[] = [
                'order_id' => $order->id,
                'order_code' => $order->prefix . $order->order_code,
                'stored_commission' => $stored,
                'calculated_commission' => $calculated,
                'delta' => $delta,
            ];

            if ($writeAudit) {
                CommissionService::audit('commission_shadow_report', $calculated, [
                    'order_id' => $order->id,
                    'stored' => $stored,
                    'calc' => $calc,
                ], null);
            }
        }

        $this->table(['Order ID','Order Code','Stored','Calculated','Delta'], $rows);
        $this->info('Done.');

        return 0;
    }
}
