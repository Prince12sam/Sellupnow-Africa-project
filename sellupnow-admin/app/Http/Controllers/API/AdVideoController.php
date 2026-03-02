<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdVideo;
use App\Models\AdVideoLike;
use Illuminate\Http\Request;
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
        [$page, $perPage, $skip] = $this->paginationParams($request);
        $userId = auth('api')->id();

        $query = AdVideo::active()->with('user')->latest('id');

        if ($listingId = $request->query('listing_id')) {
            $query->where('listing_id', $listingId);
        }

        $total  = $query->count();
        $videos = $query->skip($skip)->take($perPage)->get()
            ->map(fn ($v) => $this->formatVideo($v, $userId));

        return $this->json('ad videos', ['total' => $total, 'videos' => $videos]);
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
