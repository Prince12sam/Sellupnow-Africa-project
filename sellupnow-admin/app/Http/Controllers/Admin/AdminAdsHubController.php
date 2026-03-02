<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Banner;
use App\Models\FlashSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AdminAdsHubController extends Controller
{
    private const LO = 'listocean';

    private function lo()
    {
        return DB::connection(self::LO);
    }

    private function loTable(string $table)
    {
        if (! Schema::connection(self::LO)->hasTable($table)) {
            return null;
        }
        return $this->lo()->table($table);
    }

    public function index()
    {
        // ── SellUpNow native ──────────────────────────────────────────────
        $flashSaleTotal    = FlashSale::count();
        $flashSaleActive   = FlashSale::where('status', 1)->count();

        $nativeAdTotal     = Ad::count();
        $nativeAdActive    = Ad::where('status', 1)->count();

        $bannerTotal       = Banner::count();
        $bannerActive      = Banner::where('status', 1)->count();

        // Default values (safe fallbacks if Listocean DB/tables are missing)
        $siteAdTotal = $siteAdActive = $bannerReqTotal = $bannerReqPending = $bannerReqActive = 0;
        $totalImpressions = $totalClicks = $promoVideoTotal = $promoVideoActive = $totalVideoViews = 0;
        $reelTotal = $reelActive = 0;
        $featuredPackages = $featuredPurchases = $featuredActivations = 0;
        $pendingActions = 0;

        try {
            // ── ListOcean: advertisements table ──────────────────────────────
            if ($this->loTable('advertisements')) {
                $hasUserId = Schema::connection(self::LO)->hasColumn('advertisements', 'user_id');

                if ($hasUserId) {
                    $siteAdTotal    = $this->lo()->table('advertisements')->whereNull('user_id')->count();
                    $siteAdActive   = $this->lo()->table('advertisements')->whereNull('user_id')->where('status', 1)->count();

                    // User-submitted banner ad requests: user_id IS NOT NULL
                    $bannerReqTotal   = $this->lo()->table('advertisements')->whereNotNull('user_id')->count();
                    $bannerReqPending = $this->lo()->table('advertisements')->whereNotNull('user_id')->where('status', 0)->count();
                    $bannerReqActive  = $this->lo()->table('advertisements')->whereNotNull('user_id')->where('status', 1)->count();
                } else {
                    $siteAdTotal    = $this->lo()->table('advertisements')->count();
                    $siteAdActive   = $this->lo()->table('advertisements')->where('status', 1)->count();
                    $bannerReqTotal = $bannerReqPending = $bannerReqActive = 0;
                }

                // Total impressions & clicks across all ads
                $totalImpressions = (int) $this->lo()->table('advertisements')->sum('impression');
                $totalClicks      = (int) $this->lo()->table('advertisements')->sum('click');
            }

            // ── ListOcean: ad_videos / promo video ads ────────────────────────
            $promoVideoTotal   = ($q = $this->loTable('ad_videos')) ? $q->count() : 0;
            $promoVideoActive  = ($q = $this->loTable('ad_videos')) ? $this->lo()->table('ad_videos')->where('is_approved', 1)->count() : 0;
            $totalVideoViews   = ($q = $this->loTable('ad_videos')) ? (int) $this->lo()->table('ad_videos')->sum('views') : 0;

            // ── ListOcean: reel_ad_placements ─────────────────────────────────
            $reelTotal  = ($q = $this->loTable('reel_ad_placements')) ? $q->count() : 0;
            $reelActive = ($q = $this->loTable('reel_ad_placements')) ? $this->lo()->table('reel_ad_placements')->where('status', 1)->count() : 0;

            // ── ListOcean: featured ads ───────────────────────────────────────
            $featuredPackages   = ($q = $this->loTable('featured_ad_packages'))   ? $q->count() : 0;
            $featuredPurchases  = ($q = $this->loTable('featured_ad_purchases'))  ? $q->count() : 0;
            $featuredActivations = ($q = $this->loTable('featured_ad_activations')) ? $q->count() : 0;

            // ── Pending actions count (badge for menu) ────────────────────────
            $pendingActions = $bannerReqPending;
        } catch (\Throwable $e) {
            Log::error('AdminAdsHubController Listocean summary failed', ['error' => $e->getMessage()]);
            // keep safe defaults
        }

        return view('admin.ads-hub.index', compact(
            // Flash Sales
            'flashSaleTotal', 'flashSaleActive',
            // SellUpNow native ads & banners
            'nativeAdTotal', 'nativeAdActive',
            'bannerTotal', 'bannerActive',
            // Site Advertisements
            'siteAdTotal', 'siteAdActive',
            // Banner Ad Requests
            'bannerReqTotal', 'bannerReqPending', 'bannerReqActive',
            // Engagement
            'totalImpressions', 'totalClicks', 'totalVideoViews',
            // Promo Videos
            'promoVideoTotal', 'promoVideoActive',
            // Reel Ad Placements
            'reelTotal', 'reelActive',
            // Featured Ads
            'featuredPackages', 'featuredPurchases', 'featuredActivations',
            // Summary
            'pendingActions'
        ));
    }
}
