<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Listing;
use App\Models\ReelComment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrendingVideoController extends Controller
{
    public function explore()
    {
        $perPage     = 20;
        $currentPage = max(1, (int) request()->input('page', 1));

        // Listing-based reels — only admin-approved videos
        $listingRows = DB::table('listings')
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->where('video_is_approved', 1)
            ->where('status', 1)
            ->where('is_published', 1)
            ->orderByDesc('view')
            ->orderBy('id')  // oldest first as tiebreaker → new 0-view videos sink to bottom
            ->get(['id', 'title', 'video_url', 'view', 'image', 'created_at'])
            ->map(fn ($r) => (object) (['_type' => 'listing', 'thumbnail_url' => null, 'listing_id' => null] + (array) $r));

        // User-uploaded ad_videos (approved, not rejected).
        // Includes both standalone uploads and listing-linked videos.
        // Deduplication by video_url happens during the merge below so a video
        // that also appears in the listings pool is not shown twice.
        $userVideoRows = DB::table('ad_videos')
            ->whereNotNull('user_id')
            ->where('is_approved', 1)
            ->where('is_rejected', 0)
            ->orderByDesc('views')
            ->orderBy('created_at') // oldest first as tiebreaker → new 0-view videos sink to bottom
            ->get(['id', 'user_id', 'listing_id', 'video_url', 'thumbnail_url', 'caption', 'views', 'created_at'])
            ->map(fn ($r) => (object) [
                '_type'        => 'user_video',
                'id'           => $r->id,
                'title'        => $r->caption ?: __('Video'),
                'video_url'    => $r->video_url,
                'thumbnail_url' => $r->thumbnail_url,
                'view'         => $r->views ?? 0,
                'image'        => null,
                'listing_id'   => $r->listing_id,
                'created_at'   => $r->created_at,
            ]);

        // Merge: sort by views DESC, then listing-pool entries first (they have
        // real view counts and take priority), then oldest first as tiebreaker
        // so newly uploaded 0-view videos sink to the bottom, not the top.
        // Deduplicate by video_url — listing pool always wins.
        $seenUrls = [];
        $all = $listingRows->concat($userVideoRows)
            ->sort(function ($a, $b) {
                $viewDiff = ($b->view ?? 0) <=> ($a->view ?? 0);
                if ($viewDiff !== 0) {
                    return $viewDiff;
                }
                // Listing-pool entries beat user_video entries for same view count
                // so the listing URL is always the one that "wins" the dedup.
                $typeA = ($a->_type ?? '') === 'listing' ? 0 : 1;
                $typeB = ($b->_type ?? '') === 'listing' ? 0 : 1;
                if ($typeA !== $typeB) {
                    return $typeA <=> $typeB;
                }
                // Final tiebreaker: older videos first → new 0-view uploads go to the bottom
                return strcmp((string) ($a->created_at ?? ''), (string) ($b->created_at ?? ''));
            })
            ->filter(function ($item) use (&$seenUrls) {
                $url = (string) ($item->video_url ?? '');
                if ($url === '' || isset($seenUrls[$url])) {
                    return false;
                }
                $seenUrls[$url] = true;
                return true;
            })
            ->values();

        $total  = $all->count();
        $offset = ($currentPage - 1) * $perPage;
        $items  = $all->slice($offset, $perPage)->values();

        $videos = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('frontend.pages.reels.explore', compact('videos'));
    }

    public function watch(int $id)
    {
        $video = Listing::query()
            ->with('listing_creator')
            ->where('id', $id)
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->where('video_is_approved', 1)
            ->where('status', 1)
            ->where('is_published', 1)
            ->firstOrFail();

        $video->increment('view');

        $related = Listing::query()
            ->with('listing_creator')
            ->where('id', '!=', $video->id)
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->where('video_is_approved', 1)
            ->where('status', 1)
            ->where('is_published', 1)
            ->orderByDesc('view')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        // Fetch all active ad overlays for this listing's watch page.
        // Multiple rows = multiple ads that rotate on-screen (Facebook-style).
        $adOverlays = $this->fetchWatchPageOverlays($id);

        return view('frontend.pages.reels.watch', compact('video', 'related', 'adOverlays'));
    }

    /**
     * Fetch ALL active ad overlays for the given listing's watch page.
     * Placement = 'listing_detail_video'.
     *
     * Multiple rows for the same reel_id are returned as an array —
     * the view will rotate through them (Facebook Reels-style).
     *
     * @return array[]  Each element: ['image'=>string, 'redirect_url'=>string|null, 'title'=>string|null]
     */
    private function fetchWatchPageOverlays(int $reelId): array
    {
        $placements = DB::table('reel_ad_placements')
            ->where('reel_id', $reelId)
            ->where('reel_type', 'listing')      // watch page always shows listing reels
            ->where('placement', 'listing_detail_video')
            ->where('status', 1)
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

        // Bulk-fetch all referenced advertisements in one query (avoids N+1)
        $adIds   = $placements->pluck('advertisement_id')->filter()->unique()->values()->all();
        $adsById = DB::table('advertisements')
            ->whereIn('id', $adIds)
            ->where('status', 1)
            ->get(['id', 'title', 'image', 'redirect_url'])
            ->keyBy('id');

        $result = [];
        foreach ($placements as $placement) {
            if (empty($placement->advertisement_id)) continue;

            $adRow = $adsById[$placement->advertisement_id] ?? null;
            if (!$adRow || empty($adRow->image)) continue;

            // advertisements.image stores an attachment ID — resolve to full URL
            $imgData = get_attachment_image_by_id((int) $adRow->image);
            $imgUrl  = $imgData['img_url'] ?? null;
            if (!$imgUrl) continue;

            $result[] = [
                'image'        => $imgUrl,
                'redirect_url' => $adRow->redirect_url ?? null,
                'title'        => $adRow->title ?? null,
            ];
        }

        return $result;
    }

    /* ──────────────────────────────────────────────────────────────
     | REEL COMMENTS
     |─────────────────────────────────────────────────────────────── */

    /**
     * GET /reels/{id}/comments — returns latest 50 comments as JSON.
     */
    public function comments(int $id)
    {
        $comments = ReelComment::where('listing_id', $id)
            ->with('user:id,username,first_name,last_name')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($c) {
                $name = trim(($c->user->first_name ?? '') . ' ' . ($c->user->last_name ?? ''));
                if (!$name) $name = $c->user->username ?? 'User';
                return [
                    'id'         => $c->id,
                    'user_name'  => $name,
                    'initial'    => strtoupper(substr($name, 0, 1)) ?: 'U',
                    'body'       => $c->body,
                    'likes'      => $c->likes,
                    'created_at' => $c->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'comments' => $comments,
            'total'    => ReelComment::where('listing_id', $id)->count(),
        ]);
    }

    /**
     * POST /reels/{id}/comments — store a new comment (requires auth:web).
     */
    public function storeComment(Request $request, int $id)
    {
        $request->validate(['body' => 'required|string|max:500']);

        $user = Auth::guard('web')->user();

        $comment = ReelComment::create([
            'listing_id' => $id,
            'user_id'    => $user->id,
            'body'       => $request->input('body'),
        ]);

        $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (!$name) $name = $user->username ?? 'User';

        return response()->json([
            'id'         => $comment->id,
            'user_name'  => $name,
            'initial'    => strtoupper(substr($name, 0, 1)) ?: 'U',
            'body'       => $comment->body,
            'likes'      => 0,
            'created_at' => 'just now',
        ], 201);
    }
}