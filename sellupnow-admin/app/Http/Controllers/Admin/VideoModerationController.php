<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VideoStatusMail;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class VideoModerationController extends Controller
{
    public function index(Request $request)
    {
        $customerWebUrl = rtrim((string) env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');

        $approval = (string) $request->get('approval', 'all'); // pending|approved|all

        $query = $this->listocean()->table('listings as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->select([
                'l.id',
                'l.title',
                'l.slug',
                'l.video_url',
                'l.video_is_approved',
                'l.status',
                'l.is_published',
                'l.created_at',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->whereNull('l.deleted_at')
            ->whereNotNull('l.video_url')
            ->where('l.video_url', '!=', '')
            ->when($approval === 'pending', fn ($builder) => $builder->where('l.video_is_approved', 0))
            ->when($approval === 'approved', fn ($builder) => $builder->where('l.video_is_approved', 1))
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = (string) $request->search;
                $builder->where(function ($nested) use ($search) {
                    $nested->where('l.title', 'like', "%{$search}%")
                        ->orWhere('l.slug', 'like', "%{$search}%")
                        ->orWhere('l.video_url', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('l.id');

        /** @var LengthAwarePaginator $videos */
        $videos = $query->paginate(15);

        $videos->setCollection(
            $videos->getCollection()->map(function ($row) use ($customerWebUrl) {
                $row->user = (object) ['name' => $row->user_name ?? null];
                $row->listing_url = $customerWebUrl . '/listing/' . ($row->slug ?? '');
                return $row;
            })
        );

        return view('admin.video-moderation.index', compact('videos', 'approval'));
    }

    public function create()
    {
        return view('admin.video-moderation.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|integer|min:1',
            'video_file' => 'required_without:video_url|nullable|file|mimes:mp4,webm,ogg,mov|max:204800',
            'video_url'  => 'required_without:video_file|nullable|string|max:2000',
        ]);

        $listingId = (int) $request->input('listing_id');
        $listing = $this->listocean()->table('listings')->where('id', $listingId)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        $videoUrl = $request->hasFile('video_file')
            ? $this->uploadVideoFile($request->file('video_file'), 'reels')
            : (string) $request->input('video_url', '');

        $this->listocean()->table('listings')->where('id', $listingId)->update([
            'video_url'         => $videoUrl,
            'video_is_approved' => 0,
            'updated_at'        => now(),
        ]);

        return redirect()->route('admin.videoModeration.index')->withSuccess('Video added. Pending approval.');
    }

    /**
     * Upload a video/image file to the shared ListOcean media disk.
     * Returns the fully-qualified public URL.
     */
    private function uploadVideoFile(UploadedFile $file, string $folder): string
    {
        $disk = Storage::disk('listocean_media');
        $path = $disk->putFile($folder, $file);
        return $disk->url($path);
    }

    public function show(int $id)
    {
        $customerWebUrl = rtrim((string) env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');

        $row = $this->listocean()->table('listings as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->select([
                'l.id',
                'l.title',
                'l.slug',
                'l.video_url',
                'l.video_is_approved',
                'l.status',
                'l.is_published',
                'l.created_at',
                'l.updated_at',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->where('l.id', $id)
            ->first();

        if (! $row) {
            abort(404);
        }

        $row->user = (object) ['name' => $row->user_name ?? null];
        $row->listing_url = $customerWebUrl . '/listing/' . ($row->slug ?? '');

        return view('admin.video-moderation.show', compact('row'));
    }

    public function edit(int $id)
    {
        $row = $this->listocean()->table('listings')
            ->select('id', 'title', 'slug', 'video_url', 'video_is_approved')
            ->where('id', $id)
            ->first();

        if (! $row) {
            abort(404);
        }

        $ads = $this->listocean()->table('advertisements')
            ->select('id', 'title', 'type', 'status')
            ->where('status', 1)
            ->orderByDesc('id')
            ->get();

        $currentPlacement = $this->listocean()->table('reel_ad_placements')
            ->select('advertisement_id')
            ->where('status', 1)
            ->where('reel_type', 'listing')
            ->where('placement', 'bottom_overlay')
            ->where('reel_id', $id)
            ->first();

        $currentAdId = $currentPlacement ? (int) $currentPlacement->advertisement_id : 0;

        return view('admin.video-moderation.edit', compact('row', 'ads', 'currentAdId'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'video_file'             => 'nullable|file|mimes:mp4,webm,ogg,mov|max:204800',
            'video_url'              => 'nullable|string|max:2000',
            'video_is_approved'      => 'nullable|boolean',
            'bottom_advertisement_id'=> 'nullable|integer|min:0',
        ]);

        $listing = $this->listocean()->table('listings')->where('id', $id)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        $update = ['updated_at' => now()];

        // File upload takes priority over URL text
        if ($request->hasFile('video_file')) {
            $update['video_url']         = $this->uploadVideoFile($request->file('video_file'), 'reels');
            $update['video_is_approved'] = 0; // re-queue for approval after replacement
        } elseif ($request->has('video_url')) {
            $newUrl = (string) $request->input('video_url', '');
            $update['video_url'] = $newUrl;
            if (empty($newUrl)) {
                $update['video_is_approved'] = 0;
            }
        }

        if ($request->has('video_is_approved') && !empty($listing->video_url)) {
            $update['video_is_approved'] = $request->boolean('video_is_approved') ? 1 : 0;
        }

        $this->listocean()->table('listings')->where('id', $id)->update($update);

        // Save banner/ad placement for this reel video (optional)
        if ($request->has('bottom_advertisement_id')) {
            $adId = (int) $request->input('bottom_advertisement_id', 0);

            if ($adId <= 0) {
                $this->listocean()->table('reel_ad_placements')
                    ->where('reel_type', 'listing')
                    ->where('placement', 'bottom_overlay')
                    ->where('reel_id', $id)
                    ->delete();
            } else {
                $adExists = $this->listocean()->table('advertisements')->where('id', $adId)->exists();
                if ($adExists) {
                    $this->listocean()->table('reel_ad_placements')->updateOrInsert(
                        ['reel_type' => 'listing', 'placement' => 'bottom_overlay', 'reel_id' => $id],
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

        return redirect()->route('admin.videoModeration.index')->withSuccess('Video updated successfully');
    }

    public function updateApproval(Request $request, int $id)
    {
        $data = $request->validate([
            'video_is_approved' => 'required|boolean',
        ]);

        $listing = $this->listocean()->table('listings')->where('id', $id)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        $approved = (bool) $data['video_is_approved'];

        $this->listocean()->table('listings')->where('id', $id)->update([
            'video_is_approved' => $approved ? 1 : 0,
            'updated_at' => now(),
        ]);

        // Notify the seller via email
        try {
            $user = $this->listocean()->table('users')->where('id', $listing->user_id)->first();
            if ($user && ! empty($user->email)) {
                Mail::to($user->email)->send(new VideoStatusMail(
                    sellerName:   $user->name ?? 'Seller',
                    listingTitle: $listing->title,
                    approved:     $approved,
                    rejectReason: $request->input('reject_reason'),
                ));
            }
        } catch (\Throwable $e) {
            // Email failure must not block the approval update
            logger()->error('VideoModerationController: email failed — ' . $e->getMessage());
        }

        return back()->withSuccess('Video approval status updated successfully');
    }

    public function removeVideo(int $id)
    {
        $listing = $this->listocean()->table('listings')->where('id', $id)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        $this->listocean()->table('listings')->where('id', $id)->update([
            'video_url' => null,
            'video_is_approved' => 0,
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Video removed successfully');
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }
}
