<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class ListoceanReelAdPlacementController extends Controller
{
    private const ALLOWED_REEL_TYPES = ['listing', 'ad_video'];
    private const ALLOWED_PLACEMENTS = ['bottom_overlay', 'bottom_overlay_2', 'listing_detail_video'];

    private function hasReelPlacementsTable(): bool
    {
        return Schema::connection('listocean')->hasTable('reel_ad_placements');
    }

    private function hasAdVideosTable(): bool
    {
        return Schema::connection('listocean')->hasTable('ad_videos');
    }

    public function index(Request $request)
    {
        $db = DB::connection('listocean');

        if (! $this->hasReelPlacementsTable()) {
            $current = (int) ($request->get('page', 1) ?: 1);
            $placements = new LengthAwarePaginator([], 0, 50, $current, ['path' => $request->url(), 'query' => $request->query()]);
            return view('admin.listocean.reel-ad-placements', [
                'placements'        => $placements,
                'ads'               => collect([]),
                'adPreviewData'     => [],
                'adsById'           => [],
                'listingTitlesById' => [],
                'promoCaptionsById' => [],
                'allowedReelTypes'  => self::ALLOWED_REEL_TYPES,
                'allowedPlacements' => self::ALLOWED_PLACEMENTS,
                'editing'           => null,
            ]);
        }

        try {
            $placements = $db->table('reel_ad_placements')
                ->orderByDesc('id')
                ->paginate(50)
                ->withQueryString();

            $ads = $db->table('advertisements')
                ->select(['id', 'title', 'type', 'redirect_url', 'status', 'image', 'description', 'size'])
                ->orderByDesc('id')
                ->limit(500)
                ->get();

            // Resolve image attachment IDs → full URLs for the ad picker preview
            $adPreviewData = [];
            foreach ($ads as $ad) {
                $adPreviewData[(int) $ad->id] = [
                    'id'           => (int) $ad->id,
                    'title'        => $ad->title ?: '(no title)',
                    'type'         => $ad->type ?? '',
                    'size'         => $ad->size ?? '',
                    'redirect_url' => $ad->redirect_url ?? '',
                    'description'  => $ad->description ?? '',
                    'status'       => (int) ($ad->status ?? 0),
                    'image_url'    => $this->resolveAdImageUrl($db, $ad->image),
                ];
            }

            $listingIds = [];
            $promoIds = [];
            $adIds = [];

            foreach ($placements->items() as $p) {
                $adIds[] = (int) ($p->advertisement_id ?? 0);
                if (($p->reel_type ?? '') === 'listing') {
                    $listingIds[] = (int) ($p->reel_id ?? 0);
                }
                if (($p->reel_type ?? '') === 'ad_video') {
                    $promoIds[] = (int) ($p->reel_id ?? 0);
                }
            }

            $listingTitlesById = empty($listingIds)
                ? []
                : $db->table('listings')
                    ->whereIn('id', array_values(array_unique(array_filter($listingIds))))
                    ->pluck('title', 'id')
                    ->map(fn ($v) => (string) $v)
                    ->all();

            $promoCaptionsById = empty($promoIds) || ! $this->hasAdVideosTable()
                ? []
                : $db->table('ad_videos')
                    ->whereIn('id', array_values(array_unique(array_filter($promoIds))))
                    ->pluck('caption', 'id')
                    ->map(fn ($v) => (string) $v)
                    ->all();

            $adsById = empty($adIds)
                ? []
                : $db->table('advertisements')
                    ->select(['id', 'title', 'type', 'redirect_url', 'status', 'image', 'size'])
                    ->whereIn('id', array_values(array_unique(array_filter($adIds))))
                    ->get()
                    ->keyBy('id');

            // Attach resolved image URLs to adsById for the table rows
            foreach ($adsById as $adId => $adRow) {
                $adsById[$adId]->image_url = $this->resolveAdImageUrl($db, $adRow->image ?? null);
            }

        } catch (\Throwable $e) {
            Log::error('ListoceanReelAdPlacementController list failed', ['error' => $e->getMessage()]);
            $current = (int) ($request->get('page', 1) ?: 1);
            $placements = new LengthAwarePaginator([], 0, 50, $current, ['path' => $request->url(), 'query' => $request->query()]);
            $ads = collect([]);
            $adPreviewData = [];
            $listingTitlesById = [];
            $promoCaptionsById = [];
            $adsById = [];
        }

        $editId = (int) $request->query('edit');
        $editing = null;
        if ($editId > 0) {
            try {
                $editing = $db->table('reel_ad_placements')->where('id', $editId)->first();
            } catch (\Throwable $e) {
                Log::error('ListoceanReelAdPlacementController fetch edit failed', ['error' => $e->getMessage()]);
                $editing = null;
            }
        }

        return view('admin.listocean.reel-ad-placements', [
            'placements'         => $placements,
            'ads'                => $ads,
            'adPreviewData'      => $adPreviewData,
            'adsById'            => $adsById,
            'listingTitlesById'  => $listingTitlesById,
            'promoCaptionsById'  => $promoCaptionsById,
            'allowedReelTypes'   => self::ALLOWED_REEL_TYPES,
            'allowedPlacements'  => self::ALLOWED_PLACEMENTS,
            'editing'            => $editing,
        ]);
    }

    /**
     * Resolve a numeric attachment ID (stored in advertisements.image)
     * to the full public URL served by the frontend.
     */
    private function resolveAdImageUrl($db, $imageId): ?string
    {
        if ($imageId === null || $imageId === '' || $imageId === '0') {
            return null;
        }

        $id = is_int($imageId) ? $imageId : trim((string) $imageId);
        if (! (is_int($id) || (is_string($id) && ctype_digit($id)))) {
            // Treat as a direct path string
            $customerWebUrl = rtrim((string) config('app.customer_web_url', ''), '/');
            return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim((string) $imageId, '/');
        }

        try {
            $path = $db->table('media_uploads')->where('id', (int) $id)->value('path');
        } catch (\Throwable) {
            $path = null;
        }

        if (! $path) {
            return null;
        }

        $customerWebUrl = rtrim((string) config('app.customer_web_url', ''), '/');
        return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim((string) $path, '/');
    }

    public function store(Request $request)
    {
        if (! $this->hasReelPlacementsTable()) {
            return back()->withInput()->withError(__('ListOcean table reel_ad_placements is missing.'));
        }

        $data = $this->validatePayload($request);

        $db = DB::connection('listocean');

        $exists = $db->table('reel_ad_placements')
            ->where('reel_type', $data['reel_type'])
            ->where('reel_id', $data['reel_id'])
            ->where('placement', $data['placement'])
            ->first();

        if ($exists) {
            $db->table('reel_ad_placements')->where('id', (int) $exists->id)->update([
                'advertisement_id' => $data['advertisement_id'],
                'status' => $data['status'],
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'updated_at' => now(),
            ]);

            return back()->withSuccess(__('Placement updated (existing reel target).'));
        }

        $db->table('reel_ad_placements')->insert([
            'reel_type' => $data['reel_type'],
            'reel_id' => $data['reel_id'],
            'advertisement_id' => $data['advertisement_id'],
            'placement' => $data['placement'],
            'status' => $data['status'],
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->withSuccess(__('Placement created successfully.'));
    }

    public function update(Request $request, int $id)
    {
        if (! $this->hasReelPlacementsTable()) {
            return back()->withInput()->withError(__('ListOcean table reel_ad_placements is missing.'));
        }

        $id = (int) $id;
        if ($id <= 0) {
            return back()->withInput()->withErrors(['id' => __('Invalid placement id')]);
        }

        $data = $this->validatePayload($request);
        $db = DB::connection('listocean');

        $row = $db->table('reel_ad_placements')->where('id', $id)->first();
        if (!$row) {
            return back()->withInput()->withErrors(['id' => __('Placement not found')]);
        }

        $conflict = $db->table('reel_ad_placements')
            ->where('id', '!=', $id)
            ->where('reel_type', $data['reel_type'])
            ->where('reel_id', $data['reel_id'])
            ->where('placement', $data['placement'])
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors([
                'reel_id' => __('A placement already exists for this reel (type + id + placement combination). Edit that row instead.'),
            ]);
        }

        $db->table('reel_ad_placements')->where('id', $id)->update([
            'reel_type' => $data['reel_type'],
            'reel_id' => $data['reel_id'],
            'advertisement_id' => $data['advertisement_id'],
            'placement' => $data['placement'],
            'status' => $data['status'],
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.reelAdPlacement.index')->withSuccess(__('Placement updated successfully.'));
    }

    public function destroy(int $id)
    {
        if (! $this->hasReelPlacementsTable()) {
            return back()->withError(__('ListOcean table reel_ad_placements is missing.'));
        }

        $id = (int) $id;
        if ($id <= 0) {
            return back()->withErrors(['id' => __('Invalid placement id')]);
        }

        DB::connection('listocean')->table('reel_ad_placements')->where('id', $id)->delete();

        return back()->withSuccess(__('Placement deleted.'));
    }

    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'reel_type' => ['required', 'string', 'in:' . implode(',', self::ALLOWED_REEL_TYPES)],
            'reel_id' => ['required', 'integer', 'min:1'],
            'advertisement_id' => ['required', 'integer', 'min:1'],
            'placement' => ['required', 'string', 'in:' . implode(',', self::ALLOWED_PLACEMENTS)],
            'status' => ['nullable'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
        ]);

        $startAt = !empty($validated['start_at']) ? Carbon::parse($validated['start_at']) : null;
        $endAt = !empty($validated['end_at']) ? Carbon::parse($validated['end_at']) : null;

        return [
            'reel_type' => (string) $validated['reel_type'],
            'reel_id' => (int) $validated['reel_id'],
            'advertisement_id' => (int) $validated['advertisement_id'],
            'placement' => (string) $validated['placement'],
            'status' => $request->boolean('status') ? 1 : 0,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ];
    }
}
