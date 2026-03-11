<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BannerAdRequestsController extends Controller
{
    private array $slotOptions = [
        'none' => 'None',
        'homepage_hero_banner' => 'Homepage (Hero banner)',
        'homepage_footer_banner' => 'Homepage (Footer banner)',
        'listing_details_left' => 'Listing details (Left)',
        'listing_details_right' => 'Listing details (Right)',
        'listing_details_under_gallery' => 'Listing details (Under images)',
        'user_profile_under_header' => 'User profile (Under header)',
        'listings_under_image' => 'Listings grid (Under listing image)',
    ];

    public function index(Request $request)
    {
        $status = (string) $request->get('status', 'pending'); // pending|approved|all

        if (!Schema::connection('listocean')->hasTable('advertisements') || !Schema::connection('listocean')->hasColumn('advertisements', 'user_id')) {
            $current = (int) ($request->get('page', 1) ?: 1);
            $ads = new LengthAwarePaginator([], 0, 15, $current, ['path' => $request->url(), 'query' => $request->query()]);
            return view('admin.banner-ad-requests.index', compact('ads', 'status'));
        }

        $query = $this->listocean()->table('advertisements as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.type',
                'a.size',
                'a.image',
                'a.redirect_url',
                'a.status',
                'a.created_at',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->whereNotNull('a.user_id')
            ->where('a.type', 'image')
            ->when($status === 'pending', fn ($b) => $b->where('a.status', 0))
            ->when($status === 'approved', fn ($b) => $b->where('a.status', 1))
            ->when($request->filled('search'), function ($b) use ($request) {
                $search = (string) $request->search;
                $b->where(function ($q) use ($search) {
                    $q->where('a.title', 'like', "%{$search}%")
                        ->orWhere('a.redirect_url', 'like', "%{$search}%")
                        ->orWhere('a.id', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('a.id');

        /** @var LengthAwarePaginator $ads */
        try {
            $ads = $query->paginate(15);
        } catch (\Throwable $e) {
            Log::error('BannerAdRequestsController list query failed', ['error' => $e->getMessage()]);
            $current = (int) ($request->get('page', 1) ?: 1);
            $ads = new LengthAwarePaginator([], 0, 15, $current, ['path' => $request->url(), 'query' => $request->query()]);
        }

        $adIds = $ads->getCollection()->pluck('id')->map(fn ($v) => (int) $v)->all();
        $placements = [];
        if (!empty($adIds)) {
            try {
                if (Schema::connection('listocean')->hasTable('reel_ad_placements')) {
                    $placements = $this->listocean()->table('reel_ad_placements')
                        ->whereIn('advertisement_id', $adIds)
                        ->orderByDesc('id')
                        ->get()
                        ->groupBy('advertisement_id')
                        ->toArray();
                }
            } catch (\Throwable $e) {
                Log::error('BannerAdRequestsController placements query failed', ['error' => $e->getMessage()]);
                $placements = [];
            }
        }

        $ads->setCollection(
            $ads->getCollection()->map(function ($row) use ($placements) {
                $row->user = (object) ['name' => $row->user_name ?? null];
                $row->placements = $placements[(int)$row->id] ?? [];
                return $row;
            })
        );

        return view('admin.banner-ad-requests.index', compact('ads', 'status'));
    }

    public function edit(int $id)
    {
        if (!Schema::connection('listocean')->hasTable('advertisements') || !Schema::connection('listocean')->hasColumn('advertisements', 'user_id')) {
            return redirect()->route('admin.bannerAdRequests.index')->withError('ListOcean advertisements schema is missing required columns.');
        }

        $ad = $this->listocean()->table('advertisements')
            ->where('id', $id)
            ->whereNotNull('user_id')
            ->first();

        if (! $ad) abort(404);

        $slot = null;
        if ($this->hasFrontendAdSlotsTable()) {
            $slot = $this->listocean()->table('frontend_ad_slots')
                ->select(['slot_key', 'listing_id'])
                ->where('advertisement_id', $id)
                ->first();
        }

        $currentSlotKey   = (string) ($slot->slot_key ?? 'none');
        if ($currentSlotKey === '') $currentSlotKey = 'none';
        $currentListingId = $slot->listing_id ?? null;

        $slotOptions = $this->slotOptions;

        return view('admin.banner-ad-requests.edit', compact('ad', 'currentSlotKey', 'currentListingId', 'slotOptions'));
    }

    public function update(Request $request, int $id)
    {
        if (!Schema::connection('listocean')->hasTable('advertisements') || !Schema::connection('listocean')->hasColumn('advertisements', 'user_id')) {
            return redirect()->route('admin.bannerAdRequests.index')->withError('ListOcean advertisements schema is missing required columns.');
        }

        $data = $request->validate([
            'slot_key'   => 'required|string',
            'listing_id' => 'nullable|integer|min:1',
        ]);

        $ad = $this->listocean()->table('advertisements')
            ->where('id', $id)
            ->whereNotNull('user_id')
            ->first();

        if (! $ad) return back()->withError('Ad not found');

        $slotKey   = (string) ($data['slot_key'] ?? 'none');
        $listingId = isset($data['listing_id']) && $data['listing_id'] ? (int) $data['listing_id'] : null;

        if (!array_key_exists($slotKey, $this->slotOptions)) {
            return back()->withError('Invalid slot');
        }

        if ($this->hasFrontendAdSlotsTable()) {
            $this->listocean()->table('frontend_ad_slots')->where('advertisement_id', $id)->delete();
        }

        if ($slotKey !== 'none' && $this->hasFrontendAdSlotsTable()) {
            // Allow multiple ads per listing-scoped slot; keep one-per-slot only for global (no listing).
            $slotStatus = (int) ($ad->status ?? 0) === 1 ? 1 : 0;
            if ($listingId) {
                $this->listocean()->table('frontend_ad_slots')->updateOrInsert(
                    ['slot_key' => $slotKey, 'listing_id' => $listingId],
                    [
                        'advertisement_id' => $id,
                        'status' => $slotStatus,
                        'start_at' => null,
                        'end_at' => null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            } else {
                $this->listocean()->table('frontend_ad_slots')->updateOrInsert(
                    ['slot_key' => $slotKey, 'listing_id' => null],
                    [
                        'advertisement_id' => $id,
                        'status' => $slotStatus,
                        'start_at' => null,
                        'end_at' => null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        return redirect()->route('admin.bannerAdRequests.index')->withSuccess('Placement updated');
    }

    public function approve(int $id)
    {
        if (!Schema::connection('listocean')->hasTable('advertisements') || !Schema::connection('listocean')->hasColumn('advertisements', 'user_id')) {
            return back()->withError('ListOcean advertisements schema is missing required columns.');
        }

        $ad = $this->listocean()->table('advertisements')->where('id', $id)->whereNotNull('user_id')->first();
        if (! $ad) return back()->withError('Ad not found');

        $this->listocean()->table('advertisements')->where('id', $id)->update([
            'status' => 1,
            'updated_at' => now(),
        ]);

        // Also activate any pending placement requests for this ad.
        if (Schema::connection('listocean')->hasTable('reel_ad_placements')) {
            $this->listocean()->table('reel_ad_placements')
                ->where('advertisement_id', $id)
                ->update([
                    'status' => 1,
                    'updated_at' => now(),
                ]);
        }

        $this->syncFrontendAdSlotStatus($id, 1);

        return back()->withSuccess('Banner ad approved and placement activated');
    }

    public function deactivate(int $id)
    {
        if (!Schema::connection('listocean')->hasTable('advertisements') || !Schema::connection('listocean')->hasColumn('advertisements', 'user_id')) {
            return back()->withError('ListOcean advertisements schema is missing required columns.');
        }

        $ad = $this->listocean()->table('advertisements')->where('id', $id)->whereNotNull('user_id')->first();
        if (! $ad) return back()->withError('Ad not found');

        $this->listocean()->table('advertisements')->where('id', $id)->update([
            'status' => 0,
            'updated_at' => now(),
        ]);

        if (Schema::connection('listocean')->hasTable('reel_ad_placements')) {
            $this->listocean()->table('reel_ad_placements')
                ->where('advertisement_id', $id)
                ->update([
                    'status' => 0,
                    'updated_at' => now(),
                ]);
        }

        $this->syncFrontendAdSlotStatus($id, 0);

        return back()->withSuccess('Banner ad deactivated');
    }

    private function hasFrontendAdSlotsTable(): bool
    {
        return Schema::connection('listocean')->hasTable('frontend_ad_slots');
    }

    private function syncFrontendAdSlotStatus(int $advertisementId, int $status): void
    {
        if (! $this->hasFrontendAdSlotsTable()) {
            return;
        }

        $this->listocean()->table('frontend_ad_slots')
            ->where('advertisement_id', $advertisementId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }
}
