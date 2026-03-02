<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ListoceanFeaturedAdReportController extends Controller
{
    private const CONNECTION = 'listocean';

    public function purchases(): View
    {
        $hasTable = Schema::connection(self::CONNECTION)->hasTable('featured_ad_purchases');

        $purchases = collect();
        if ($hasTable) {
            $purchases = DB::connection(self::CONNECTION)
                ->table('featured_ad_purchases')
                ->orderByDesc('id')
                ->limit(200)
                ->get();
        }

        return view('admin.listocean-featured-ad-reports.purchases', compact('hasTable', 'purchases'));
    }

    public function activations(): View
    {
        $hasTable = Schema::connection(self::CONNECTION)->hasTable('featured_ad_activations');

        $activations = collect();
        if ($hasTable) {
            $activations = DB::connection(self::CONNECTION)
                ->table('featured_ad_activations')
                ->orderByDesc('id')
                ->limit(300)
                ->get();
        }

        return view('admin.listocean-featured-ad-reports.activations', compact('hasTable', 'activations'));
    }
}
