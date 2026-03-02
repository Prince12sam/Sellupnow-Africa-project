<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $generaleSetting = generaleSetting('setting');

        // User stats
        $totalUsers           = User::count();

        // Safe query helper for Listocean connection — returns 0 on error (missing table, etc.)
        $safeCount = function (string $table, ?callable $modify = null) {
            try {
                $q = DB::connection('listocean')->table($table);
                if ($modify) $q = $modify($q);
                return $q->count();
            } catch (\Throwable $e) {
                return 0;
            }
        };

        // Listing stats (from Listocean DB)
        $totalListings        = $safeCount('listings');
        $activeListings       = $safeCount('listings', fn($q) => $q->where('status', 1)->where('is_published', 1));
        $featuredListings     = $safeCount('listings', fn($q) => $q->where('is_featured', 1));
        $pendingListings      = $safeCount('listings', fn($q) => $q->where('is_published', 0));

        // Content stats (from Listocean DB)
        $totalCategories      = $safeCount('categories');
        $activeAuctionBids    = $safeCount('auction_bids', fn($q) => $q->where('status', 'active'));

        // Action-required stats (from Listocean DB)
        $pendingReports       = $safeCount('listing_reports', fn($q) => $q->where('status', 'pending'));
        $pendingVerifications = $safeCount('identity_verifications', fn($q) => $q->where('status', 0));
        $totalVerifications = $safeCount('identity_verifications');

        // Wallet / financial
        $pendingWithdraw  = Withdraw::where('status', 'pending')->sum('amount');
        $alreadyWithdraw  = Withdraw::where('status', 'approved')->sum('amount');
        $deniedWithdraw   = Withdraw::where('status', 'denied')->sum('amount');
        $totalCommission  = Transaction::where('is_commission', true)->sum('amount');

        // Recent listings (raw queries against listocean to avoid cross-connection ORM issues)
        $lo = DB::connection('listocean');

        $recentListings = collect();
        try {
            $recentListings = $lo->table('listings as l')
                ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
                ->leftJoin('categories as c', 'c.id', '=', 'l.category_id')
                ->select('l.id', 'l.title', 'l.price', 'l.created_at', 'l.is_published', 'l.status',
                         'u.name as _user_name', 'c.name as _category_name')
                ->orderByDesc('l.id')
                ->limit(8)
                ->get()
                ->map(function ($r) {
                    $r->user      = (object)['name' => $r->_user_name];
                    $r->category  = (object)['name' => $r->_category_name];
                    $r->thumbnail = asset('default/default.jpg');
                    try { $r->created_at = \Carbon\Carbon::parse($r->created_at); } catch (\Throwable $e) {}
                    return $r;
                });
        } catch (\Throwable $e) {}

        // Most favourited listings
        $topFavoritedListings = collect();
        try {
            $topFavoritedListings = $lo->table('listings as l')
                ->leftJoin('listing_favorites as lf', 'lf.listing_id', '=', 'l.id')
                ->select('l.id', 'l.title', 'l.price', $lo->raw('COUNT(lf.id) as favorites_count'))
                ->groupBy('l.id', 'l.title', 'l.price')
                ->orderByDesc('favorites_count')
                ->limit(8)
                ->get()
                ->map(function ($r) {
                    $r->thumbnail = asset('default/default.jpg');
                    return $r;
                });
        } catch (\Throwable $e) {}

        // Recent reports
        $recentReports = collect();
        try {
            $recentReports = $lo->table('listing_reports as lr')
                ->leftJoin('listings as l', 'l.id', '=', 'lr.listing_id')
                ->leftJoin('users as u', 'u.id', '=', 'lr.user_id')
                ->select('lr.id', 'lr.status',
                         'l.title as _listing_title',
                         'u.name as _user_name', 'u.avatar as _user_avatar')
                ->orderByDesc('lr.id')
                ->limit(6)
                ->get()
                ->map(function ($r) {
                    $r->listing = (object)['title' => $r->_listing_title, 'name' => $r->_listing_title];
                    $r->user    = (object)[
                        'name'   => $r->_user_name,
                        'avatar' => $r->_user_avatar ?? asset('default/default.jpg'),
                    ];
                    return $r;
                });
        } catch (\Throwable $e) {}

        return view('admin.dashboard', compact(
            'totalUsers', 'totalListings', 'activeListings', 'featuredListings',
            'pendingListings', 'pendingReports', 'pendingVerifications', 'totalVerifications',
            'totalCategories', 'activeAuctionBids',
            'pendingWithdraw', 'alreadyWithdraw', 'deniedWithdraw', 'totalCommission',
            'recentListings', 'topFavoritedListings', 'recentReports',
            'generaleSetting'
        ));
    }

    public function listingStatistics()
    {
        $type = request('type');
        $date = request('date');

        $safeListingCount = function (?callable $modify = null): int {
            try {
                $query = DB::connection('listocean')->table('listings');
                if ($modify) {
                    $query = $modify($query);
                }
                return (int) $query->count();
            } catch (\Throwable $th) {
                return 0;
            }
        };

        if ($type == 'daily') {
            if ($date) {
                $count = $safeListingCount(fn($q) => $q->whereDate('created_at', $date));
                return $this->json('single day listing statistics', [
                    'labels' => [\Carbon\Carbon::parse($date)->format('l')],
                    'values' => [$count],
                    'total'  => $count,
                ]);
            }
            $startDate = now()->startOfWeek();
            $endDate   = now()->endOfWeek();
            $rows = [];
            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                $rows[] = [
                    'label' => $d->format('l'),
                    'value' => $safeListingCount(fn($q) => $q->whereDate('created_at', $d->toDateString())),
                ];
            }
            return $this->json('daily listing statistics', [
                'labels' => array_column($rows, 'label'),
                'values' => array_column($rows, 'value'),
                'total'  => array_sum(array_column($rows, 'value')),
            ]);
        } elseif ($type == 'monthly') {
            $rows = [];
            for ($d = now()->startOfYear()->copy(); $d->lte(now()->endOfYear()); $d->addMonth()) {
                $rows[] = [
                    'label' => $d->format('M'),
                    'value' => $safeListingCount(fn($q) => $q->whereMonth('created_at', $d->month)->whereYear('created_at', $d->year)),
                ];
            }
            return $this->json('monthly listing statistics', [
                'labels' => array_column($rows, 'label'),
                'values' => array_column($rows, 'value'),
                'total'  => array_sum(array_column($rows, 'value')),
            ]);
        } else {
            $rows = [];
            for ($d = now()->copy(); $d->year >= now()->subYears(6)->year; $d->subYear()) {
                $rows[] = [
                    'label' => $d->format('Y'),
                    'value' => $safeListingCount(fn($q) => $q->whereYear('created_at', $d->year)),
                ];
            }
            return $this->json('yearly listing statistics', [
                'labels' => array_column($rows, 'label'),
                'values' => array_column($rows, 'value'),
                'total'  => array_sum(array_column($rows, 'value')),
            ]);
        }
    }

    // Kept for backward compatibility
    public function orderStatistics()
    {
        return $this->json('not applicable', ['labels' => [], 'values' => [], 'total' => 0]);
    }
}
