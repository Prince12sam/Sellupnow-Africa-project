<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdVideo;
use App\Models\AdVideoLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdVideoController extends Controller
{
    private function paginationParams(Request $request): array
    {
        $page    = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 10), 1);
        return [$page, $perPage, ($page - 1) * $perPage];
    }

    private function formatVideo(AdVideo $v, ?int $userId): array
    {
        return [
            'id'          => $v->id,
            'title'       => $v->title,
            'description' => $v->description,
            'video_url'   => $v->video_url ? asset('storage/' . $v->video_url) : null,
            'thumbnail'   => $v->thumbnail ? asset('storage/' . $v->thumbnail) : null,
            'views'       => $v->views,
            'likes_count' => $v->likes_count,
            'is_liked'    => $userId ? AdVideoLike::where('ad_video_id', $v->id)->where('user_id', $userId)->exists() : false,
            'listing_id'  => $v->listing_id,
            'user_id'     => $v->user_id,
            'created_at'  => $v->created_at,
        ];
    }

    /** List all active ad videos (paginated). */
    public function index(Request $request)
    {
        // Flutter sends start=1,2,3... (1-based page) and limit=N
        $page    = max((int) $request->query('start', 1), 1);
        $perPage = min(max((int) $request->query('limit', 20), 1), 50);
        $offset  = ($page - 1) * $perPage;
        $authId  = auth('api')->id();

        // --- SQL-level paginated UNION ---
        // Sub-query 1: ad_videos (user-uploaded, active)
        $adVideosQuery = DB::table('ad_videos')
            ->select(DB::raw("
                id, user_id, listing_id, video_url, thumbnail, thumbnail_url,
                caption, title, description, views AS view_count, likes_count,
                is_sponsored, cta_text, cta_url, start_at, end_at, created_at,
                NULL AS image, NULL AS price, NULL AS lat, NULL AS lon,
                'ad_video' AS _source
            "))
            ->whereNull('deleted_at')
            ->where('is_active', 1);

        // Sub-query 2: listings with approved videos
        $listingsQuery = DB::table('listings')
            ->select(DB::raw("
                id, user_id, NULL AS listing_id, video_url, NULL AS thumbnail, NULL AS thumbnail_url,
                NULL AS caption, title, description, view AS view_count, 0 AS likes_count,
                0 AS is_sponsored, NULL AS cta_text, NULL AS cta_url, NULL AS start_at, NULL AS end_at, created_at,
                image, price, lat, lon,
                'listing' AS _source
            "))
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->where('video_is_approved', 1)
            ->where('status', 1)
            ->where('is_published', 1);

        // UNION + sort + paginate at SQL level
        $items = $adVideosQuery->unionAll($listingsQuery)
            ->orderByDesc('view_count')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        // Bulk-load users and media_uploads for the current page
        $userIds     = $items->pluck('user_id')->filter()->unique()->values()->all();
        $usersById   = !empty($userIds) ? DB::table('users')
            ->whereIn('id', $userIds)
            ->get(['id', 'name', 'image', 'media_id', 'created_at'])
            ->keyBy('id') : collect();

        // Collect all numeric image IDs from users + listings
        $mediaIds = collect();
        foreach ($usersById as $u) {
            if ($u->media_id) {
                $mediaIds->push((int) $u->media_id);
            } elseif ($u->image && ctype_digit(trim((string) $u->image))) {
                $mediaIds->push((int) $u->image);
            }
        }
        foreach ($items as $v) {
            if ($v->_source === 'listing' && isset($v->image) && $v->image && ctype_digit(trim((string) $v->image))) {
                $mediaIds->push((int) $v->image);
            }
        }
        $mediaById = $mediaIds->unique()->isNotEmpty()
            ? DB::table('media_uploads')->whereIn('id', $mediaIds->unique()->all())->get(['id', 'path'])->keyBy('id')
            : collect();

        $base = rtrim((string) env('CUSTOMER_WEB_URL', config('app.url')), '/');

        $resolveMediaUrl = function (?string $image) use ($base, $mediaById): ?string {
            if (! $image) {
                return null;
            }
            if (str_starts_with($image, 'http')) {
                return $image;
            }
            if (ctype_digit(trim($image))) {
                $row = $mediaById->get((int) $image);
                return $row ? $base . '/assets/uploads/media-uploader/' . ltrim((string) $row->path, '/') : null;
            }
            if (Storage::disk('public')->exists($image)) {
                return Storage::disk('public')->url($image);
            }
            return null;
        };

        $resolveUserImage = function ($user) use ($resolveMediaUrl): ?string {
            if (! $user) {
                return null;
            }
            if ($user->media_id) {
                return $resolveMediaUrl((string) $user->media_id);
            }
            return $resolveMediaUrl($user->image);
        };

        // Bulk load like status for authenticated user
        $likedIds = collect();
        if ($authId) {
            $adVideoIds = $items->where('_source', 'ad_video')->pluck('id')->all();
            if (! empty($adVideoIds)) {
                $likedIds = DB::table('ad_video_likes')
                    ->where('user_id', $authId)
                    ->whereIn('ad_video_id', $adVideoIds)
                    ->pluck('ad_video_id')
                    ->flip();
            }
        }

        $data = $items->map(function ($v) use ($usersById, $resolveMediaUrl, $resolveUserImage, $likedIds, $base) {
            $user     = $v->user_id ? $usersById->get($v->user_id) : null;
            $isLiked  = $v->_source === 'ad_video' ? $likedIds->has($v->id) : false;

            if ($v->_source === 'ad_video') {
                $videoUrl = $v->video_url
                    ? (str_starts_with($v->video_url, 'http') ? $v->video_url : asset('storage/' . $v->video_url))
                    : null;
                $thumbUrl = $v->thumbnail_url
                    ?? ($v->thumbnail ? asset('storage/' . $v->thumbnail) : null);
                return [
                    '_id'         => (string) $v->id,
                    'ad'          => $v->listing_id ? (string) $v->listing_id : null,
                    'uploader'    => $user ? [
                        '_id'          => (string) $user->id,
                        'name'         => $user->name,
                        'profileImage' => $resolveUserImage($user),
                        'registeredAt' => $user->created_at,
                    ] : null,
                    'videoUrl'    => $videoUrl,
                    'thumbnailUrl'=> $thumbUrl,
                    'caption'     => $v->caption ?? $v->title,
                    'totalLikes'  => (int) ($v->likes_count ?? 0),
                    'isLike'      => $isLiked,
                    'isFollow'    => false,
                    'isSponsored' => (bool) ($v->is_sponsored ?? false),
                    'isActive'    => true,
                    'adType'      => null,
                    'ctaText'     => $v->cta_text ?? null,
                    'ctaUrl'      => $v->cta_url ?? null,
                    'adDetails'   => null,
                    'createdAt'   => $v->created_at,
                    'shares'      => 0,
                    'startAt'     => $v->start_at ?? null,
                    'endAt'       => $v->end_at ?? null,
                    'priority'    => null,
                ];
            }

            // Listing-based video
            $thumbUrl = $resolveMediaUrl((string) ($v->image ?? ''));
            return [
                '_id'         => (string) $v->id,
                'ad'          => (string) $v->id,
                'uploader'    => $user ? [
                    '_id'          => (string) $user->id,
                    'name'         => $user->name,
                    'profileImage' => $resolveUserImage($user),
                    'registeredAt' => $user->created_at,
                ] : null,
                'videoUrl'    => $v->video_url,
                'thumbnailUrl'=> $thumbUrl,
                'caption'     => $v->title,
                'totalLikes'  => 0,
                'isLike'      => false,
                'isFollow'    => false,
                'isSponsored' => false,
                'isActive'    => true,
                'adType'      => null,
                'ctaText'     => null,
                'ctaUrl'      => null,
                'adDetails'   => [
                    'title'        => $v->title,
                    'subTitle'     => null,
                    'description'  => $v->description ?? null,
                    'primaryImage' => $thumbUrl,
                    'location'     => [
                        'latitude'    => $v->lat ? (float) $v->lat : null,
                        'longitude'   => $v->lon ? (float) $v->lon : null,
                        'fullAddress' => null,
                    ],
                    'price' => $v->price ? (float) $v->price : null,
                ],
                'createdAt'   => $v->created_at,
                'shares'      => 0,
                'startAt'     => null,
                'endAt'       => null,
                'priority'    => null,
            ];
        })->values()->all();

        return response()->json([
            'message' => 'ad videos',
            'data'    => $data,
        ]);
    }

    /** List ad videos belonging to a specific seller. */
    public function ofSeller(Request $request)
    {
        [$page, $perPage, $skip] = $this->paginationParams($request);
        $userId     = auth('api')->id();
        $sellerId   = (int) ($request->query('seller_id') ?? $request->query('user_id'));

        $query = AdVideo::where('user_id', $sellerId)->active()->latest('id');

        $total  = $query->count();
        $videos = $query->skip($skip)->take($perPage)->get()
            ->map(fn ($v) => $this->formatVideo($v, $userId));

        return $this->json('seller ad videos', ['total' => $total, 'videos' => $videos]);
    }

    /** Upload a new ad video. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'video'      => 'required|file|mimes:mp4,mov,avi,webm|max:102400', // 100 MB
            'thumbnail'  => 'nullable|image|max:5120',
            'title'      => 'nullable|string|max:255',
            'description'=> 'nullable|string',
            'listing_id' => 'nullable|integer|exists:listings,id',
        ]);

        $userId    = auth('api')->id();
        $videoPath = $request->file('video')->store('ad-videos', 'public');
        $thumbPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('ad-video-thumbs', 'public')
            : null;

        $video = AdVideo::create([
            'user_id'     => $userId,
            'listing_id'  => $data['listing_id'] ?? null,
            'title'       => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'video_url'   => $videoPath,
            'thumbnail'   => $thumbPath,
        ]);

        return $this->json('Ad video uploaded successfully', [
            'video' => $this->formatVideo($video, $userId),
        ]);
    }

    /** Update title/description/thumbnail of an owned video. */
    public function update(Request $request, $id = null)
    {
        $videoId = $id ?? $request->input('video_id');
        $userId  = auth('api')->id();

        $video = AdVideo::where('id', $videoId)->where('user_id', $userId)->firstOrFail();

        $data = $request->validate([
            'title'      => 'nullable|string|max:255',
            'description'=> 'nullable|string',
            'thumbnail'  => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('ad-video-thumbs', 'public');
        }

        $video->update($data);

        return $this->json('Ad video updated', [
            'video' => $this->formatVideo($video->fresh(), $userId),
        ]);
    }

    /** Soft-delete an owned ad video. */
    public function destroy(Request $request, $id = null)
    {
        $videoId = $id ?? $request->input('video_id');
        $userId  = auth('api')->id();

        $video = AdVideo::where('id', $videoId)->where('user_id', $userId)->firstOrFail();
        $video->delete();

        return $this->json('Ad video deleted successfully');
    }
}
