@extends('layouts.app')

@section('header-title', __('Ads Hub'))
@section('header-subtitle', __('Manage every ad type from one place.'))

@section('content')
<div class="container-fluid my-4">

    {{-- ── Page heading ──────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-1 mb-4">
        <div>
            <h4 class="m-0">{{ __('Ads Hub') }}</h4>
            <p class="text-muted small mb-0">{{ __('Centralised control panel for all advertising on the platform.') }}</p>
        </div>
    </div>

    {{-- ── Top-level engagement metrics ─────────────────────────────── --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="dashboard-box item-1">
                        <h2 class="count">{{ number_format($totalImpressions) }}</h2>
                        <h3 class="title">{{ __('Total Impressions') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/ads.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dashboard-box item-2">
                        <h2 class="count">{{ number_format($totalClicks) }}</h2>
                        <h3 class="title">{{ __('Total Clicks') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/ads.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dashboard-box item-3">
                        @php $ctr = $totalImpressions > 0 ? round($totalClicks / $totalImpressions * 100, 2) : 0; @endphp
                        <h2 class="count">{{ $ctr }}%</h2>
                        <h3 class="title">{{ __('Overall CTR') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/ads.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dashboard-box item-4">
                        <h2 class="count">{{ $bannerReqPending }}</h2>
                        <h3 class="title">{{ __('Pending Approvals') }}</h3>
                        <div class="icon">
                            <img src="{{ asset('assets/icons-admin/notification.svg') }}" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────── --}}
    {{-- GROUP 1 · Paid Ad Placements                                     --}}
    {{-- ──────────────────────────────────────────────────────────────── --}}
    <h6 class="text-uppercase text-muted fw-semibold small px-1 mb-2 mt-3">
        <i class="fa-solid fa-rectangle-ad me-1"></i>{{ __('Paid Ad Placements') }}
    </h6>
    <div class="row g-3 mb-4">

        {{-- Banner Ad Requests --}}
        @hasPermission('admin.bannerAdRequests.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Banner Ad Requests') }}</p>
                            <h3 class="fw-bold mb-0">{{ $bannerReqTotal }}</h3>
                        </div>
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            {{ $bannerReqPending }} {{ __('Pending') }}
                        </span>
                    </div>
                    <div class="d-flex gap-2 text-muted small mb-3">
                        <span><i class="fa-solid fa-circle-check text-success me-1"></i>{{ $bannerReqActive }} {{ __('Approved') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('User-submitted banner ads awaiting your review & approval before going live.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                    <a href="{{ route('admin.bannerAdRequests.index') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-list me-1"></i>{{ __('Manage Requests') }}
                        @if($bannerReqPending > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $bannerReqPending }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- Site Advertisements (internal admin-placed ads) --}}
        @hasPermission('admin.siteAdvertisement.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Site Advertisements') }}</p>
                            <h3 class="fw-bold mb-0">{{ $siteAdTotal }}</h3>
                        </div>
                        <span class="badge bg-success fs-6 px-3 py-2">{{ $siteAdActive }} {{ __('Active') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Internal ads placed by the admin across homepage hero, listing pages, and sidebar slots.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3 d-flex gap-2">
                    <a href="{{ route('admin.siteAdvertisement.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="fa-solid fa-list me-1"></i>{{ __('View All') }}
                    </a>
                    @hasPermission('admin.siteAdvertisement.create')
                    <a href="{{ route('admin.siteAdvertisement.create') }}" class="btn btn-primary btn-sm flex-fill">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add New') }}
                    </a>
                    @endhasPermission
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- Promo Video Ads --}}
        @hasPermission('admin.promoVideoAds.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Promo Video Ads') }}</p>
                            <h3 class="fw-bold mb-0">{{ $promoVideoTotal }}</h3>
                        </div>
                        <span class="badge bg-success fs-6 px-3 py-2">{{ $promoVideoActive }} {{ __('Active') }}</span>
                    </div>
                    <div class="d-flex gap-2 text-muted small mb-3">
                        <span><i class="fa-solid fa-eye text-primary me-1"></i>{{ number_format($totalVideoViews) }} {{ __('views') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Short video ads that appear in the reels feed. Tracks view counts per video.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                    <a href="{{ route('admin.promoVideoAds.index') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-film me-1"></i>{{ __('Manage Video Ads') }}
                    </a>
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- Reel Ad Placements --}}
        @hasPermission('admin.reelAdPlacement.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Reel Ad Placements') }}</p>
                            <h3 class="fw-bold mb-0">{{ $reelTotal }}</h3>
                        </div>
                        <span class="badge bg-success fs-6 px-3 py-2">{{ $reelActive }} {{ __('Active') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Control which ads, videos, or listings are injected at specific positions in the reels feed.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                    <a href="{{ route('admin.reelAdPlacement.index') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-sliders me-1"></i>{{ __('Manage Placements') }}
                    </a>
                </div>
            </div>
        </div>
        @endhasPermission

    </div>

    {{-- ──────────────────────────────────────────────────────────────── --}}
    {{-- GROUP 2 · Featured Ad System                                     --}}
    {{-- ──────────────────────────────────────────────────────────────── --}}
    <h6 class="text-uppercase text-muted fw-semibold small px-1 mb-2 mt-2">
        <i class="fa-solid fa-star me-1"></i>{{ __('Featured Ad System') }}
    </h6>
    <div class="row g-3 mb-4">

        {{-- Featured Ad Packages --}}
        @hasPermission('admin.featuredAdPackage.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Featured Packages') }}</p>
                            <h3 class="fw-bold mb-0">{{ $featuredPackages }}</h3>
                        </div>
                        <span class="badge bg-info text-dark fs-6 px-3 py-2">{{ __('Packages') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Define paid packages (duration + price) that users purchase to boost their listings to the top.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3 d-flex gap-2">
                    <a href="{{ route('admin.featuredAdPackage.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="fa-solid fa-list me-1"></i>{{ __('View All') }}
                    </a>
                    @hasPermission('admin.featuredAdPackage.create')
                    <a href="{{ route('admin.featuredAdPackage.create') }}" class="btn btn-primary btn-sm flex-fill">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add Package') }}
                    </a>
                    @endhasPermission
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- Featured Ad Purchases --}}
        @hasPermission('admin.featuredAdReport.purchases')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Featured Purchases') }}</p>
                            <h3 class="fw-bold mb-0">{{ $featuredPurchases }}</h3>
                        </div>
                        <span class="badge bg-secondary fs-6 px-3 py-2">{{ __('Revenue') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('All wallet-based purchases of featured ad packages by users — your ad revenue record.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                    <a href="{{ route('admin.featuredAdReport.purchases') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-receipt me-1"></i>{{ __('View Purchases') }}
                    </a>
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- Featured Ad Activations --}}
        @hasPermission('admin.featuredAdReport.activations')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Featured Activations') }}</p>
                            <h3 class="fw-bold mb-0">{{ $featuredActivations }}</h3>
                        </div>
                        <span class="badge bg-success fs-6 px-3 py-2">{{ __('Live Boosts') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Currently active listing boosts across the platform. Each activation shows the listing, user, and expiry.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                    <a href="{{ route('admin.featuredAdReport.activations') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-bolt me-1"></i>{{ __('View Activations') }}
                    </a>
                </div>
            </div>
        </div>
        @endhasPermission

    </div>

    {{-- ──────────────────────────────────────────────────────────────── --}}
    {{-- GROUP 3 · Promotions & Display Ads                              --}}
    {{-- ──────────────────────────────────────────────────────────────── --}}
    <h6 class="text-uppercase text-muted fw-semibold small px-1 mb-2 mt-2">
        <i class="fa-solid fa-tags me-1"></i>{{ __('Promotions & Display Ads') }}
    </h6>
    <div class="row g-3 mb-4">

        {{-- Flash Sales --}}
        @hasPermission('admin.flashSale.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Flash Sales') }}</p>
                            <h3 class="fw-bold mb-0">{{ $flashSaleTotal }}</h3>
                        </div>
                        <span class="badge bg-danger fs-6 px-3 py-2">{{ $flashSaleActive }} {{ __('Active') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Time-limited sale events shown in the app with countdown timers. Drive urgency and purchases.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3 d-flex gap-2">
                    <a href="{{ route('admin.flashSale.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="fa-solid fa-list me-1"></i>{{ __('View All') }}
                    </a>
                    @hasPermission('admin.flashSale.create')
                    <a href="{{ route('admin.flashSale.create') }}" class="btn btn-primary btn-sm flex-fill">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add New') }}
                    </a>
                    @endhasPermission
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- Flash Sale Widget --}}
        @hasPermission('admin.flashSaleWidget.edit')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Flash Sale Widget') }}</p>
                            <h3 class="fw-bold mb-0">—</h3>
                        </div>
                        <span class="badge bg-secondary fs-6 px-3 py-2">{{ __('Widget') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Configure which flash sale is displayed in the homepage widget and set the promotional banner image.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                    <a href="{{ route('admin.flashSaleWidget.edit') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-pen-to-square me-1"></i>{{ __('Configure Widget') }}
                    </a>
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- SellUpNow Banner Ads (native) --}}
        @hasPermission('admin.ad.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Display Ads') }}</p>
                            <h3 class="fw-bold mb-0">{{ $nativeAdTotal }}</h3>
                        </div>
                        <span class="badge bg-success fs-6 px-3 py-2">{{ $nativeAdActive }} {{ __('Active') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Native display ad units (image/embedded) shown in the SellUpNow storefront.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3 d-flex gap-2">
                    <a href="{{ route('admin.ad.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="fa-solid fa-list me-1"></i>{{ __('View All') }}
                    </a>
                    @hasPermission('admin.ad.create')
                    <a href="{{ route('admin.ad.create') }}" class="btn btn-primary btn-sm flex-fill">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add New') }}
                    </a>
                    @endhasPermission
                </div>
            </div>
        </div>
        @endhasPermission

        {{-- SellUpNow Banners --}}
        @hasPermission('admin.banner.index')
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Hero Banners') }}</p>
                            <h3 class="fw-bold mb-0">{{ $bannerTotal }}</h3>
                        </div>
                        <span class="badge bg-success fs-6 px-3 py-2">{{ $bannerActive }} {{ __('Active') }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Full-width hero banners displayed at the top of the homepage. Control images, links, and ordering.') }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3 d-flex gap-2">
                    <a href="{{ route('admin.banner.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="fa-solid fa-list me-1"></i>{{ __('View All') }}
                    </a>
                    @hasPermission('admin.banner.create')
                    <a href="{{ route('admin.banner.create') }}" class="btn btn-primary btn-sm flex-fill">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add New') }}
                    </a>
                    @endhasPermission
                </div>
            </div>
        </div>
        @endhasPermission

    </div>

</div>
@endsection
