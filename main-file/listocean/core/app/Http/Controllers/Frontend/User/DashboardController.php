<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Backend\Listing;
use App\Models\Frontend\Boost;
use App\Models\Frontend\ListingFavorite;
use App\Models\Frontend\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user_id = Auth::guard('web')->user()->id;
        $user = User::with('listings', 'reviews', 'user_country','user_state')->findOrFail($user_id);

        // listings
        $user_ads_posted       = $user->listings->count();
        $user_active_listings  = $user->listings->where('is_published', 1)->where('status', 1)->count();
        $user_deactivated_ads  = $user->listings->where('is_published', 0)->count(); // user-deactivated (regardless of admin status)
        $user_favorite_ads     = ListingFavorite::where('user_id', $user_id)->count();

        // Ratings
        $averageRating = $user->reviews?->avg('rating');
        $user_review_count = $user->reviews?->count();

        // user given reviews
        $user_given_reviews = Review::where('reviewer_id', $user_id)->take(500)->get();

        return view('frontend.user.dashboard.dashboard', [
            'user' => $user,
            'user_ads_posted' => $user_ads_posted,
            'user_active_listings' => $user_active_listings,
            'user_deactivated_ads' => $user_deactivated_ads,
            'user_favorite_ads' => $user_favorite_ads,
            'averageRating' => $averageRating,
            'user_review_count' => $user_review_count,
            'user_given_reviews' => $user_given_reviews,
        ]);
    }

    // ── Seller Analytics ─────────────────────────────────────────────────────
    public function analytics()
    {
        $user_id = Auth::guard('web')->user()->id;
        $user    = User::findOrFail($user_id);

        // All listings with favorites count aggregated
        $listings = Listing::where('user_id', $user_id)
            ->withCount('listingFavorites')
            ->orderByDesc('created_at')
            ->get();

        // Active boosts keyed by listing_id for fast lookup
        $activeBoostedIds = Boost::where('user_id', $user_id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->pluck('listing_id')
            ->flip();

        // Summary totals
        $totalViews     = $listings->sum('view');
        $totalFavorites = $listings->sum('listing_favorites_count');
        $totalListings  = $listings->count();
        $featuredCount  = $listings->where('is_featured', 1)->count();

        return view('frontend.user.dashboard.analytics', compact(
            'user',
            'listings',
            'activeBoostedIds',
            'totalViews',
            'totalFavorites',
            'totalListings',
            'featuredCount'
        ));
    }
}

