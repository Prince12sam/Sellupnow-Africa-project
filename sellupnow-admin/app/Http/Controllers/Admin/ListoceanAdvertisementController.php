<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListoceanAdvertisementController extends Controller
{
    /**
     * Copy an uploaded image into ListOcean's media-uploader directory,
     * insert a media_uploads record, and return the integer ID.
     *
     * advertisements.image MUST be a numeric media_uploads ID — that is the
     * expectation of render_image_markup_by_attachment_id() and every other
     * admin controller (BannerController, FooterController, etc.).
     */
    private function uploadImageFile(UploadedFile $file): int
    {
        $targetDir = env('LISTOCEAN_PUBLIC_PATH')
            ? rtrim(str_replace('\\', '/', env('LISTOCEAN_PUBLIC_PATH')), '/') . '/assets/uploads/media-uploader'
            : listocean_core_path('public/assets/uploads/media-uploader');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '-', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'advertisement';
        $fileName = $safeBase . '-' . time() . '.' . $ext;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (!@copy($file->getRealPath(), $targetPath)) {
            throw new \RuntimeException('Failed to copy uploaded file to ListOcean media directory.');
        }

        $db = $this->listocean();

        // Avoid duplicate row for the same filename
        $existing = $db->table('media_uploads')->where('path', $fileName)->value('id');
        if ($existing) {
            return (int) $existing;
        }

        $type = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'], true) ? 'image' : 'file';

        return (int) $db->table('media_uploads')->insertGetId([
            'title'      => $safeBase,
            'path'       => $fileName,
            'alt'        => $safeBase,
            'size'       => null,
            'dimensions' => null,
            'user_id'    => null,
            'type'       => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Upload a video file into ListOcean's media-uploader directory,
     * insert a media_uploads record (type='video'), and return the integer ID.
     */
    private function uploadVideoFile(\Illuminate\Http\UploadedFile $file): int
    {
        $targetDir = env('LISTOCEAN_PUBLIC_PATH')
            ? rtrim(str_replace('\\', '/', env('LISTOCEAN_PUBLIC_PATH')), '/') . '/assets/uploads/media-uploader'
            : listocean_core_path('public/assets/uploads/media-uploader');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension() ?: 'mp4');
        $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '-', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'ad-video';
        $fileName = $safeBase . '-' . time() . '.' . $ext;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (!@copy($file->getRealPath(), $targetPath)) {
            throw new \RuntimeException('Failed to copy video file to ListOcean media directory.');
        }

        $db = $this->listocean();

        $existing = $db->table('media_uploads')->where('path', $fileName)->value('id');
        if ($existing) {
            return (int) $existing;
        }

        return (int) $db->table('media_uploads')->insertGetId([
            'title'      => $safeBase,
            'path'       => $fileName,
            'alt'        => $safeBase,
            'size'       => null,
            'dimensions' => null,
            'user_id'    => null,
            'type'       => 'video',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Auto-assign (or clear) a frontend_ad_slot row for a named slot key.
     * Calling with $adId = null removes the pairing.
     * $listingId scopes the ad to a single listing; null = all listings (global).
     */
    private function syncFrontendAdSlot(string $slotKey, ?int $adId, bool $active = true, ?int $listingId = null): void
    {
        $slotKey = trim($slotKey);
        if ($slotKey === '') return;

        // Table may not exist in all deployments — skip gracefully if absent
        if (!Schema::connection('listocean')->hasTable('frontend_ad_slots')) {
            return;
        }

        if ($adId === null) {
            $this->listocean()->table('frontend_ad_slots')->where('slot_key', $slotKey)->delete();
            return;
        }

        // Match on both slot_key AND listing_id so per-listing and global rows coexist.
        $this->listocean()->table('frontend_ad_slots')->updateOrInsert(
            ['slot_key' => $slotKey, 'listing_id' => $listingId],
            [
                'advertisement_id' => $adId,
                'status'           => $active ? 1 : 0,
                'start_at'         => null,
                'end_at'           => null,
                'updated_at'       => now(),
                'created_at'       => now(),
            ]
        );
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }

    public function index(Request $request)
    {
        $query = $this->listocean()->table('advertisements')->orderByDesc('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('title', 'like', "%{$s}%");
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }

        $advertisements = $query->paginate(15)->withQueryString();

        return view('admin.site-advertisements.index', compact('advertisements'));
    }

    private function getPromoVideos(): array
    {
        if (!Schema::connection('listocean')->hasTable('ad_videos')) {
            return [];
        }

        return $this->listocean()
            ->table('ad_videos')
            ->where('is_approved', 1)
            ->orderByDesc('id')
            ->get(['id', 'video_url', 'thumbnail_url', 'caption'])
            ->toArray();
    }

    public function create()
    {
        $promoVideos = $this->getPromoVideos();
        return view('admin.site-advertisements.create', compact('promoVideos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|in:image,embed_code,video',
            'size'            => 'nullable|string|max:100',
            'slot'            => 'nullable|string|max:255',
            'listing_id_scope'=> 'nullable|integer|min:1',
            'embed_code'      => 'nullable|string',
            'redirect_url'    => 'nullable|url|max:512',
            'image'           => 'nullable|string|max:512',
            'image_file'      => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'video_file'      => 'nullable|file|mimes:mp4,webm,ogg,mov,avi|max:102400',
            'status'          => 'nullable',
        ]);

        // Determine media value: video upload > library pick > image upload > pasted URL
        $imageValue = $validated['image'] ?? null;
        if ($request->hasFile('video_file')) {
            $imageValue = $this->uploadVideoFile($request->file('video_file'));
        } elseif ($request->filled('video_from_library')) {
            // Admin selected an existing promo video URL from the library
            $imageValue = $request->input('video_from_library');
        } elseif ($request->hasFile('image_file')) {
            $imageValue = $this->uploadImageFile($request->file('image_file'));
        }

        $isActive      = $request->boolean('status') ? 1 : 0;
        $slotKey       = trim($validated['slot'] ?? '');
        $listingIdScope = isset($validated['listing_id_scope']) ? (int) $validated['listing_id_scope'] : null;

        $adId = $this->listocean()->table('advertisements')->insertGetId([
            'title'        => $validated['title'],
            'type'         => $validated['type'],
            'size'         => $validated['size'] ?? null,
            'slot'         => $slotKey ?: null,
            'embed_code'   => $validated['embed_code'] ?? null,
            'redirect_url' => $validated['redirect_url'] ?? null,
            'image'        => $imageValue,
            'status'       => $isActive,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Auto-assign to frontend_ad_slots if a named slot is provided
        if ($slotKey !== '') {
            $this->syncFrontendAdSlot($slotKey, (int) $adId, (bool) $isActive, $listingIdScope);
        }

        return to_route('admin.siteAdvertisement.index')->withSuccess(__('Advertisement created successfully'));
    }

    public function edit(int $id)
    {
        $advertisement = $this->listocean()->table('advertisements')->where('id', $id)->first();

        if (! $advertisement) {
            abort(404);
        }

        // Resolve numeric attachment IDs to their public URL
        $imageUrl    = $this->resolveImageUrl((string) ($advertisement->image ?? ''));
        $promoVideos = $this->getPromoVideos();

        // Load current listing_id scope from frontend_ad_slots (null = global / all listings)
        $currentListingId = null;
        if (Schema::connection('listocean')->hasTable('frontend_ad_slots')) {
            $slotRow = $this->listocean()->table('frontend_ad_slots')
                ->where('advertisement_id', $id)
                ->first();
            $currentListingId = $slotRow->listing_id ?? null;
        }

        return view('admin.site-advertisements.edit', compact('advertisement', 'imageUrl', 'promoVideos', 'currentListingId'));
    }

    /**
     * Resolve the stored advertisements.image value to a displayable URL.
     *
     * The field should always contain a numeric media_uploads ID (inserted by
     * uploadImageFile() or the ListOcean frontend media picker). Legacy rows
     * or externally-pasted values may occasionally contain a plain URL — those
     * are returned as-is so they still render in the admin preview.
     */
    private function resolveImageUrl(string $value): string
    {
        $value = trim($value);
        if ($value === '') return '';

        // Already an absolute URL (e.g. externally pasted, or a legacy upload)
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (ctype_digit($value)) {
            // Correct column is 'path', NOT 'image'
            $path = $this->listocean()
                ->table('media_uploads')
                ->where('id', (int) $value)
                ->value('path');

            if ($path) {
                // Use the ListOcean frontend base URL, not the admin APP_URL
                $baseUrl = rtrim(env('LISTOCEAN_APP_URL', config('app.url')), '/');
                return $baseUrl . '/assets/uploads/media-uploader/' . ltrim((string) $path, '/');
            }
        }

        return '';
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|in:image,embed_code,video',
            'size'            => 'nullable|string|max:100',
            'slot'            => 'nullable|string|max:255',
            'listing_id_scope'=> 'nullable|integer|min:1',
            'embed_code'      => 'nullable|string',
            'redirect_url'    => 'nullable|url|max:512',
            'image'           => 'nullable|string|max:512',
            'image_file'      => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'video_file'      => 'nullable|file|mimes:mp4,webm,ogg,mov,avi|max:102400',
            'status'          => 'nullable',
        ]);

        $exists = $this->listocean()->table('advertisements')->where('id', $id)->exists();
        if (! $exists) {
            abort(404);
        }

        // Determine media value: video upload > library pick > image upload > pasted URL
        $mediaExplicitlyProvided = false;
        $imageValue = $validated['image'] ?? null;
        if ($request->hasFile('video_file')) {
            $imageValue = $this->uploadVideoFile($request->file('video_file'));
            $mediaExplicitlyProvided = true;
        } elseif ($request->filled('video_from_library')) {
            $imageValue = $request->input('video_from_library');
            $mediaExplicitlyProvided = true;
        } elseif ($request->hasFile('image_file')) {
            $imageValue = $this->uploadImageFile($request->file('image_file'));
            $mediaExplicitlyProvided = true;
        } elseif ($imageValue !== null) {
            // A URL/path was explicitly pasted in the image field
            $mediaExplicitlyProvided = true;
        }

        // If no new media was provided, preserve the existing image/video URL.
        // This prevents clearing a saved video when re-saving without re-uploading.
        if (!$mediaExplicitlyProvided) {
            $existing = $this->listocean()->table('advertisements')->where('id', $id)->value('image');
            $imageValue = $existing;
        }

        $isActive       = $request->boolean('status') ? 1 : 0;
        $slotKey        = trim($validated['slot'] ?? '');
        $listingIdScope = isset($validated['listing_id_scope']) ? (int) $validated['listing_id_scope'] : null;

        $this->listocean()->table('advertisements')->where('id', $id)->update([
            'title'        => $validated['title'],
            'type'         => $validated['type'],
            'size'         => $validated['size'] ?? null,
            'slot'         => $slotKey ?: null,
            'embed_code'   => $validated['embed_code'] ?? null,
            'redirect_url' => $validated['redirect_url'] ?? null,
            'image'        => $imageValue,
            'status'       => $isActive,
            'updated_at'   => now(),
        ]);

        // Sync frontend_ad_slots for this ad.
        // Always wipe prior rows for this ad first — prevents stale slot assignments
        // accumulating when the admin changes the slot from one value to another.
        $this->listocean()->table('frontend_ad_slots')->where('advertisement_id', $id)->delete();

        if ($slotKey !== '') {
            $this->syncFrontendAdSlot($slotKey, $id, (bool) $isActive, $listingIdScope);
        }

        return to_route('admin.siteAdvertisement.index')->withSuccess(__('Advertisement updated successfully'));
    }

    public function toggleStatus(int $id)
    {
        $ad = $this->listocean()->table('advertisements')->where('id', $id)->first();
        if (! $ad) {
            abort(404);
        }

        $this->listocean()->table('advertisements')->where('id', $id)->update([
            'status'     => $ad->status ? 0 : 1,
            'updated_at' => now(),
        ]);

        return back()->withSuccess(__('Status updated'));
    }

    public function destroy(int $id)
    {
        $this->listocean()->table('advertisements')->where('id', $id)->delete();

        return back()->withSuccess(__('Advertisement deleted successfully'));
    }
}
