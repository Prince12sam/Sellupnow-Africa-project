<?php

namespace App\Http\Resources;

use App\Models\MediaUpload;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingDetailsResource extends JsonResource
{
    private function parseAttributesJson($raw): array
    {
        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? ''));
            $value = trim((string) ($item['value'] ?? ''));
            if ($name === '' || $value === '') {
                continue;
            }

            $result[] = [
                'name' => $name,
                'value' => $value,
                'image' => null,
            ];
        }

        return $result;
    }

    public function toArray(Request $request): array
    {
        $currencyRate = (float) ($request->currencyData['rate'] ?? 1);
        $price = (float) $this->price * $currencyRate;
        $isFavorite = $this->relationLoaded('favorites') ? $this->favorites->isNotEmpty() : false;
        $user = $this->relationLoaded('user') ? $this->user : null;
        $category = $this->relationLoaded('category') ? $this->category : null;
        $subCategory = $this->relationLoaded('subCategory') ? $this->subCategory : null;
        $childCategory = $this->relationLoaded('childCategory') ? $this->childCategory : null;

        $categoryHierarchy = [];
        foreach ([$category, $subCategory, $childCategory] as $catNode) {
            if ($catNode) {
                $categoryHierarchy[] = [
                    '_id' => (string) $catNode->id,
                    'name' => $catNode->name,
                    'image' => null,
                ];
            }
        }

        $attributes = $this->parseAttributesJson($this->attributes_json ?? null);
        if (empty($attributes)) {
            if (! empty($this->condition)) {
                $attributes[] = ['name' => 'Condition', 'value' => (string) $this->condition, 'image' => null];
            }
            if (! empty($this->authenticity)) {
                $attributes[] = ['name' => 'Original', 'value' => (string) $this->authenticity, 'image' => null];
            }
        }

        // Build gallery images from gallery_images field (pipe-separated media_upload IDs)
        $gallery = [];
        if ($this->gallery_images) {
            $baseUrl = rtrim(config('app.url'), '/');
            // gallery_images stores pipe-separated IDs, e.g. "1181|1180|1179|1178"
            $ids = array_filter(array_map('intval', explode('|', $this->gallery_images)));
            if (!empty($ids)) {
                $mediaMap = MediaUpload::whereIn('id', $ids)->pluck('path', 'id');
                foreach ($ids as $id) {
                    if (isset($mediaMap[$id])) {
                        $gallery[] = $baseUrl . '/assets/uploads/media-uploader/' . $mediaMap[$id];
                    }
                }
            }
        }

        // If no gallery images were resolved, use the thumbnail as the sole gallery entry
        if (empty($gallery) && $this->thumbnail) {
            $gallery = [$this->thumbnail];
        }

        return [
            '_id'               => (string) $this->id,
            'seller'            => $user ? [
                '_id'           => (string) $user->id,
                'name'          => $user->name,
                'profileImage'  => $user->thumbnail,
                'phoneNumber'   => $user->phone,
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
            'attributes'        => $attributes,
            'status'            => (int) $this->status,
            'title'             => $this->title,
            'subTitle'          => null,
            'description'       => $this->description,
            'contactNumber'     => $this->phone,
            'availableUnits'    => 1,
            'primaryImage'      => $this->thumbnail,
            'galleryImages'     => $gallery,
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
            'escrowEnabled'     => (bool) $this->escrow_enabled,
            'categoryHierarchy' => $categoryHierarchy,
        ];
    }
}
