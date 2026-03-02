<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingDetailsResource;
use App\Http\Resources\ListingResource;
use App\Models\AuctionBid;
use App\Models\Listing;
use App\Services\AiRecommendationService;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    private function baseQuery(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $subCategoryId = $request->query('sub_category_id');
        $childCategoryId = $request->query('child_category_id');

        $userId = auth('api')->id();

        return Listing::query()
            ->with([
                'category',
                'favorites' => function ($query) use ($userId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->isActive()
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($categoryId, fn ($builder) => $builder->where('category_id', $categoryId))
            ->when($subCategoryId, fn ($builder) => $builder->where('sub_category_id', $subCategoryId))
            ->when($childCategoryId, fn ($builder) => $builder->where('child_category_id', $childCategoryId));
    }

    private function paginatedResponse(Request $request, $query, string $message = 'listings')
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 10), 1);
        $skip = ($page - 1) * $perPage;

        $total = $query->count();
        $listings = $query
            ->skip($skip)
            ->take($perPage)
            ->get();

        // Optional AI ranking for buyers (platform-wide ordering improvement)
        try {
            if (auth('api')->check()) {
                /** @var AiRecommendationService $ai */
                $ai = app(AiRecommendationService::class);
                if ($ai->isEnabled() && $listings->count() >= 2) {
                    $rows = $listings
                        ->map(fn (Listing $l) => $l->only(['id', 'title', 'sub_title', 'price', 'city', 'state', 'country']))
                        ->all();

                    $rankedIds = $ai->rankListingIdsForUser($rows, $request, 'listing_feed');
                    if (! empty($rankedIds)) {
                        $byId = $listings->keyBy('id');
                        $reordered = collect();
                        foreach ($rankedIds as $id) {
                            if ($byId->has($id)) {
                                $reordered->push($byId->get($id));
                            }
                        }
                        if ($reordered->count() === $listings->count()) {
                            $listings = $reordered;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fail open: keep normal ordering
        }

        return $this->json($message, [
            'total' => $total,
            'products' => ListingResource::collection($listings),
            'listings' => ListingResource::collection($listings),
            'filters' => [
                'brands' => [],
                'min_price' => (int) floor((float) ($listings->min('price') ?? 0)),
                'max_price' => (int) ceil((float) ($listings->max('price') ?? 0)),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = $this->baseQuery($request)->latest('id');

        return $this->paginatedResponse($request, $query, 'listings');
    }

    public function categoryWise(Request $request)
    {
        $query = $this->baseQuery($request)->latest('id');

        return $this->paginatedResponse($request, $query, 'category wise listings');
    }

    public function popular(Request $request)
    {
        $query = $this->baseQuery($request)->orderByDesc('view')->orderByDesc('id');

        return $this->paginatedResponse($request, $query, 'popular listings');
    }

    public function mostLiked(Request $request)
    {
        $query = $this->baseQuery($request)->orderByDesc('favorites_count')->orderByDesc('id');

        return $this->paginatedResponse($request, $query, 'most liked listings');
    }

    public function show(Request $request)
    {
        $listingId = $request->query('listing_id') ?? $request->query('product_id');
        $slug = $request->query('slug');

        if (! $listingId && ! $slug) {
            return $this->json('The listing id or slug field is required.', [
                'errors' => [
                    'listing_id' => ['The listing id field is required when slug is not present.'],
                ],
            ], 422);
        }

        $listing = Listing::query()
            ->with([
                'category',
                'subCategory',
                'childCategory',
                'user',
                'favorites' => function ($query) {
                    $userId = auth('api')->id();
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->isActive()
            ->when($listingId, fn ($builder) => $builder->where('id', $listingId))
            ->when($slug, fn ($builder) => $builder->where('slug', $slug))
            ->first();

        if (! $listing) {
            return $this->json('The selected listing id is invalid.', [
                'errors' => [
                    'listing_id' => ['The selected listing id is invalid.'],
                ],
            ], 422);
        }

        $listing->increment('view');

        return $this->json('listing details', [
            'product' => ListingDetailsResource::make($listing),
            'listing' => ListingDetailsResource::make($listing),
            'related_products' => [],
            'popular_products' => [],
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'negotiable'      => 'nullable|boolean',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'lat'             => 'nullable|numeric',
            'lon'             => 'nullable|numeric',
            'category_id'     => 'nullable|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:categories,id',
            'child_category_id' => 'nullable|integer|exists:categories,id',
            'country_id'      => 'nullable|integer|exists:countries,id',
            'image'           => 'nullable|file|image|max:5120',
        ]);

        $userId = auth('api')->id();
        $slug   = Str::slug($data['title']) . '-' . Str::random(6);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('listings', 'public');
        }

        $listing = Listing::create([
            'user_id'          => $userId,
            'title'            => $data['title'],
            'slug'             => $slug,
            'description'      => $data['description'] ?? null,
            'price'            => $data['price'] ?? 0,
            'negotiable'       => $data['negotiable'] ?? false,
            'phone'            => $data['phone'] ?? null,
            'address'          => $data['address'] ?? null,
            'lat'              => $data['lat'] ?? null,
            'lon'              => $data['lon'] ?? null,
            'category_id'      => $data['category_id'] ?? null,
            'sub_category_id'  => $data['sub_category_id'] ?? null,
            'child_category_id' => $data['child_category_id'] ?? null,
            'country_id'       => $data['country_id'] ?? null,
            'image'            => $imagePath,
            'status'           => true,
            'is_published'     => true,
            'published_at'     => now(),
        ]);

        return $this->json('listing created', [
            'product' => ListingResource::make($listing->load('category')),
            'listing' => ListingResource::make($listing->load('category')),
        ], 201);
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request)
    {
        $listingId = $request->input('listing_id') ?? $request->input('product_id');
        $listing = Listing::where('user_id', auth('api')->id())->findOrFail($listingId);

        $data = $request->validate([
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'negotiable'      => 'nullable|boolean',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'lat'             => 'nullable|numeric',
            'lon'             => 'nullable|numeric',
            'category_id'     => 'nullable|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:categories,id',
            'child_category_id' => 'nullable|integer|exists:categories,id',
            'country_id'      => 'nullable|integer|exists:countries,id',
            'image'           => 'nullable|file|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($listing->image) {
                Storage::disk('public')->delete($listing->image);
            }
            $data['image'] = $request->file('image')->store('listings', 'public');
        }

        if (!empty($data['title']) && $data['title'] !== $listing->title) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        }

        $listing->update(array_filter($data, fn($v) => $v !== null));

        return $this->json('listing updated', [
            'product' => ListingResource::make($listing->fresh()->load('category')),
            'listing' => ListingResource::make($listing->fresh()->load('category')),
        ]);
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $listingId = $request->input('listing_id') ?? $request->input('product_id');
        $listing = Listing::where('user_id', auth('api')->id())->findOrFail($listingId);
        $listing->delete();

        return $this->json('listing deleted');
    }

    // ── Related by category ───────────────────────────────────────────────────
    public function relatedByCategory(Request $request)
    {
        $listingId  = $request->query('listing_id') ?? $request->query('product_id');
        $categoryId = $request->query('category_id');

        if (!$categoryId && $listingId) {
            $categoryId = Listing::find($listingId)?->category_id;
        }

        $query = $this->baseQuery($request)
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($listingId, fn ($q) => $q->where('id', '!=', $listingId))
            ->latest('id');

        return $this->paginatedResponse($request, $query, 'related listings');
    }

    // ── Auction listings ──────────────────────────────────────────────────────
    public function auctionListings(Request $request)
    {
        $userId = auth('api')->id();

        // Listings with at least one bid (proxy for "auction" listings)
        $query = Listing::query()
            ->with([
                'category',
                'favorites' => function ($q) use ($userId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->whereHas('auctionBids')
            ->isActive()
            ->latest('id');

        return $this->paginatedResponse($request, $query, 'auction listings');
    }

    // ── All listings of a specific seller ─────────────────────────────────────
    public function sellerListings(Request $request)
    {
        $sellerId = $request->query('seller_id') ?? $request->query('user_id');
        $userId   = auth('api')->id();

        $query = Listing::query()
            ->with([
                'category',
                'favorites' => function ($q) use ($userId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->isActive()
            ->when($sellerId, fn ($q) => $q->where('user_id', $sellerId))
            ->latest('id');

        return $this->paginatedResponse($request, $query, 'seller listings');
    }

    // ── Basic listing info for the authenticated seller (for video upload) ────
    public function sellerProductsBasicInfo(Request $request)
    {
        $userId   = auth('api')->id();
        $listings = Listing::query()
            ->where('user_id', $userId)
            ->isActive()
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'image', 'price']);

        return $this->json('seller products basic info', [
            'products' => $listings,
            'listings' => $listings,
        ]);
    }

    // ── Promote ads ───────────────────────────────────────────────────────────
    public function promoteAds(Request $request)
    {
        $data = $request->validate([
            'adIds'      => 'required|string',  // comma-separated listing IDs
            'package_id' => 'nullable|integer|exists:featured_ad_packages,id',
            'days'       => 'nullable|integer|min:1|max:365',
        ]);

        $userId = auth('api')->id();
        $adIds  = array_filter(
            array_map('intval', explode(',', $data['adIds'])),
            fn ($id) => $id > 0
        );

        if (empty($adIds)) {
            return $this->json('No valid ad IDs provided', [], 422);
        }

        // Verify all ads belong to the authenticated user
        $owned = Listing::where('user_id', $userId)
            ->whereIn('id', $adIds)
            ->pluck('id')
            ->toArray();

        $notOwned = array_values(array_diff($adIds, $owned));
        if (! empty($notOwned)) {
            return $this->json('Some ads do not belong to you', ['invalid_ids' => $notOwned], 403);
        }

        $days  = $data['days'] ?? 7;
        $until = now()->addDays($days);

        Listing::whereIn('id', $owned)->update([
            'is_featured'        => true,
            'featured_until'     => $until,
            'featured_package_id'=> $data['package_id'] ?? null,
        ]);

        return $this->json('Ads promoted successfully', [
            'promoted_ids'   => array_values($owned),
            'featured_until' => $until->toDateTimeString(),
        ]);
    }

    // ── Place bid ─────────────────────────────────────────────────────────────
    public function placeBid(Request $request)
    {
        $data = $request->validate([
            'listing_id' => 'required|integer|exists:listings,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $userId    = auth('api')->id();
        $listingId = $data['listing_id'];
        $amount    = (float) $data['amount'];

        // Check that the bid is higher than the current highest bid
        $highestBid = AuctionBid::where('listing_id', $listingId)
            ->where('status', 'active')
            ->max('amount');

        if ($highestBid && $amount <= $highestBid) {
            return $this->json(
                'Your bid must be higher than the current highest bid of ' . $highestBid,
                [],
                422
            );
        }

        // Mark all previous bids for this listing as outbid
        AuctionBid::where('listing_id', $listingId)
            ->where('status', 'active')
            ->update(['status' => 'outbid']);

        $bid = AuctionBid::create([
            'listing_id' => $listingId,
            'user_id'    => $userId,
            'amount'     => $amount,
            'status'     => 'active',
        ]);

        // Notify listing owner about new bid (skip if owner is the bidder)
        try {
            $listing = Listing::find($listingId);
            if ($listing && $listing->user_id !== $userId) {
                $bidder = auth('api')->user();
                PushNotificationService::sendToUsers(
                    $listing->user_id,
                    'New bid on your listing',
                    ($bidder->name ?? 'Someone') . ' placed a bid of ' . number_format($amount, 2),
                    ['type' => 'new_bid', 'listing_id' => (string) $listingId]
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->json('Bid placed successfully', [
            'bid_id'     => $bid->id,
            'listing_id' => $listingId,
            'amount'     => $bid->amount,
            'status'     => $bid->status,
        ]);
    }
}
