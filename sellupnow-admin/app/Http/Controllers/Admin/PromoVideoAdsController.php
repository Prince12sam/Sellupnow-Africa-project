<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PromoVideoAdsController extends Controller
{
    private function hasAdVideosTable(): bool
    {
        return Schema::connection('listocean')->hasTable('ad_videos');
    }

    private function hasReelPlacementsTable(): bool
    {
        return Schema::connection('listocean')->hasTable('reel_ad_placements');
    }

    public function index(Request $request)
    {
        $status = (string) $request->get('status', 'pending'); // pending|approved|rejected|all

        if (! $this->hasAdVideosTable()) {
            $current = (int) ($request->get('page', 1) ?: 1);
            $videos = new LengthAwarePaginator([], 0, 15, $current, ['path' => $request->url(), 'query' => $request->query()]);
            return view('admin.promo-video-ads.index', compact('videos', 'status'));
        }

        $query = $this->listocean()->table('ad_videos as v')
            ->leftJoin('users as u', 'u.id', '=', 'v.user_id')
            ->select([
                'v.id',
                'v.user_id',
                'v.video_url',
                'v.caption',
                'v.cta_text',
                'v.cta_url',
                'v.is_sponsored',
                'v.is_approved',
                'v.is_rejected',
                'v.reject_reason',
                'v.start_at',
                'v.end_at',
                'v.created_at',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->when($status === 'pending', fn ($b) => $b->where('v.is_approved', 0)->where('v.is_rejected', 0))
            ->when($status === 'approved', fn ($b) => $b->where('v.is_approved', 1))
            ->when($status === 'rejected', fn ($b) => $b->where('v.is_rejected', 1))
            ->when($request->filled('search'), function ($b) use ($request) {
                $search = (string) $request->search;
                $b->where(function ($q) use ($search) {
                    $q->where('v.caption', 'like', "%{$search}%")
                        ->orWhere('v.video_url', 'like', "%{$search}%")
                        ->orWhere('v.cta_url', 'like', "%{$search}%")
                        ->orWhere('v.id', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('v.id');

        /** @var LengthAwarePaginator $videos */
        try {
            $videos = $query->paginate(15);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PromoVideoAdsController list query failed', ['error' => $e->getMessage()]);
            $current = (int) ($request->get('page', 1) ?: 1);
            $videos = new LengthAwarePaginator([], 0, 15, $current, ['path' => $request->url(), 'query' => $request->query()]);
        }

        $videos->setCollection(
            $videos->getCollection()->map(function ($row) {
                $displayName = $row->user_name ?? null;
                if ($displayName === null && $row->user_id === null) {
                    $displayName = 'Admin';
                }
                $row->user = (object) ['name' => $displayName];
                return $row;
            })
        );

        return view('admin.promo-video-ads.index', compact('videos', 'status'));
    }

    public function create()
    {
        if (! $this->hasAdVideosTable()) {
            return redirect()->route('admin.promoVideoAds.index')->withError('ListOcean table ad_videos is missing.');
        }

        return view('admin.promo-video-ads.create');
    }

    public function store(Request $request)
    {
        if (! $this->hasAdVideosTable()) {
            return redirect()->route('admin.promoVideoAds.index')->withError('ListOcean table ad_videos is missing.');
        }

        $request->validate([
            'video_file'     => 'required|file|mimes:mp4,webm,ogg,mov|max:204800',
            'thumbnail_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'caption'        => 'nullable|string|max:300',
            'cta_text'       => 'nullable|string|max:60',
            'cta_url'        => 'nullable|string|max:2000',
            'start_at'       => 'nullable|date',
            'end_at'         => 'nullable|date|after_or_equal:start_at',
        ]);

        $videoUrl = $this->uploadVideoFile($request->file('video_file'), 'ad-videos');

        $thumbUrl = null;
        if ($request->hasFile('thumbnail_file')) {
            $thumbUrl = $this->uploadVideoFile($request->file('thumbnail_file'), 'ad-video-thumbs');
        }

        $this->listocean()->table('ad_videos')->insert([
            'user_id'      => null,
            'video_url'    => $videoUrl,
            'thumbnail_url'=> $thumbUrl,
            'caption'      => $request->input('caption'),
            'cta_text'     => $request->input('cta_text'),
            'cta_url'      => $request->input('cta_url'),
            'start_at'     => $request->input('start_at'),
            'end_at'       => $request->input('end_at'),
            'is_sponsored' => 1,
            'is_approved'  => 1,
            'approved_at'  => now(),
            'is_rejected'  => 0,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('admin.promoVideoAds.index')->withSuccess('Promo video ad created successfully.');
    }

    public function edit(int $id)
    {
        if (! $this->hasAdVideosTable()) {
            return redirect()->route('admin.promoVideoAds.index')->withError('ListOcean table ad_videos is missing.');
        }

        $row = $this->listocean()->table('ad_videos')
            ->where('id', $id)
            ->first();

        if (! $row) abort(404);

        $ads = $this->listocean()->table('advertisements')
            ->select('id', 'title', 'type', 'status')
            ->where('status', 1)
            ->orderByDesc('id')
            ->get();

        $currentPlacement = $this->hasReelPlacementsTable()
            ? $this->listocean()->table('reel_ad_placements')
                ->select('advertisement_id')
                ->where('status', 1)
                ->where('reel_type', 'ad_video')
                ->where('placement', 'bottom_overlay')
                ->where('reel_id', $id)
                ->first()
            : null;

        $currentAdId = $currentPlacement ? (int) $currentPlacement->advertisement_id : 0;

        return view('admin.promo-video-ads.edit', compact('row', 'ads', 'currentAdId'));
    }

    public function update(Request $request, int $id)
    {
        if (! $this->hasAdVideosTable()) {
            return redirect()->route('admin.promoVideoAds.index')->withError('ListOcean table ad_videos is missing.');
        }

        $data = $request->validate([
            'video_file'              => 'nullable|file|mimes:mp4,webm,ogg,mov|max:204800',
            'thumbnail_file'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'caption'                 => 'nullable|string|max:300',
            'cta_text'                => 'nullable|string|max:60',
            'cta_url'                 => 'nullable|string|max:2000',
            'start_at'                => 'nullable|date',
            'end_at'                  => 'nullable|date|after_or_equal:start_at',
            'is_sponsored'            => 'nullable|boolean',
            'moderation'              => 'required|string|in:pending,approved,rejected',
            'reject_reason'           => 'nullable|string|max:300',
            'bottom_advertisement_id' => 'nullable|integer|min:0',
        ]);

        $row = $this->listocean()->table('ad_videos')->where('id', $id)->first();
        if (! $row) return back()->withError('Promo video not found');

        $update = [
            'caption'      => $data['caption'] ?? null,
            'cta_text'     => $data['cta_text'] ?? null,
            'cta_url'      => $data['cta_url'] ?? null,
            'start_at'     => $data['start_at'] ?? null,
            'end_at'       => $data['end_at'] ?? null,
            'is_sponsored' => !empty($data['is_sponsored']) ? 1 : 0,
            'updated_at'   => now(),
        ];

        // Allow admin to replace the video file
        if ($request->hasFile('video_file')) {
            $update['video_url'] = $this->uploadVideoFile($request->file('video_file'), 'ad-videos');
        }
        if ($request->hasFile('thumbnail_file')) {
            $update['thumbnail_url'] = $this->uploadVideoFile($request->file('thumbnail_file'), 'ad-video-thumbs');
        }

        if ($data['moderation'] === 'approved') {
            $update['is_approved'] = 1;
            $update['approved_at'] = now();
            $update['is_rejected'] = 0;
            $update['reject_reason'] = null;
            $update['rejected_at'] = null;
        } elseif ($data['moderation'] === 'rejected') {
            $update['is_approved'] = 0;
            $update['approved_at'] = null;
            $update['is_rejected'] = 1;
            $update['reject_reason'] = $data['reject_reason'] ?? 'Rejected';
            $update['rejected_at'] = now();
        } else {
            $update['is_approved'] = 0;
            $update['approved_at'] = null;
            $update['is_rejected'] = 0;
            $update['reject_reason'] = null;
            $update['rejected_at'] = null;
        }

        $this->listocean()->table('ad_videos')->where('id', $id)->update($update);

        // If approving and this video is linked to a listing:
        // sync video_url onto the listing and retire any other approved ad_videos for it.
        if ($data['moderation'] === 'approved' && !empty($row->listing_id)) {
            $newVideoUrl = $update['video_url'] ?? $row->video_url;
            $this->listocean()->table('listings')
                ->where('id', $row->listing_id)
                ->update([
                    'video_url'         => $newVideoUrl,
                    'video_is_approved' => 1,
                ]);
            $this->listocean()->table('ad_videos')
                ->where('listing_id', $row->listing_id)
                ->where('id', '!=', $id)
                ->where('is_approved', 1)
                ->update(['is_approved' => 0, 'updated_at' => now()]);
        }

        // If rejecting and this video is linked to a listing, clear listing video too.
        if ($data['moderation'] === 'rejected' && !empty($row->listing_id)) {
            $this->listocean()->table('listings')
                ->where('id', $row->listing_id)
                ->update(['video_url' => null, 'video_is_approved' => 2]);
        }

        if (array_key_exists('bottom_advertisement_id', $data) && $this->hasReelPlacementsTable()) {
            $adId = (int) ($data['bottom_advertisement_id'] ?? 0);

            if ($adId <= 0) {
                $this->listocean()->table('reel_ad_placements')
                    ->where('reel_type', 'ad_video')
                    ->where('placement', 'bottom_overlay')
                    ->where('reel_id', $id)
                    ->delete();
            } else {
                $adExists = $this->listocean()->table('advertisements')->where('id', $adId)->exists();
                if ($adExists) {
                    $this->listocean()->table('reel_ad_placements')->updateOrInsert(
                        ['reel_type' => 'ad_video', 'placement' => 'bottom_overlay', 'reel_id' => $id],
                        [
                            'advertisement_id' => $adId,
                            'status' => 1,
                            'start_at' => null,
                            'end_at' => null,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        }

        return redirect()->route('admin.promoVideoAds.index')->withSuccess('Promo video updated successfully');
    }

    /**
     * Quick approve or reject a user-submitted video directly from the index.
     * POST /admin/promo-video-ads/{id}/moderate
     */
    public function quickModerate(Request $request, int $id)
    {
        if (! $this->hasAdVideosTable()) {
            return back()->withError('ListOcean table ad_videos is missing.');
        }

        $data = $request->validate([
            'action'        => 'required|in:approve,reject',
            'reject_reason' => 'nullable|string|max:300',
        ]);

        $row = $this->listocean()->table('ad_videos')->where('id', $id)->first();
        if (! $row) return back()->withError('Video not found.');

        if ($data['action'] === 'approve') {
            $this->listocean()->table('ad_videos')->where('id', $id)->update([
                'is_approved'   => 1,
                'approved_at'   => now(),
                'is_rejected'   => 0,
                'reject_reason' => null,
                'rejected_at'   => null,
                'updated_at'    => now(),
            ]);
            // If linked to a listing: sync video_url + video_is_approved onto the listing,
            // and retire any OTHER ad_videos for the same listing so only one is active.
            if (!empty($row->listing_id)) {
                $this->listocean()->table('listings')
                    ->where('id', $row->listing_id)
                    ->update([
                        'video_url'         => $row->video_url,
                        'video_is_approved' => 1,
                    ]);
                // De-approve any sibling ad_videos for the same listing (not this one)
                $this->listocean()->table('ad_videos')
                    ->where('listing_id', $row->listing_id)
                    ->where('id', '!=', $id)
                    ->where('is_approved', 1)
                    ->update(['is_approved' => 0, 'updated_at' => now()]);
            }
            return back()->withSuccess("Video #{$id} approved.");
        }

        $this->listocean()->table('ad_videos')->where('id', $id)->update([
            'is_approved'   => 0,
            'approved_at'   => null,
            'is_rejected'   => 1,
            'reject_reason' => $data['reject_reason'] ?: 'Rejected by admin.',
            'rejected_at'   => now(),
            'updated_at'    => now(),
        ]);
        // If linked to a listing, clear its video_url so it disappears from the Reels feed
        if (!empty($row->listing_id)) {
            $this->listocean()->table('listings')
                ->where('id', $row->listing_id)
                ->update([
                    'video_url'         => null,
                    'video_is_approved' => 2,
                ]);
        }
        return back()->withSuccess("Video #{$id} rejected.");
    }

    /**
     * Upload a file to the shared ListOcean media disk and return its public URL.
     */
    private function uploadVideoFile(UploadedFile $file, string $folder): string
    {
        $disk = Storage::disk('listocean_media');
        $path = $disk->putFile($folder, $file);
        return $disk->url($path);
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }
}
