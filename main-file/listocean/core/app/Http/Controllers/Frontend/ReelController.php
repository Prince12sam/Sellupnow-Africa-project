<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReelController extends Controller
{
    /**
     * Reels feed main page.
     */
    public function index(Request $request)
    {
        $page      = max(1, (int) $request->query('page', 1));
        $startId   = max(0, (int) $request->query('start', 0));      // listing-based start
        $avStartId = max(0, (int) $request->query('av_start', 0));   // user ad_video-based start
        $data      = $this->buildFeed($page, $startId, $avStartId);

        // AJAX / infinite-scroll request — return JSON
        if ($request->ajax() || $request->query('format') === 'json') {
            return response()->json($data);
        }

        return view('frontend.reels.index', [
            'reels'    => $data['reels'],
            'has_more' => $data['has_more'],
            'page'     => $page,
            'start_id' => $startId,
        ]);
    }

    /**
     * AJAX endpoint for loading more reels.
     */
    public function load(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $data = $this->buildFeed($page);

        return response()->json($data);
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function buildFeed(int $page, int $startId = 0, int $avStartId = 0): array
    {
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        // ── 1. Organic pool: listing-based reels ──────────────────────────────
        //    video_is_approved=1 ensures only admin-approved videos go public.
        $listingOrganics = DB::table('listings')
            ->where('video_url', '!=', '')
            ->whereNotNull('video_url')
            ->where('video_is_approved', 1)
            ->where('status', 1)
            ->where('is_published', 1)
            ->when($startId > 0, fn ($q) => $q->where('id', '!=', $startId))
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'slug', 'image', 'video_url', 'price', 'user_id', 'created_at'])
            ->map(fn ($r) => (object) (['_source' => 'listing'] + (array) $r));

        // ── 2. Organic pool: user-uploaded ad_videos (approved, not rejected) ─
        $userVideoOrganics = DB::table('ad_videos')
            ->whereNotNull('user_id')
            ->where('is_approved', 1)
            ->where('is_rejected', 0)
            ->when($avStartId > 0, fn ($q) => $q->where('id', '!=', $avStartId))
            ->orderByDesc('created_at')
            ->get(['id', 'user_id', 'listing_id', 'video_url', 'thumbnail_url', 'caption', 'created_at'])
            ->map(fn ($r) => (object) (['_source' => 'user_video'] + (array) $r));

        // Deduplicate user_video entries whose video_url already appears in the listing pool
        $listingVideoUrls = $listingOrganics->pluck('video_url')->filter()->flip();
        $userVideoOrganics = $userVideoOrganics->reject(
            fn ($r) => isset($listingVideoUrls[(string)($r->video_url ?? '')])
        );

        // ── 3. Merge, sort newest-first, paginate ────────────────────────────
        $merged = $listingOrganics
            ->concat($userVideoOrganics)
            ->sortByDesc('created_at')
            ->values();

        $has_more    = $merged->count() > ($offset + $perPage);
        $organicPool = $merged->slice($offset, $perPage)->values();

        // ── 4. Bulk-fetch linked listings for user_video items (avoid N+1) ───
        $linkedListingIds = $organicPool
            ->where('_source', 'user_video')
            ->pluck('listing_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $linkedListings = count($linkedListingIds)
            ? DB::table('listings')
                ->whereIn('id', $linkedListingIds)
                ->get(['id', 'title', 'slug', 'image', 'price'])
                ->keyBy('id')
            : collect();

        // ── 5. Bulk-fetch user rows for all organic items ────────────────────
        $allUserIds = $organicPool->pluck('user_id')->filter()->unique()->values()->all();
        $usersById  = count($allUserIds)
            ? DB::table('users')
                ->whereIn('id', $allUserIds)
                ->get(['id', 'first_name', 'last_name', 'username', 'image'])
                ->keyBy('id')
            : collect();

        // ── 6. Active promo video ad — random pick from approved pool ─────────
        // Exclude any ad_video whose video_url already appears in the organic
        // listing pool — those are listing-mirrored videos, not standalone promos,
        // and would produce a duplicate if injected at position 8.
        $listingUrlsForPromo = $listingOrganics->pluck('video_url')->filter()->unique()->values()->all();
        $promoAd = DB::table('ad_videos')
            ->where('is_approved', 1)
            ->where('is_rejected', 0)
            ->where(function ($q) {
                $q->whereNull('end_at')->orWhere('end_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->when(
                count($listingUrlsForPromo) > 0,
                fn ($q) => $q->whereNotIn('video_url', $listingUrlsForPromo)
            )
            ->inRandomOrder()
            ->first();

        // ── 7. Build overlay map from reel_ad_placements ──────────────────────
        $overlayMap = $this->buildOverlayMap();

        // ── 8. Compose the feed ───────────────────────────────────────────────
        $reels   = [];
        $poolIdx = 0;
        $feedPos = 1;

        // Prepend the requested user ad_video so the user lands on it first
        if ($avStartId > 0) {
            $avStart = DB::table('ad_videos')
                ->where('id', $avStartId)
                ->where('is_approved', 1)
                ->where('is_rejected', 0)
                ->first(['id', 'user_id', 'listing_id', 'video_url', 'thumbnail_url', 'caption']);

            if ($avStart) {
                $avUser    = $avStart->user_id ? ($usersById[$avStart->user_id] ?? DB::table('users')->where('id', $avStart->user_id)->first(['first_name','last_name','username','image'])) : null;
                $avLinked  = $avStart->listing_id ? ($linkedListings[$avStart->listing_id] ?? DB::table('listings')->where('id', $avStart->listing_id)->first(['id','title','slug','price'])) : null;
                $reels[] = [
                    'type'         => 'user_video',
                    'id'           => $avStart->id,
                    'title'        => $avStart->caption ?: ($avLinked->title ?? __('Video')),
                    'slug'         => $avLinked->slug ?? null,
                    'image'        => null,
                    'poster_url'   => $avStart->thumbnail_url ?: null,
                    'listing_url'  => $avLinked ? route('frontend.listing.details', $avLinked->slug) : null,
                    'video_url'    => $avStart->video_url,
                    'price'        => $avLinked->price ?? null,
                    'seller'       => $avUser ? (trim($avUser->first_name . ' ' . $avUser->last_name) ?: $avUser->username) : 'Unknown',
                    'seller_photo' => $avUser ? $avUser->image : null,
                    'sponsored'    => false,
                    'ad_overlays'  => [],
                ];
                $feedPos++;
            }
        }

        // Prepend the requested listing start video so the user lands on it first
        if ($startId > 0) {
            $startItem = DB::table('listings')
                ->where('id', $startId)
                ->where('video_url', '!=', '')
                ->whereNotNull('video_url')
                ->where('video_is_approved', 1)
                ->where('status', 1)
                ->where('is_published', 1)
                ->first(['id', 'title', 'slug', 'image', 'video_url', 'price', 'user_id']);

            if ($startItem) {
                $startUser = DB::table('users')->where('id', $startItem->user_id)
                    ->first(['first_name', 'last_name', 'username', 'image']);
                $reels[] = [
                    'type'         => 'listing',
                    'id'           => $startItem->id,
                    'title'        => $startItem->title,
                    'slug'         => $startItem->slug,
                    'image'        => $startItem->image,
                    'video_url'    => $startItem->video_url,
                    'price'        => $startItem->price,
                    'seller'       => $startUser ? (trim($startUser->first_name . ' ' . $startUser->last_name) ?: $startUser->username) : 'Unknown',
                    'seller_photo' => $startUser ? $startUser->image : null,
                    'sponsored'    => false,
                    'ad_overlays'  => $this->resolveOverlays($overlayMap, $startItem->id),
                ];
                $feedPos++;
            }
        }

        while ($poolIdx < $organicPool->count()) {
            // Every 8th position: inject promo video ad (if available)
            if ($promoAd && ($feedPos % 8 === 0)) {
                $reels[] = [
                    'type'        => 'ad_video',
                    'id'          => $promoAd->id,
                    'title'       => $promoAd->caption ?? 'Sponsored',
                    'video_url'   => $promoAd->video_url,
                    'thumbnail'   => $promoAd->thumbnail_url,
                    'cta_text'    => $promoAd->cta_text ?? 'Learn More',
                    'cta_url'     => $promoAd->cta_url  ?? '#',
                    'sponsored'   => true,
                    'ad_overlays' => $this->resolveOverlays($overlayMap, $promoAd->id),
                ];
                $feedPos++;
                continue;
            }

            $item = $organicPool[$poolIdx++];

            if ($item->_source === 'user_video') {
                // ── User-uploaded video reel ──────────────────────────────────
                $user   = isset($item->user_id) ? ($usersById[$item->user_id] ?? null) : null;
                $linked = $item->listing_id ? ($linkedListings[$item->listing_id] ?? null) : null;

                $reels[] = [
                    'type'         => 'user_video',
                    'id'           => $item->id,
                    'title'        => $item->caption ?: ($linked->title ?? __('Video')),
                    'slug'         => $linked->slug ?? null,
                    'image'        => null,
                    'poster_url'   => $item->thumbnail_url ?: null,
                    'listing_url'  => $linked ? route('frontend.listing.details', $linked->slug) : null,
                    'video_url'    => $item->video_url,
                    'price'        => $linked->price ?? null,
                    'seller'       => $user ? (trim($user->first_name . ' ' . $user->last_name) ?: $user->username) : 'Unknown',
                    'seller_photo' => $user ? $user->image : null,
                    'sponsored'    => false,
                    'ad_overlays'  => [],
                ];
            } else {
                // ── Standard listing reel ─────────────────────────────────────
                $user = isset($item->user_id) ? ($usersById[$item->user_id] ?? null) : null;

                $reels[] = [
                    'type'         => 'listing',
                    'id'           => $item->id,
                    'title'        => $item->title,
                    'slug'         => $item->slug,
                    'image'        => $item->image,
                    'video_url'    => $item->video_url,
                    'price'        => $item->price,
                    'seller'       => $user ? (trim($user->first_name . ' ' . $user->last_name) ?: $user->username) : 'Unknown',
                    'seller_photo' => $user ? $user->image : null,
                    'sponsored'    => false,
                    'ad_overlays'  => $this->resolveOverlays($overlayMap, $item->id),
                ];
            }

            $feedPos++;
        }

        return [
            'reels'    => $reels,
            'has_more' => $has_more,
            'page'     => $page,
        ];
    }

    /**
     * Build a map of reel_id => [ [...ad1...], [...ad2...], ... ]
     * from all currently active reel_ad_placements rows that target the feed
     * (placement = bottom_overlay or bottom_overlay_2).
     *
     * Multiple ads per reel are fully supported — they will rotate on-screen
     * every few seconds (Facebook-style). Simply create additional placement
     * rows for the same reel_id in the admin panel.
     */
    private function buildOverlayMap(): array
    {
        $placements = DB::table('reel_ad_placements')
            ->where('status', 1)
            ->whereIn('placement', ['bottom_overlay', 'bottom_overlay_2'])
            ->where('reel_type', 'listing')   // feed shows listing reels — don't mix ad_video IDs
            ->where(function ($q) {
                $q->whereNull('end_at')->orWhere('end_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->get();

        if ($placements->isEmpty()) {
            return [];
        }

        // Bulk-fetch all referenced advertisements in one query
        $adIds   = $placements->pluck('advertisement_id')->filter()->unique()->values()->all();
        $adsById = DB::table('advertisements')
            ->whereIn('id', $adIds)
            ->where('status', 1)
            ->get(['id', 'title', 'image', 'redirect_url'])
            ->keyBy('id');

        // Group by reel_id — each reel can have multiple ads
        $map = [];
        foreach ($placements as $p) {
            if (empty($p->advertisement_id)) {
                continue;
            }
            $adRow = $adsById[$p->advertisement_id] ?? null;
            if (!$adRow) {
                continue;
            }

            // advertisements.image stores an attachment ID — resolve to full URL
            $imgData = get_attachment_image_by_id((int) $adRow->image);
            $imgUrl  = $imgData['img_url'] ?? null;
            if (!$imgUrl) {
                continue;
            }

            // Append to this reel's ad list (supports multiple ads per reel)
            $map[$p->reel_id][] = [
                'image'        => $imgUrl,
                'redirect_url' => $adRow->redirect_url ?? null,
                'title'        => $adRow->title ?? null,
            ];
        }

        return $map;
    }

    /**
     * Return all active ad overlays for a given reel ID.
     * Returns an empty array if none are configured.
     *
     * @return array[]  Each element: ['image'=>string, 'redirect_url'=>string|null, 'title'=>string|null]
     */
    private function resolveOverlays(array $overlayMap, int $reelId): array
    {
        return $overlayMap[$reelId] ?? [];
    }
}
