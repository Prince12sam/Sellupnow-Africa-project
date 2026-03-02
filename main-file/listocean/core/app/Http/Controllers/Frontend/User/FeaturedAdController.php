<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Backend\Listing;
use App\Models\Frontend\FeaturedAdPackage;
use App\Models\Frontend\UserMembership;
use App\Services\FeaturedAdService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeaturedAdController extends Controller
{
    public function __construct(
        private FeaturedAdService $featuredAdService,
        private WalletService $walletService,
    ) {}

    /**
     * My featured-ad history / active slots.
     */
    public function index(): View
    {
        $userId    = Auth::guard('web')->id();
        $purchases = $this->featuredAdService->getUserPurchases($userId);
        $balance   = $this->walletService->balance($userId);

        return view('frontend.user.featured-ads.index', compact('purchases', 'balance'));
    }

    /**
     * Available packages — optionally pre-select a listing.
     */
    public function packages(Request $request): View
    {
        $userId   = Auth::guard('web')->id();
        $packages = $this->featuredAdService->getActivePackages();
        $balance  = $this->walletService->balance($userId);

        // Load user's active listings for the listing selector
        $listings = Listing::where('user_id', $userId)
            ->where('status', 1)
            ->where('is_published', 1)
            ->select('id', 'title', 'slug')
            ->latest()
            ->get();

        $selectedListingId = (int) $request->query('listing_id', 0);

        // Membership featured listing credits remaining
        $membershipCredits = 0;
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
            $userMembership = UserMembership::where('user_id', $userId)
                ->where('status', 1)
                ->first();
            $membershipCredits = $userMembership ? max(0, (int) $userMembership->featured_listing) : 0;
        }

        return view('frontend.user.featured-ads.packages', compact(
            'packages', 'balance', 'listings', 'selectedListingId', 'membershipCredits'
        ));
    }

    /**
     * Process the purchase.
     */
    public function purchase(Request $request): RedirectResponse
    {
        $request->validate([
            'package_id' => ['required', 'integer', 'min:1'],
            'listing_id' => ['required', 'integer', 'min:1'],
        ]);

        $userId     = Auth::guard('web')->id();
        $packageId  = (int) $request->input('package_id');
        $listingId  = (int) $request->input('listing_id');

        // Verify listing belongs to the user
        $listing = Listing::where('id', $listingId)->where('user_id', $userId)->first();
        if (!$listing) {
            return back()->with('error', 'Listing not found or does not belong to you.');
        }

        try {
            $this->featuredAdService->purchase($userId, $packageId, $listingId);
            return redirect()->route('user.featuredAds.index')
                ->with('success', 'Your listing has been featured successfully!');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
