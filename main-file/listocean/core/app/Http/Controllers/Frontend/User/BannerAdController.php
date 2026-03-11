<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Backend\Advertisement;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerAdController extends Controller
{
    private array $slots = [
        'homepage_hero_banner'         => 'Homepage — Hero Banner (Full width)',
        'homepage_footer_banner'       => 'Homepage — Footer Banner (Wide)',
        'listing_details_left'         => 'Listing Detail — Sidebar Left (300×250)',
        'listing_details_under_gallery' => 'Listing Detail — Under Gallery (728×90)',
        'listing_details_right'        => 'Listing Detail — Sidebar Right (300×250)',
        'user_profile_under_header'    => 'User Profile — Under Header (Full width)',
        'listings_under_image'         => 'Listings Grid — Under Image',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'userEmailVerify', 'globalVariable', 'maintains_mode', 'setlang']);
    }

    /**
     * GET /user/banner-ads
     */
    public function index()
    {
        $ads = Advertisement::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(10);

        return view('frontend.user.banner-ads.index', compact('ads'));
    }

    /**
     * GET /user/banner-ads/request
     */
    public function create(MembershipService $membership)
    {
        $slots       = $this->slots;
        $userId      = Auth::id();
        $bannerQuota = $membership->getBannerAdQuota($userId);
        $bannerUsed  = Advertisement::where('user_id', $userId)->count();
        return view('frontend.user.banner-ads.request', compact('slots', 'bannerQuota', 'bannerUsed'));
    }

    /**
     * POST /user/banner-ads/request
     */
    public function store(Request $request, MembershipService $membership)
    {
        $request->validate([
            'title'          => 'required|string|max:191',
            'image'          => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'redirect_url'   => 'required|url|max:500',
            'requested_slot' => 'required|string|in:' . implode(',', array_keys($this->slots)),
        ]);

        // Quota enforcement
        $userId      = Auth::id();
        $bannerQuota = $membership->getBannerAdQuota($userId);
        if ($bannerQuota === 0) {
            return back()->withErrors(['quota' => __('Your current membership plan does not include banner ad requests. Please upgrade your plan.')]);
        }
        if ($bannerQuota > 0) {
            $bannerUsed = Advertisement::where('user_id', $userId)->count();
            if ($bannerUsed >= $bannerQuota) {
                return back()->withErrors(['quota' => __('You have reached the banner ad request limit for your plan (:used/:quota).', ['used' => $bannerUsed, 'quota' => $bannerQuota])]);
            }
        }

        $path = $request->file('image')->store('banner-ads/' . $userId, 'public');

        Advertisement::create([
            'user_id'        => $userId,
            'title'          => $request->title,
            'type'           => 'image',
            'image'          => $path,
            'redirect_url'   => $request->redirect_url,
            'requested_slot' => $request->requested_slot,
            'status'         => 0, // pending admin approval
            'click'          => 0,
            'impression'     => 0,
        ]);

        return redirect()->route('user.banner-ads.index')
            ->with('success', __('Your banner ad request has been submitted. We will review it within 24 hours.'));
    }
}
