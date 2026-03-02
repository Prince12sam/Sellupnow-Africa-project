<!--- Dashboard --->
<li>
    <a class="menu {{ $request->routeIs('admin.dashboard.*') ? 'active' : '' }}"
        href="{{ route('admin.dashboard.index') }}">
        <span>
            <img class="menu-icon" src="{{ asset('assets/icons-admin/overview.svg') }}" alt="icon" loading="lazy" />
            {{ __('Overview') }}
        </span>
    </a>
</li>

@php
   use \Nwidart\Modules\Facades\Module;
    $shopPanelEnabled = false;
    $commerceModulesEnabled = (bool) env('ENABLE_COMMERCE_MODULES', false);

    $isRootUser = false;
    if (auth()->check()) {
        try {
            $isRootUser = auth()->user()->getRoleNames()->contains('root');
        } catch (\Throwable $th) {
            $isRootUser = false;
        }
    }
@endphp


@if ($shopPanelEnabled)
@hasPermission(['shop.pos.index', 'shop.pos.draft', 'shop.pos.sales'])
    <li class="menu-divider">
        <span class="menu-title">{{ __('Point Of Sale') }}</span>
        <div class="devider_line"></div>
    </li>

    <li>
        <a class="menu {{ request()->routeIs('shop.pos.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#posMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/pos.svg') }}" alt="icon" loading="lazy" />
                {{ __('POS') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.pos.*') ? 'show' : '' }}" id="posMenu">
            <div class="listBar">
                @hasPermission('shop.pos.index')
                    <a href="{{ route('shop.pos.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.pos.index') ? 'active' : '' }}">
                        {{ __('Sale Offline') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.pos.sales')
                    <a href="{{ route('shop.pos.sales') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.pos.sales') ? 'active' : '' }}">
                        {{ __('Offline Sales') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.pos.draft')
                    <a href="{{ route('shop.pos.draft') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.pos.draft') ? 'active' : '' }}">
                        {{ __('Drafts Orders') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@endif


@hasPermission(['admin.category.index', 'admin.product.index', 'admin.listingModeration.index', 'admin.videoModeration.index'])
    <li class="menu-divider">
        <span class="menu-title">{{ __('All Listings') }}</span>
        <div class="devider_line"></div>
    </li>
@endhasPermission

@hasPermission(['admin.product.index', 'admin.listingModeration.index', 'admin.videoModeration.index'])
    <!--- Products--->
    <li>
        <a class="menu {{ request()->routeIs('admin.product.*', 'admin.listingModeration.*', 'admin.videoModeration.*', 'admin.escrow.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#productMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/product.svg') }}" alt="icon" loading="lazy" />
                {{ __('All Listings') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.product.*', 'admin.listingModeration.*', 'admin.videoModeration.*', 'admin.escrow.*') ? 'show' : '' }}"
            id="productMenu">
            <div class="listBar">
                @hasPermission(['admin.listingModeration.index', 'admin.videoModeration.index'])
                    <a href="{{ route('admin.listingModeration.index') }}"
                        class="subMenu {{ $request->routeIs('admin.listingModeration.*') && request('queue','all') === 'all' ? 'active' : '' }}">
                        {{ __('All Listings') }}
                    </a>
                    <a href="{{ route('admin.listingModeration.index', ['queue' => 'new']) }}"
                        class="subMenu {{ $request->routeIs('admin.listingModeration.*') && request('queue') === 'new' ? 'active' : '' }}"
                        title="{{ __('New Listing Request') }}">
                        {{ __('New Listing Request') }}
                    </a>
                    <a href="{{ route('admin.listingModeration.index', ['queue' => 'update']) }}"
                        class="subMenu {{ $request->routeIs('admin.listingModeration.*') && request('queue') === 'update' ? 'active' : '' }}"
                        title="{{ __('Update Listing Request') }}">
                        {{ __('Update Listing Request') }}
                    </a>
                    <a href="{{ route('admin.listingModeration.index', ['queue' => 'removed']) }}"
                        class="subMenu {{ $request->routeIs('admin.listingModeration.*') && request('queue') === 'removed' ? 'active' : '' }}"
                        title="{{ __('Removed Listings') }}">
                        {{ __('Removed Listings') }}
                    </a>

                    <a href="{{ route('admin.videoModeration.index') }}"
                        class="subMenu {{ $request->routeIs('admin.videoModeration.*') ? 'active' : '' }}">
                        {{ __('Trending Videos') }}
                    </a>

                    @hasPermission('admin.videoModeration.store')
                    <a href="{{ route('admin.videoModeration.create') }}"
                        class="subMenu ps-4 {{ $request->routeIs('admin.videoModeration.create') ? 'active' : '' }}">
                        + {{ __('Add Video') }}
                    </a>
                    @endhasPermission

                    <a href="{{ route('admin.escrow.index') }}"
                        class="subMenu {{ $request->routeIs('admin.escrow.*') ? 'active' : '' }}">
                        {{ __('Escrow Transactions') }}
                    </a>
                    <a href="{{ route('admin.escrow.settings') }}"
                        class="subMenu {{ request()->routeIs('admin.escrow.settings') ? 'active' : '' }}">
                        {{ __('Escrow Settings') }}
                    </a>
                @endhasPermission

            </div>
        </div>
    </li>
@endhasPermission



@if (module_exists('purchase') )
    @include('purchase::layouts.purchaseSidebar')
@endif

@hasPermission('admin.category.index')
    <!--- categories--->
    <li>
        <a class="menu {{ $request->routeIs('admin.category.*') ? 'active' : '' }}"
            href="{{ route('admin.category.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/category.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Categories') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission(['admin.categoryAttribute.index'])
    <li>
        <a class="menu {{ $request->routeIs('admin.categoryAttribute.*') ? 'active' : '' }}"
            href="{{ route('admin.categoryAttribute.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/attribute.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Attributes') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission('admin.brand.index')
    <!--- Brands --->
    <li>
        <a class="menu {{ $request->routeIs('admin.brand.*') ? 'active' : '' }}"
            href="{{ route('admin.brand.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/category.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Brands') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission(['admin.siteCountry.index', 'admin.siteState.index', 'admin.siteCity.index'])
    <li>
        <a class="menu {{ request()->routeIs('admin.siteCountry.*', 'admin.siteState.*', 'admin.siteCity.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#locationMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/country.svg') }}" alt="icon" loading="lazy" />
                {{ __('Listing Locations') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ request()->routeIs('admin.siteCountry.*', 'admin.siteState.*', 'admin.siteCity.*') ? 'show' : '' }}"
            id="locationMenu">
            <div class="listBar">
                @hasPermission('admin.siteCountry.index')
                    <a href="{{ route('admin.siteCountry.index') }}"
                        class="subMenu {{ request()->routeIs('admin.siteCountry.*') ? 'active' : '' }}">
                        {{ __('Countries') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.siteState.index')
                    <a href="{{ route('admin.siteState.index') }}"
                        class="subMenu {{ request()->routeIs('admin.siteState.*') ? 'active' : '' }}">
                        {{ __('Cities') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.siteCity.index')
                    <a href="{{ route('admin.siteCity.index') }}"
                        class="subMenu {{ request()->routeIs('admin.siteCity.*') ? 'active' : '' }}">
                        {{ __('Towns') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission










@hasPermission([
    'admin.banner.index',
    'admin.bannerAdRequests.index',
    'admin.reelAdPlacement.index',
    'admin.siteAdvertisement.index',
    'admin.promoVideoAds.index',
    'admin.featuredAdPackage.index',
    'admin.featuredAdReport.purchases',
    'admin.featuredAdReport.activations',
])
    <!--- Ad Management (unified) --->
    <li>
        <a class="menu {{ request()->routeIs('admin.banner.*', 'admin.bannerAdRequests.*', 'admin.siteAdvertisement.*', 'admin.reelAdPlacement.*', 'admin.promoVideoAds.*', 'admin.featuredAdPackage.*', 'admin.featuredAdReport.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#adMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/ads.svg') }}" alt="icon" loading="lazy" />
                {{ __('Ad Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.banner.*', 'admin.bannerAdRequests.*', 'admin.siteAdvertisement.*', 'admin.reelAdPlacement.*', 'admin.promoVideoAds.*', 'admin.featuredAdPackage.*', 'admin.featuredAdReport.*', 'admin.adsHub.*') ? 'show' : '' }}"
            id="adMenu">
            <div class="listBar">

                {{-- Ads Hub removed: admin DB ad dashboard, zero data --}}

                {{-- ── Paid placements ───────────────────────── --}}
                @hasPermission('admin.bannerAdRequests.index')
                    <a href="{{ route('admin.bannerAdRequests.index') }}"
                        class="subMenu {{ request()->routeIs('admin.bannerAdRequests.*') ? 'active' : '' }}">
                        {{ __('Banner Ad Requests') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.siteAdvertisement.index')
                    <a href="{{ route('admin.siteAdvertisement.index') }}"
                        class="subMenu {{ request()->routeIs('admin.siteAdvertisement.*') ? 'active' : '' }}">
                        {{ __('Site Advertisements') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.promoVideoAds.index')
                    <a href="{{ route('admin.promoVideoAds.index') }}"
                        class="subMenu {{ request()->routeIs('admin.promoVideoAds.*') ? 'active' : '' }}">
                        {{ __('Promo Video Ads') }}
                    </a>
                    @hasPermission('admin.promoVideoAds.store')
                    <a href="{{ route('admin.promoVideoAds.create') }}"
                        class="subMenu ps-4 {{ request()->routeIs('admin.promoVideoAds.create') ? 'active' : '' }}">
                        + {{ __('Create Promo Video') }}
                    </a>
                    @endhasPermission
                @endhasPermission

                @hasPermission('admin.reelAdPlacement.index')
                    <a href="{{ route('admin.reelAdPlacement.index') }}"
                        class="subMenu {{ request()->routeIs('admin.reelAdPlacement.*') ? 'active' : '' }}">
                        {{ __('Reel Ad Placements') }}
                    </a>
                @endhasPermission

                {{-- ── Featured ad system ────────────────────── --}}
                @hasPermission('admin.featuredAdPackage.index')
                    <a href="{{ route('admin.featuredAdPackage.index') }}"
                        class="subMenu {{ request()->routeIs('admin.featuredAdPackage.*') ? 'active' : '' }}">
                        {{ __('Featured Packages') }}
                    </a>
                @endhasPermission

                {{-- Commission Rules removed: admin DB, frontend CommissionService ignores it (uses static_option fallback) --}}
                {{-- Listing Boosts removed: admin DB, zero boost records anywhere --}}

                @hasPermission('admin.featuredAdReport.purchases')
                    <a href="{{ route('admin.featuredAdReport.purchases') }}"
                        class="subMenu {{ request()->routeIs('admin.featuredAdReport.purchases') ? 'active' : '' }}">
                        {{ __('Featured Purchases') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.featuredAdReport.activations')
                    <a href="{{ route('admin.featuredAdReport.activations') }}"
                        class="subMenu {{ request()->routeIs('admin.featuredAdReport.activations') ? 'active' : '' }}">
                        {{ __('Featured Activations') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.banner.index')
                    <a href="{{ route('admin.banner.index') }}"
                        class="subMenu {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}">
                        {{ __('Hero Banners') }}
                    </a>
                @endhasPermission

            </div>
        </div>
    </li>
@endhasPermission

@hasPermission('admin.customerNotification.index')
    <!--- notification--->
    <li>
        <a class="menu {{ $request->routeIs('admin.customerNotification.*') ? 'active' : '' }}"
            href="{{ route('admin.customerNotification.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/notification.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Push Notification') }}
            </span>
        </a>
    </li>
@endhasPermission
{{-- Blog removed: uses admin DB (blogs/tags tables), not connected to listocean_db --}}
@if ($commerceModulesEnabled && $businessModel == 'multi')
    @hasPermission([
        'admin.shop.index',
        'admin.shop.create',
        'admin.subscription-plan.index',
        'admin.subscription-plan.create'
    ])
        <li class="menu-divider">
            <span class="menu-title">{{ __('Vendor management') }}</span>
            <div class="devider_line"></div>
        </li>
    @endhasPermission

    @hasPermission(['admin.shop.index', 'admin.shop.create'])
        <!--- shop management--->
        <li>
            <a class="menu {{ request()->routeIs('admin.shop.*', 'admin.withdraw.index') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#shopMenu">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/vendor.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Vendors') }}
                </span>
                <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.shop.*', 'admin.withdraw.index') ? 'show' : '' }}"
                id="shopMenu">
                <div class="listBar">

                    @hasPermission('admin.shop.index')
                        <a href="{{ route('admin.shop.index') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.shop.index') ? 'active' : '' }}">
                            {{ __('List Of Vendors') }}
                        </a>
                    @endhasPermission
                    @hasPermission('admin.shop.create')
                        <a href="{{ route('admin.shop.create') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.shop.create') ? 'active' : '' }}">
                            {{ __('Add Vendor') }}
                        </a>
                    @endhasPermission
                    @hasPermission('admin.withdraw.index')
                        <a href="{{ route('admin.withdraw.index') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.withdraw.*') ? 'active' : '' }}">
                            {{ __('Withdraw Request') }}
                        </a>
                    @endhasPermission
                </div>
            </div>
        </li>
    @endhasPermission

    @hasPermission(['admin.subscription-plan.index', 'admin.subscription-plan.create'])
        <!--- subscription plans --->
        <li>
            <a class="menu {{ request()->routeIs('admin.subscription-plan.*') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#subscriptionMenu">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/crown.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Subscription') }}
                </span>
                <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.subscription-plan.*') ? 'show' : '' }}"
                id="subscriptionMenu">
                <div class="listBar">
                    @hasPermission('admin.subscription-plan.index')
                        <a href="{{ route('admin.subscription-plan.index') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.subscription-plan.index') ? 'active' : '' }}">
                            {{ __('List Of Plans') }}
                        </a>
                    @endhasPermission
                    @hasPermission('admin.employee.create')
                        <a href="{{ route('admin.subscription-plan.create') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.subscription-plan.create') ? 'active' : '' }}">
                            {{ __('Add New Plan') }}
                        </a>
                    @endhasPermission
                </div>
            </div>
        </li>
    @endhasPermission
@endif

@hasPermission([
    'admin.listingReport.index',
    'admin.reportReason.index',
    'admin.supportTicket.index',
    'admin.support.index'
])
    <!--- Conversations --->
    <li class="menu-divider">
        <span class="menu-title">{{ __('Messages') }}</span>
        <div class="devider_line"></div>
    </li>
@endhasPermission

@hasPermission('admin.listingReport.index')
    <li>
        <a class="menu {{ $request->routeIs('admin.listingReport.*') ? 'active' : '' }}"
            href="{{ route('admin.listingReport.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/query.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Listing Reports') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission('admin.reportReason.index')
    <li>
        <a class="menu {{ $request->routeIs('admin.reportReason.*') ? 'active' : '' }}"
            href="{{ route('admin.reportReason.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/query.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Report Reasons') }}
            </span>
        </a>
    </li>
@endhasPermission

{{-- In-App Chat removed: ChatOversightController uses admin DB ShopUser/ShopUserChats (e-commerce shop chat), not listocean live_chat_threads --}}
{{-- WhatsApp removed: admin DB, whats_app_contacts/messages all empty, never used --}}

@hasPermission(['admin.supportTicket.index'])
    <!--- Help Requests --->
    <li>
        <a href="{{ route('admin.supportTicket.index') }}"
            class="menu {{ request()->routeIs('admin.supportTicket.*') ? 'active' : '' }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/query.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Customer Query') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission(['admin.support.index'])
    <!--- Help Notes --->
    <li>
        <a href="{{ route('admin.support.index') }}"
            class="menu {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/notes.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Notes') }}
            </span>
        </a>
    </li>
@endhasPermission

@if ($commerceModulesEnabled)
    @hasPermission(['admin.rider.index', 'admin.customer.index', 'admin.employee.index', 'admin.role.index'])
        <li class="menu-divider">
            <span class="menu-title">{{ __('User Management') }}</span>
            <div class="devider_line"></div>
        </li>
    @endhasPermission
@else
    @hasPermission(['admin.customer.index', 'admin.employee.index', 'admin.role.index'])
        <li class="menu-divider">
            <span class="menu-title">{{ __('User Management') }}</span>
            <div class="devider_line"></div>
        </li>
    @endhasPermission
@endif

@if (module_exists('purchase') )
    @include('purchase::layouts.supplierSidebar')
@endif
@if ($commerceModulesEnabled)
    @hasPermission(['admin.rider.index', 'admin.rider.create'])
        <li>
            <a class="menu {{ request()->routeIs('admin.rider.*') ? 'active' : '' }}" data-bs-toggle="collapse"
                href="#riderMenu">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/truck.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Drivers') }}
                </span>
                <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.rider.*') ? 'show' : '' }}"
                id="riderMenu">
                <div class="listBar">
                    @hasPermission('admin.rider.index')
                        <a href="{{ route('admin.rider.index') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.rider.index') ? 'active' : '' }}">
                            {{ __('List Of Drivers') }}
                        </a>
                    @endhasPermission
                    @hasPermission('admin.employee.create')
                        <a href="{{ route('admin.rider.create') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.rider.create') ? 'active' : '' }}">
                            {{ __('Add New Driver') }}
                        </a>
                    @endhasPermission
                </div>
            </div>
        </li>
    @endhasPermission
@endif

@if($isRootUser)
    <li>
        <a class="menu {{ request()->routeIs('admin.customer.*', 'admin.identityVerification.*', 'admin.membershipPlan.*', 'admin.siteWallet.*', 'admin.listocean-review.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#customerMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/customer.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Users') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.customer.*', 'admin.identityVerification.*', 'admin.membershipPlan.*', 'admin.siteWallet.*', 'admin.listocean-review.*') ? 'show' : '' }}"
            id="customerMenu">
            <div class="listBar">
                <a href="{{ route('admin.customer.index') }}"
                    class="subMenu hasCount {{ request()->routeIs('admin.customer.index') ? 'active' : '' }}">
                    {{ __('List Of Users') }}
                </a>

                <a href="{{ route('admin.identityVerification.index') }}"
                    class="subMenu hasCount {{ request()->routeIs('admin.identityVerification.*') ? 'active' : '' }}">
                    {{ __('Identity Verification Requests') }}
                </a>

                <a href="{{ route('admin.membershipPlan.index') }}"
                    class="subMenu hasCount {{ request()->routeIs('admin.membershipPlan.*') ? 'active' : '' }}">
                    {{ __('Membership Plans') }}
                </a>

                <a href="{{ route('admin.siteWallet.index') }}"
                    class="subMenu hasCount {{ request()->routeIs('admin.siteWallet.*') ? 'active' : '' }}">
                    {{ __('Wallet (Customer Web)') }}
                </a>

                <a href="{{ route('admin.listocean-review.index') }}"
                    class="subMenu hasCount {{ request()->routeIs('admin.listocean-review.*') ? 'active' : '' }}">
                    {{ __('User Reviews') }}
                </a>

                <a href="{{ route('admin.customer.create') }}"
                    class="subMenu hasCount {{ request()->routeIs('admin.customer.create') ? 'active' : '' }}">
                    {{ __('Add New User') }}
                </a>
            </div>
        </div>
    </li>

@elseif($isRootUser === false)
@hasPermission(['admin.customer.index', 'admin.customer.create'])
    <li>
        <a class="menu {{ request()->routeIs('admin.customer.*', 'admin.identityVerification.*', 'admin.membershipPlan.*', 'admin.siteWallet.*', 'admin.listocean-review.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#customerMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/customer.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Users') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.customer.*', 'admin.identityVerification.*', 'admin.membershipPlan.*', 'admin.siteWallet.*', 'admin.listocean-review.*') ? 'show' : '' }}"
            id="customerMenu">
            <div class="listBar">
                @hasPermission('admin.customer.index')
                    <a href="{{ route('admin.customer.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.customer.index') ? 'active' : '' }}">
                        {{ __('List Of Users') }}
                    </a>

                    <a href="{{ route('admin.identityVerification.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.identityVerification.*') ? 'active' : '' }}">
                        {{ __('Identity Verification Requests') }}
                    </a>

                    <a href="{{ route('admin.membershipPlan.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.membershipPlan.*') ? 'active' : '' }}">
                        {{ __('Membership Plans') }}
                    </a>

                    <a href="{{ route('admin.siteWallet.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.siteWallet.*') ? 'active' : '' }}">
                        {{ __('Wallet (Customer Web)') }}
                    </a>

                    <a href="{{ route('admin.listocean-review.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.listocean-review.*') ? 'active' : '' }}">
                        {{ __('User Reviews') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.customer.create')
                    <a href="{{ route('admin.customer.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.customer.create') ? 'active' : '' }}">
                        {{ __('Add New User') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>

@endhasPermission
@endif
@hasPermission(['admin.employee.index', 'admin.employee.create'])

    <li>
        <a class="menu {{ request()->routeIs('admin.employee.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#employeeMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/employee.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Moderators') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.employee.*') ? 'show' : '' }}"
            id="employeeMenu">
            <div class="listBar">
                @hasPermission('admin.employee.index')
                    <a href="{{ route('admin.employee.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.employee.index') ? 'active' : '' }}">
                        {{ __('List Of Moderators') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.employee.create')
                    <a href="{{ route('admin.employee.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.employee.create') ? 'active' : '' }}">
                        {{ __('Add New Moderator') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@hasPermission([
    'admin.generale-setting.index',
    'admin.business-setting.index',
    'admin.themeColor.index',
    'admin.ticketIssueType.index',
    'admin.contactUs.index',
    'admin.pusher.index',
    'admin.mailConfig.index',
    'admin.paymentGateway.index',
    'admin.sms-gateway.index',
    'admin.firebase.index',
    'admin.verification.index',
    'admin.role.index'
])
    <li class="menu-divider">
        <span class="menu-title">{{ __('Settings') }}</span>
        <div class="devider_line"></div>
    </li>
@endhasPermission

@if ($shopPanelEnabled && $businessModel != 'single')
    @hasPermission(['shop.profile.index'])
        <!--- Profile --->
        <li>
            <a class="menu {{ $request->routeIs('shop.profile.*') ? 'active' : '' }}"
                href="{{ route('shop.profile.index') }}">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/user-circle.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('My Profile') }}
                </span>
            </a>
        </li>
    @endhasPermission
@endif

@hasPermission('admin.business-setting.index')
    <!---Business Settings --->
    <li>
        <a class="menu {{ request()->routeIs('admin.business-setting.*') ? 'active' : '' }}"
            href="{{ route('admin.business-setting.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/business.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Business Setting') }}
            </span>
        </a>
    </li>
@endhasPermission

<!--- third party configuration --->
@hasPermission([
    'admin.socialAuth.index',
    'admin.pusher.index',
    'admin.mailConfig.index',
    'admin.paymentGateway.index',
    'admin.sms-gateway.index',
    'admin.firebase.index',
    'admin.googleReCaptcha.index',
    'admin.aiPrompt.configure'
])
    <li>
        <a class="menu {{ request()->routeIs('admin.socialAuth.*', 'admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*', 'admin.googleReCaptcha.*', 'admin.aiPrompt.configure') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#thirdPartConfig" title="Third Party configuration">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/tool.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Configure Dependence') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*', 'admin.googleReCaptcha.*', 'admin.socialAuth.*', 'admin.aiPrompt.configure') ? 'show' : '' }}"
            id="thirdPartConfig">
            <div class="listBar">
                @hasPermission('admin.paymentGateway.index')
                    <a href="{{ route('admin.paymentGateway.index') }}"
                        class="subMenu {{ request()->routeIs('admin.paymentGateway.*') ? 'active' : '' }}">
                        {{ __('Payment Gateway') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.sms-gateway.index')
                    <a href="{{ route('admin.sms-gateway.index') }}"
                        class="subMenu {{ request()->routeIs('admin.sms-gateway.*') ? 'active' : '' }}">
                        {{ __('SMS Gateway') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.socialAuth.index')
                    <a href="{{ route('admin.socialAuth.index') }}"
                        class="subMenu {{ request()->routeIs('admin.socialAuth.*') ? 'active' : '' }}">
                        {{ __('Social Login') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.pusher.index')
                    <a href="{{ route('admin.pusher.index') }}"
                        class="subMenu {{ request()->routeIs('admin.pusher.*') ? 'active' : '' }}">
                        {{ __('Pusher Setup') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.mailConfig.index')
                    <a href="{{ route('admin.mailConfig.index') }}"
                        class="subMenu {{ request()->routeIs('admin.mailConfig.*') ? 'active' : '' }}">
                        {{ __('Mail Config') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.emailTemplate.index')
                    <a href="{{ route('admin.emailTemplate.index') }}"
                        class="subMenu {{ request()->routeIs('admin.emailTemplate.*') ? 'active' : '' }}">
                        {{ __('Email Templates') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.mapSettings.index')
                    <a href="{{ route('admin.mapSettings.index') }}"
                        class="subMenu {{ request()->routeIs('admin.mapSettings.*') ? 'active' : '' }}">
                        {{ __('Map Settings') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.firebase.index')
                    <a href="{{ route('admin.firebase.index') }}"
                        class="subMenu {{ request()->routeIs('admin.firebase.*') ? 'active' : '' }}">
                        {{ __('Firebase Notification') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.googleReCaptcha.index')
                    <a href="{{ route('admin.googleReCaptcha.index') }}"
                        class="subMenu {{ request()->routeIs('admin.googleReCaptcha.*') ? 'active' : '' }}">
                        {{ __('Google ReCaptcha') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.aiPrompt.configure')
                    <a href="{{ route('admin.aiPrompt.configure') }}"
                        class="subMenu {{ request()->routeIs('admin.aiPrompt.configure') ? 'active' : '' }}">
                        {{ __('OpenAI Config') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.pageSettings.loginRegister')
                    <a href="{{ route('admin.pageSettings.loginRegister') }}"
                        class="subMenu {{ request()->routeIs('admin.pageSettings.*') ? 'active' : '' }}">
                        {{ __('Page Settings') }}
                    </a>
                    <a href="{{ route('admin.safetyTips.edit') }}"
                        class="subMenu {{ request()->routeIs('admin.safetyTips.*') ? 'active' : '' }}">
                        {{ __('Safety Tips') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@hasPermission('admin.siteNotice.index')
    <!--- listocean notices --->
    <li>
        <a class="menu {{ $request->routeIs('admin.siteNotice.*') ? 'active' : '' }}"
            href="{{ route('admin.siteNotice.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/notification.svg') }}" alt="icon" loading="lazy" />
                {{ __('Notices') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission(['admin.themeColor.index', 'admin.homepageHero.edit'])
    <!--- Settings --->
    <li>
        <a class="menu {{ request()->routeIs('admin.themeColor.*', 'admin.offerBanner.*', 'admin.homepageHero.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#appearance">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/palette.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Appearance') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.themeColor.*', 'admin.offerBanner.*', 'admin.homepageHero.*') ? 'show' : '' }}"
            id="appearance">
            <div class="listBar">
                @hasPermission('admin.homepageHero.edit')
                    <a href="{{ route('admin.homepageHero.edit') }}"
                        class="subMenu {{ request()->routeIs('admin.homepageHero.*') ? 'active' : '' }}">
                        {{ __('Homepage Hero') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.themeColor.index')
                    <a href="{{ route('admin.themeColor.index') }}"
                        class="subMenu {{ request()->routeIs('admin.themeColor.*', 'admin.offerBanner.*') ? 'active' : '' }}">
                        {{ __('Color & Home Screen') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@if ($shopPanelEnabled)
@hasPermission(['shop.bulk-product-export.index', 'shop.bulk-product-import.index', 'shop.gallery.index'])
    <!--- Import / Export --->
    <li>
        <a class="menu {{ request()->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#exportImportMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/download.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Import/Export') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'show' : '' }}"
            id="exportImportMenu">
            <div class="listBar">
                @hasPermission('shop.bulk-product-export.index')
                    <a href="{{ route('shop.bulk-product-export.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.bulk-product-export.*') ? 'active' : '' }}">
                        {{ __('Products Export') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.bulk-product-import.index')
                    <a href="{{ route('shop.bulk-product-import.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.bulk-product-import.*') ? 'active' : '' }}">
                        {{ __('Products Import') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.gallery.index')
                    <a href="{{ route('shop.gallery.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.gallery.*') ? 'active' : '' }}">
                        {{ __('File Manager') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@endif
<!--- cms --->
@hasPermission(['admin.menu.index', 'admin.footer.index', 'admin.sitePages.index', 'admin.faq.index'])
    <li>
        <a class="menu {{ request()->routeIs('admin.menu.index*', 'admin.footer.*', 'admin.sitePages.*', 'admin.faq.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#cms">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/file-settings.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Manage Content') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.menu.*', 'admin.footer.*', 'admin.sitePages.*', 'admin.faq.*') ? 'show' : '' }}"
            id="cms">
            <div class="listBar">

                @hasPermission('admin.sitePages.index')
                    <div class="subMenu text-muted fw-semibold" style="font-size:11px;padding:10px 20px 4px;letter-spacing:.04em;text-transform:uppercase;cursor:default;">{{ __('Content Pages') }}</div>
                    <a href="{{ route('admin.sitePages.editBySlug', 'about') }}" class="subMenu ps-4 {{ request()->is('*site-pages/by-slug/about*') ? 'active' : '' }}">{{ __('About Us') }}</a>
                    <a href="{{ route('admin.sitePages.editBySlug', 'terms-and-conditions') }}" class="subMenu ps-4 {{ request()->is('*site-pages/by-slug/terms*') ? 'active' : '' }}">{{ __('Terms & Conditions') }}</a>
                    <a href="{{ route('admin.sitePages.editBySlug', 'privacy-policy') }}" class="subMenu ps-4 {{ request()->is('*site-pages/by-slug/privacy*') ? 'active' : '' }}">{{ __('Privacy Policy') }}</a>
                    <a href="{{ route('admin.sitePages.editBySlug', 'contact') }}" class="subMenu ps-4 {{ request()->is('*site-pages/by-slug/contact*') ? 'active' : '' }}">{{ __('Contact') }}</a>
                    <a href="{{ route('admin.sitePages.editBySlug', 'safety-informations') }}" class="subMenu ps-4 {{ request()->is('*site-pages/by-slug/safety*') ? 'active' : '' }}">{{ __('Safety Information') }}</a>
                @endhasPermission

                @hasPermission('admin.faq.index')
                    <a href="{{ route('admin.faq.index') }}"
                       class="subMenu ps-4 {{ request()->routeIs('admin.faq.*') ? 'active' : '' }}">
                        {{ __('FAQ Items') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.menu.index')
                    <a href="{{ route('admin.menu.index') }}"
                        class="subMenu {{ request()->routeIs('admin.menu.index') ? 'active' : '' }}">
                        {{ __('Menus') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.footer.index')
                    <a href="{{ route('admin.footer.index') }}"
                        class="subMenu {{ request()->routeIs('admin.footer.index') ? 'active' : '' }}">
                        {{ __('Footer') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@hasPermission([
    'admin.generale-setting.index',
    'admin.ticketIssueType.index',
    'admin.verification.index',
    'admin.contactUs.index',
    'admin.country.index',
    'admin.role.index',
    'admin.aiPrompt.index'
])
    <!--- Settings --->
    <li>
        <a class="menu {{ request()->routeIs('admin.generale-setting.*', 'admin.ticketIssueType.*', 'admin.verification.*', 'admin.contactUs.*', 'admin.country.*', 'admin.role.*', 'admin.aiPrompt.index', 'admin.language.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#settings">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/settings.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('System Settings') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.generale-setting.*', 'admin.ticketIssueType.*', 'admin.verification.*', 'admin.contactUs.*', 'admin.country.*', 'admin.role.*', 'admin.aiPrompt.index', 'admin.language.*') ? 'show' : '' }}"
            id="settings">
            <div class="listBar">
                @hasPermission('admin.generale-setting.index')
                    <a href="{{ route('admin.generale-setting.index') }}"
                        class="subMenu {{ request()->routeIs('admin.generale-setting.index') ? 'active' : '' }}">
                        {{ __('General') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.aiPrompt.index')
                    <a href="{{ route('admin.aiPrompt.index') }}"
                        class="subMenu {{ request()->routeIs('admin.aiPrompt.index') ? 'active' : '' }}">
                        {{ __('Ai Prompt') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.verification.index')
                    <a href="{{ route('admin.verification.index') }}"
                        class="subMenu {{ request()->routeIs('admin.verification.*') ? 'active' : '' }}">
                        {{ __('Auth Verification') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.ticketIssueType.index')
                    <a href="{{ route('admin.ticketIssueType.index') }}"
                        class="subMenu {{ request()->routeIs('admin.ticketIssueType.index') ? 'active' : '' }}">
                        {{ __('Ticket Issue Types') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.role.index')
                    <a href="{{ route('admin.role.index') }}"
                        class="subMenu {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                        {{ __('Role & Permissions') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.contactUs.index')
                    <a href="{{ route('admin.contactUs.index') }}"
                        class="subMenu {{ request()->routeIs('admin.contactUs.index') ? 'active' : '' }}">
                        {{ __('Contact Configure') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.country.index')
                    <a href="{{ route('admin.country.index') }}"
                        class="subMenu {{ request()->routeIs('admin.country.index') ? 'active' : '' }}">
                        {{ __('Configure Country') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.language.index')
                    <a href="{{ route('admin.language.index') }}"
                        class="subMenu {{ request()->routeIs('admin.language.*') ? 'active' : '' }}">
                        {{ __('Configure Language') }}
                    </a>
                @endhasPermission
                
            </div>
        </div>
    </li>
@endhasPermission

