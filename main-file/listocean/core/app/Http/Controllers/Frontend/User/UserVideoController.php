<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Backend\AdVideo;
use App\Models\Backend\Listing;
use App\Services\MembershipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserVideoController extends Controller
{
    public function __construct(private MembershipService $membershipService) {}

    /**
     * List all videos uploaded by the logged-in user.
     * GET /user/my-videos
     */
    public function index(): View
    {
        $userId = Auth::guard('web')->id();

        $videos = AdVideo::where('user_id', $userId)
            ->with('listing:id,title,slug')
            ->latest()
            ->paginate(12);

        $videoQuota = $this->membershipService->getVideoQuota($userId);
        $videoUsed  = AdVideo::where('user_id', $userId)->count();

        return view('frontend.user.videos.my-videos', compact('videos', 'videoQuota', 'videoUsed'));
    }

    /**
     * Show the upload form.
     * GET /user/my-videos/create
     */
    public function create(): View|RedirectResponse
    {
        $userId     = Auth::guard('web')->id();
        $videoQuota = $this->membershipService->getVideoQuota($userId);
        $videoUsed  = AdVideo::where('user_id', $userId)->count();

        // Block upload if quota exhausted
        if ($videoQuota === 0 || ($videoQuota > 0 && $videoUsed >= $videoQuota)) {
            return redirect()->route('user.my.videos')
                ->with('error', __('You have reached your video upload limit for your current plan.'));
        }

        // Only active published listings belonging to this user
        $listings = Listing::where('user_id', $userId)
            ->where('status', 1)
            ->where('is_published', 1)
            ->select('id', 'title')
            ->latest()
            ->get();

        return view('frontend.user.videos.create', compact('listings', 'videoQuota', 'videoUsed'));
    }

    /**
     * Store a new uploaded video.
     * POST /user/my-videos
     */
    public function store(Request $request): RedirectResponse
    {
        $userId     = Auth::guard('web')->id();
        $videoQuota = $this->membershipService->getVideoQuota($userId);
        $videoUsed  = AdVideo::where('user_id', $userId)->count();

        if ($videoQuota === 0 || ($videoQuota > 0 && $videoUsed >= $videoQuota)) {
            return back()->with('error', __('Video upload limit reached for your current membership plan.'));
        }

        $request->validate([
            'video_file'       => 'required|file|mimes:mp4,webm,mov|max:204800', // 200 MB
            'thumbnail_base64' => 'nullable|string|max:2800000', // ~2 MB base64
            'caption'          => 'nullable|string|max:300',
            'listing_id'       => 'nullable|integer|exists:listings,id',
            'cta_text'         => 'nullable|string|max:60',
            'cta_url'          => 'nullable|url|max:2000',
        ]);

        // Ensure the tagged listing belongs to this user
        if ($request->filled('listing_id')) {
            $listingOwner = Listing::where('id', $request->listing_id)
                ->where('user_id', $userId)
                ->exists();
            if (!$listingOwner) {
                return back()->withErrors(['listing_id' => __('You can only tag your own listings.')]);
            }
        }

        $videoPath = $request->file('video_file')->store('ad-videos', 'public');
        $videoUrl  = Storage::disk('public')->url($videoPath);

        // Decode the browser-captured base64 thumbnail and save as JPEG
        $thumbUrl = null;
        $b64 = $request->input('thumbnail_base64');
        if ($b64 && str_starts_with($b64, 'data:image/')) {
            // strip the data URI prefix ("data:image/jpeg;base64,")
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $b64));
            if ($imageData !== false && strlen($imageData) > 500) {
                $thumbFilename = 'ad-video-thumbs/' . uniqid('thumb_', true) . '.jpg';
                Storage::disk('public')->put($thumbFilename, $imageData);
                $thumbUrl = Storage::disk('public')->url($thumbFilename);
            }
        }

        $listingId = $request->input('listing_id') ?: null;

        // If tagging an existing listing: retire any previously approved video for it
        // so old videos don't persist in the reel feed while the new one awaits review.
        if ($listingId) {
            AdVideo::where('listing_id', $listingId)
                ->where('user_id', $userId)
                ->where('is_approved', 1)
                ->update(['is_approved' => 0, 'updated_at' => now()]);

            // Also pull the listing's own video_url back to pending so it stops showing
            Listing::where('id', $listingId)->where('user_id', $userId)
                ->update(['video_is_approved' => 0]);
        }

        AdVideo::create([
            'user_id'      => $userId,
            'listing_id'   => $listingId,
            'video_url'    => $videoUrl,
            'thumbnail_url'=> $thumbUrl,
            'caption'      => $request->input('caption'),
            'cta_text'     => $request->input('cta_text'),
            'cta_url'      => $request->input('cta_url'),
            'is_approved'  => 0,
            'is_rejected'  => 0,
            'is_sponsored' => 0,
        ]);

        return redirect()->route('user.my.videos')
            ->with('success', __('Video uploaded successfully. It will be visible once approved by admin.'));
    }

    /**
     * Show the edit form for an existing video.
     * GET /user/my-videos/{id}/edit
     */
    public function edit(int $id): View|RedirectResponse
    {
        $userId = Auth::guard('web')->id();
        $video  = AdVideo::where('id', $id)->where('user_id', $userId)->firstOrFail();

        $listings = Listing::where('user_id', $userId)
            ->where('status', 1)
            ->where('is_published', 1)
            ->select('id', 'title')
            ->latest()
            ->get();

        return view('frontend.user.videos.edit', compact('video', 'listings'));
    }

    /**
     * Update an existing video (caption / listing / CTA, and optionally replace the file).
     * POST /user/my-videos/{id}/update
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $userId     = Auth::guard('web')->id();
        $video      = AdVideo::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $videoQuota = $this->membershipService->getVideoQuota($userId);

        // If replacing the video file, re-check that the plan still allows uploads
        if ($request->hasFile('video_file') && $videoQuota === 0) {
            return back()->with('error', __('Your current plan does not allow video uploads. Please upgrade.'));
        }

        $request->validate([
            'video_file'       => 'nullable|file|mimes:mp4,webm,mov|max:204800',
            'thumbnail_base64' => 'nullable|string|max:2800000', // ~2 MB base64
            'caption'          => 'nullable|string|max:300',
            'listing_id'       => 'nullable|integer|exists:listings,id',
            'cta_text'         => 'nullable|string|max:60',
            'cta_url'          => 'nullable|url|max:2000',
        ]);

        if ($request->filled('listing_id')) {
            $listingOwner = Listing::where('id', $request->listing_id)
                ->where('user_id', $userId)->exists();
            if (!$listingOwner) {
                return back()->withErrors(['listing_id' => __('You can only tag your own listings.')]);
            }
        }

        $urlToPath = function (?string $url): ?string {
            if (!$url) return null;
            $path = ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/');
            return preg_replace('#^storage/#', '', $path) ?: null;
        };

        // Replace video file?
        if ($request->hasFile('video_file')) {
            if ($p = $urlToPath($video->video_url)) Storage::disk('public')->delete($p);
            $videoPath        = $request->file('video_file')->store('ad-videos', 'public');
            $video->video_url = Storage::disk('public')->url($videoPath);
            // Replacing the file resets approval
            $video->is_approved = 0;
            $video->is_rejected = 0;
            $video->reject_reason = null;
        }

        // Replace thumbnail if a new base64 was captured
        $b64 = $request->input('thumbnail_base64');
        if ($b64 && str_starts_with($b64, 'data:image/')) {
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $b64));
            if ($imageData !== false && strlen($imageData) > 500) {
                if ($p = $urlToPath($video->thumbnail_url)) Storage::disk('public')->delete($p);
                $thumbFilename       = 'ad-video-thumbs/' . uniqid('thumb_', true) . '.jpg';
                Storage::disk('public')->put($thumbFilename, $imageData);
                $video->thumbnail_url = Storage::disk('public')->url($thumbFilename);
            }
        }

        $video->caption    = $request->input('caption');
        $video->listing_id = $request->input('listing_id') ?: null;
        $video->cta_text   = $request->input('cta_text');
        $video->cta_url    = $request->input('cta_url');
        $video->save();

        return redirect()->route('user.my.videos')
            ->with('success', __('Video updated successfully.')
                . ($request->hasFile('video_file') ? ' ' . __('It will be visible once re-approved by admin.') : ''));
    }

    /**
     * Delete a user's own video.
     * POST /user/my-videos/{id}/delete
     */
    public function destroy(int $id): RedirectResponse
    {
        $userId = Auth::guard('web')->id();

        $video = AdVideo::where('id', $id)->where('user_id', $userId)->firstOrFail();

        // Remove files from storage (urls are stored as full public URLs)
        $urlToPath = function (?string $url): ?string {
            if (!$url) return null;
            $path = ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/');
            return preg_replace('#^storage/#', '', $path) ?: null;
        };

        if ($p = $urlToPath($video->video_url))     Storage::disk('public')->delete($p);
        if ($p = $urlToPath($video->thumbnail_url)) Storage::disk('public')->delete($p);

        $video->delete();

        return back()->with('success', __('Video deleted.'));
    }
}
