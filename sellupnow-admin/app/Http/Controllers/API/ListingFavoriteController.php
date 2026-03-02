<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Models\ListingFavorite;
use Illuminate\Http\Request;

class ListingFavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'listing_id' => 'required_without:product_id|nullable|integer|exists:listings,id',
            'product_id' => 'required_without:listing_id|nullable|integer|exists:listings,id',
        ]);

        $listingId = (int) ($data['listing_id'] ?? $data['product_id']);
        $userId = auth('api')->id();

        $favorite = ListingFavorite::query()
            ->where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
        } else {
            ListingFavorite::create([
                'user_id' => $userId,
                'listing_id' => $listingId,
            ]);
            $isFavorite = true;
        }

        $listing = Listing::query()
            ->with([
                'category',
                'favorites' => fn ($query) => $query->where('user_id', $userId),
            ])
            ->withCount('favorites')
            ->findOrFail($listingId);

        return $this->json('favorite updated successfully', [
            'product' => ListingResource::make($listing),
            'listing' => ListingResource::make($listing),
            'is_favorite' => $isFavorite,
        ]);
    }

    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 10), 1);
        $skip = ($page - 1) * $perPage;

        $userId = auth('api')->id();

        $query = Listing::query()
            ->with([
                'category',
                'favorites' => fn ($favoriteQuery) => $favoriteQuery->where('user_id', $userId),
            ])
            ->withCount('favorites')
            ->whereHas('favorites', fn ($favoriteQuery) => $favoriteQuery->where('user_id', $userId))
            ->isActive()
            ->latest('id');

        $total = $query->count();
        $listings = $query->skip($skip)->take($perPage)->get();

        return $this->json('favorite products', [
            'total' => $total,
            'products' => ListingResource::collection($listings),
            'listings' => ListingResource::collection($listings),
        ]);
    }

    public function likesForAd(Request $request)
    {
        $listingId = $request->query('listing_id') ?? $request->query('product_id');
        $userId = auth('api')->id();

        $count = ListingFavorite::where('listing_id', $listingId)->count();
        $isLiked = $userId
            ? ListingFavorite::where('listing_id', $listingId)->where('user_id', $userId)->exists()
            : false;

        return $this->json('ad likes', [
            'total'    => $count,
            'is_liked' => $isLiked,
        ]);
    }
}
