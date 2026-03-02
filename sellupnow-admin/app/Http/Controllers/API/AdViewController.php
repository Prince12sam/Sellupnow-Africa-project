<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdViewController extends Controller
{
    /**
     * Increment the view count for a listing.
     */
    public function record(Request $request)
    {
        $data = $request->validate([
            'listing_id' => 'required_without:product_id|nullable|integer',
            'product_id' => 'required_without:listing_id|nullable|integer',
        ]);

        $listingId = $data['listing_id'] ?? $data['product_id'];

        $listing = Listing::query()->find($listingId);
        if ($listing) {
            // Dedupe: per listing + fingerprint (user id if logged in, otherwise IP+UA hash).
            $fingerprint = auth()->check() ? 'u:'.auth()->id() : 'ip:'.request()->ip().':'.substr(sha1(request()->header('User-Agent', '')), 0, 10);
            $cacheKey = "listing_viewed:{$listingId}:{$fingerprint}";
            $ttl = 60 * 60; // 1 hour dedupe window

            if (! Cache::has($cacheKey)) {
                try {
                    $listing->increment('view');
                    Cache::put($cacheKey, 1, $ttl);
                } catch (\Throwable $e) {
                    // ignore DB errors
                }
            }
        }

        return $this->json('ad view recorded', [
            'listing_id' => (int) $listingId,
            'views'      => $listing?->view ?? 0,
        ]);
    }

    /**
     * Return the view count for a listing.
     */
    public function getViews(Request $request)
    {
        $listingId = $request->query('listing_id') ?? $request->query('product_id');

        $listing = Listing::query()->find($listingId);

        return $this->json('ad views', [
            'listing_id' => (int) $listingId,
            'views'      => $listing?->view ?? 0,
        ]);
    }
}
