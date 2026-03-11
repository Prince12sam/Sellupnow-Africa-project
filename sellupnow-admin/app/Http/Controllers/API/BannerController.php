<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Http\Resources\BannerResource;
use App\Repositories\BannerRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class BannerController extends Controller
{
    /**
     * Get all banners
     */
    public function index(Request $request)
    {
        $hasPlacementFilter = $request->has('placement');
        $placement = (string) $request->query('placement', Banner::PLACEMENT_HOMEPAGE);
        $allowedPlacements = array_keys(Banner::placementOptions());
        if ($hasPlacementFilter && !in_array($placement, $allowedPlacements, true)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid placement value.',
                'data' => [],
            ], 422);
        }

        $query = $this->baseBannerQuery();

        if ($hasPlacementFilter) {
            if ($placement === Banner::PLACEMENT_HOMEPAGE) {
                $query->where(function ($builder) {
                    $builder
                        ->where('placement', Banner::PLACEMENT_HOMEPAGE)
                        ->orWhereNull('placement');
                });
            } else {
                $query->where('placement', $placement);
            }

            $banners = $query->latest('id')->get();

            if ($banners->isEmpty() && $placement === Banner::PLACEMENT_MOBILE_AFTER_LIVE_AUCTION) {
                $banners = $this->baseBannerQuery()
                    ->where(function ($builder) {
                        $builder
                            ->where('placement', Banner::PLACEMENT_HOMEPAGE)
                            ->orWhereNull('placement');
                    })
                    ->latest('id')
                    ->get();
            }
        } else {
            // Legacy mobile builds do not send placement. First try homepage banners,
            // then safely fallback to mobile-after-live-auction if homepage is empty.
            $homepageBanners = (clone $query)->where(function ($builder) {
                $builder
                    ->where('placement', Banner::PLACEMENT_HOMEPAGE)
                    ->orWhereNull('placement');
            })->latest('id')->get();

            $banners = $homepageBanners->isNotEmpty()
                ? $homepageBanners
                : (clone $query)
                    ->where('placement', Banner::PLACEMENT_MOBILE_AFTER_LIVE_AUCTION)
                    ->latest('id')
                    ->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'all banners',
            'data' => BannerResource::collection($banners)->toArray($request),
        ]);
    }

    private function baseBannerQuery(): Builder
    {
        $rootShopId = generaleSetting('rootShop')?->id;

        return BannerRepository::query()
            ->active()
            ->where(function (Builder $builder) use ($rootShopId) {
                $builder->whereNull('shop_id');

                if ($rootShopId) {
                    $builder->orWhere('shop_id', $rootShopId);
                }
            });
    }
}
