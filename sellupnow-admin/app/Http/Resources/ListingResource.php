<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currencyRate = (float) ($request->currencyData['rate'] ?? 1);
        $price = (float) $this->price * $currencyRate;
        $isFavorite = $this->relationLoaded('favorites') ? $this->favorites->isNotEmpty() : false;
        $user = $this->relationLoaded('user') ? $this->user : null;
        $category = $this->relationLoaded('category') ? $this->category : null;
        $categoryHierarchy = $category ? [[
            '_id' => (string) $category->id,
            'name' => $category->name,
            'image' => null,
        ]] : [];

        return [
            '_id'               => (string) $this->id,
            'seller'            => $user ? [
                '_id'           => (string) $user->id,
                'name'          => $user->name,
                'profileImage'  => $user->thumbnail,
                'phoneNumber'   => $user->phone,
                'email'         => $user->email,
                'isVerified'    => (bool) ($user->verified_status ?? false),
                'averageRating' => 0,
                'totalRating'   => 0,
                'registeredAt'  => optional($user->created_at)->toISOString(),
                'createdAt'     => optional($user->created_at)->toISOString(),
            ] : null,
            'purchasedPackage'  => null,
            'category'          => $category ? [
                '_id'  => (string) $category->id,
                'name' => $category->name,
                'image'=> null,
            ] : null,
            'attributes'        => [],
            'status'            => (function () {
                // Map the simple 0/1 DB columns to the semantic status codes
                // that the mobile app's My Ads screen expects.
                if ($this->is_featured && $this->status && $this->is_published) {
                    return 5; // Featured
                }
                if ($this->status && $this->is_published) {
                    return 2; // Approved / Live
                }
                if ($this->status && ! $this->is_published) {
                    return 1; // Pending / Under Review
                }
                return 6; // Deactivated
            })(),
            'title'             => $this->title,
            'subTitle'          => null,
            'description'       => $this->description,
            'contactNumber'     => is_numeric($this->phone) ? (int) $this->phone : null,
            'availableUnits'    => 1,
            'primaryImage'      => $this->thumbnail,
            'galleryImages'     => $this->thumbnail ? [$this->thumbnail] : [],
            'location'          => [
                'country'      => null,
                'state'        => null,
                'city'         => null,
                'latitude'     => $this->lat  ? (float) $this->lat  : null,
                'longitude'    => $this->lon  ? (float) $this->lon  : null,
                'fullAddress'  => $this->address,
            ],
            'saleType'          => (int) ($this->sale_type ?? 0),
            'isOfferAllowed'    => (bool) $this->negotiable,
            'minimumOffer'      => null,
            'price'             => (float) number_format($price, 2, '.', ''),
            'isAuctionEnabled'  => (bool) ($this->is_auction_enabled ?? false),
            'auctionStartingPrice' => $this->auction_starting_price ? (float) $this->auction_starting_price : null,
            'lastBidAmount'     => $this->relationLoaded('auctionBids')
                ? ($this->auctionBids->where('status', 'active')->sortByDesc('amount')->first()->amount ?? null)
                : (\App\Models\AuctionBid::where('listing_id', $this->id)->where('status', 'active')->max('amount') ?: null),
            'auctionDurationDays'  => $this->auction_duration_days,
            'auctionStartDate'  => optional($this->auction_start_date)->toISOString(),
            'auctionEndDate'    => optional($this->auction_end_date)->toISOString(),
            'scheduledPublishDate' => null,
            'isReservePriceEnabled'=> (bool) ($this->is_reserve_price_enabled ?? false),
            'reservePriceAmount'   => $this->reserve_price_amount ? (float) $this->reserve_price_amount : null,
            'isFake'            => false,
            'isOfferPlaced'     => false,
            'isActive'          => (bool) $this->status,
            'createdAt'         => optional($this->created_at)->toISOString(),
            'likesCount'        => (int) ($this->favorites_count ?? 0),
            'viewsCount'        => (int) $this->view,
            'isLike'            => $isFavorite,
            'isPlacedBid'       => (function () {
                $userId = auth('api')->id();
                if (! $userId) return false;
                return \App\Models\AuctionBid::where('listing_id', $this->id)->where('user_id', $userId)->exists();
            })(),
            'isViewed'          => false,
            'categoryHierarchy' => $categoryHierarchy,
        ];
    }
}
