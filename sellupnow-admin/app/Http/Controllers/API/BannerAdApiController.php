<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerAdApiController extends Controller
{
    private array $slotOptions = [
        'homepage_hero_banner'           => 'Homepage (Hero banner)',
        'homepage_footer_banner'         => 'Homepage (Footer banner)',
        'listing_details_left'           => 'Listing details (Left)',
        'listing_details_right'          => 'Listing details (Right)',
        'listing_details_under_gallery'  => 'Listing details (Under images)',
        'user_profile_under_header'      => 'User profile (Under header)',
        'listings_under_image'           => 'Listings grid (Under listing image)',
    ];

    /**
     * GET /api/client/bannerAd/getMyBannerAds?page=1&limit=20
     * Returns the authenticated user's banner ad requests.
     */
    public function getMyBannerAds(Request $request)
    {
        $user  = $request->user();
        $page  = max(1, (int) $request->query('page', 1));
        $limit = min(50, (int) $request->query('limit', 20));

        $query = DB::connection('listocean')
            ->table('advertisements')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        $total = $query->count();
        $ads   = $query->offset(($page - 1) * $limit)->limit($limit)
            ->get(['id', 'title', 'requested_slot', 'redirect_url', 'status', 'created_at', 'image']);

        // Map status int to label
        $ads = $ads->map(function ($ad) {
            $ad->status_label = match ((int) $ad->status) {
                1       => 'approved',
                2       => 'rejected',
                default => 'pending',
            };
            $ad->slot_label = $this->slotOptions[$ad->requested_slot] ?? $ad->requested_slot ?? '—';
            return $ad;
        });

        return response()->json([
            'status' => true,
            'total'  => $total,
            'page'   => $page,
            'limit'  => $limit,
            'data'   => $ads,
            'slots'  => $this->slotOptions,
        ]);
    }

    /**
     * POST /api/client/bannerAd/submitBannerAd
     * Submit a new banner ad request.
     * Body: title, requested_slot, redirect_url
     */
    public function submitBannerAd(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'title'          => 'required|string|max:191',
            'requested_slot' => 'required|string|in:' . implode(',', array_keys($this->slotOptions)),
            'redirect_url'   => 'required|url|max:2048',
            'image'          => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $bannerQuota = $this->getBannerAdQuota((int) $user->id);
        if ($bannerQuota === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Your current membership plan does not include banner ad requests. Please upgrade your plan.',
            ], 403);
        }

        $bannerUsed = DB::connection('listocean')->table('advertisements')
            ->where('user_id', $user->id)
            ->count();

        if ($bannerQuota > 0 && $bannerUsed >= $bannerQuota) {
            return response()->json([
                'status' => false,
                'message' => "You have reached the banner ad request limit for your plan ({$bannerUsed}/{$bannerQuota}).",
            ], 403);
        }

        $path = Storage::disk('public')->putFile('banner-ads/' . $user->id, $request->file('image'));
        $imageUrl = Storage::disk('public')->url($path);

        $id = DB::connection('listocean')->table('advertisements')->insertGetId([
            'user_id'        => $user->id,
            'title'          => $request->input('title'),
            'type'           => 'image',
            'image'          => $imageUrl,
            'requested_slot' => $request->input('requested_slot'),
            'redirect_url'   => $request->input('redirect_url'),
            'status'         => 0,
            'click'          => 0,
            'impression'     => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Banner ad submitted for review.',
            'id'      => $id,
        ], 201);
    }

    private function getBannerAdQuota(int $userId): int
    {
        $membership = DB::table('user_memberships')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            })
            ->orderByDesc('id')
            ->first();

        if (! $membership) {
            return 0;
        }

        return (int) (DB::table('membership_plans')
            ->where('id', $membership->membership_id)
            ->value('banner_ad_quota') ?? 0);
    }
}
