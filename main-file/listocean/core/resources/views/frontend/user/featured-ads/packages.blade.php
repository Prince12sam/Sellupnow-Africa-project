@extends('frontend.layout.master')
@section('site_title')
    {{ __('Featured Ad Packages') }}
@endsection
@section('content')
    <div class="profile-setting profile-pages section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>

                                {{-- Wallet balance + membership credits banner --}}
                                <div class="relevant-ads box-shadow1 p-24 mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <h4 class="mb-1">{{ __('Feature Your Listing') }}</h4>
                                            <p class="text-muted mb-0" style="font-size:13px;">
                                                {{ __('Wallet balance') }}: <strong>{{ amount_with_currency_symbol($balance) }}</strong>
                                                <a href="{{ route('user.wallet.topup') }}" class="ms-2" style="font-size:12px;">
                                                    {{ __('Add funds →') }}
                                                </a>
                                            </p>
                                            @if($membershipCredits > 0)
                                                <p class="mb-0 mt-1" style="font-size:13px;">
                                                    <i class="las la-star" style="color:#f5a623;"></i>
                                                    {{ __('Membership credits') }}: <strong>{{ $membershipCredits }} {{ __('featured listing(s) remaining') }}</strong>
                                                    &mdash; <span class="text-muted" style="font-size:12px;">{{ __('Use them from the Add Listing form (free, no expiry).') }}</span>
                                                </p>
                                            @elseif(moduleExists('Membership') && membershipModuleExistsAndEnable('Membership'))
                                                <p class="mb-0 mt-1 text-muted" style="font-size:12px;">
                                                    <i class="las la-info-circle"></i>
                                                    {{ __('No membership featured credits remaining. Purchase a timed package below.') }}
                                                </p>
                                            @endif
                                        </div>
                                        <a href="{{ route('user.featuredAds.index') }}" class="btn btn-outline-secondary btn-sm">
                                            {{ __('My Featured Ads') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Flash messages --}}
                                @if(session('success'))
                                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                                @endif

                                {{-- Package cards --}}
                                @if($packages->isEmpty())
                                    <div class="relevant-ads box-shadow1 p-24">
                                        <p class="text-muted mb-0">{{ __('No featured ad packages are available at the moment. Please check back later.') }}</p>
                                    </div>
                                @else
                                    <div class="row g-4">
                                        @foreach($packages as $package)
                                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                                <div class="relevant-ads box-shadow1 p-24 h-100 d-flex flex-column">
                                                    <h5 class="mb-1">{{ $package->name }}</h5>
                                                    <p class="text-muted" style="font-size:13px;min-height:40px;">
                                                        {{ $package->description ?? '' }}
                                                    </p>

                                                    <ul class="list-unstyled mb-3" style="font-size:14px;">
                                                        <li>
                                                            <i class="las la-calendar me-1" style="color:var(--main-color-one,#524EB7);"></i>
                                                            {{ $package->duration_days }} {{ __('days') }}
                                                        </li>
                                                        <li>
                                                            <i class="las la-bullhorn me-1" style="color:var(--main-color-one,#524EB7);"></i>
                                                            {{ $package->advertisement_limit }} {{ __('ad impressions') }}
                                                        </li>
                                                        @if($package->position)
                                                            <li>
                                                                <i class="las la-map-marker-alt me-1" style="color:var(--main-color-one,#524EB7);"></i>
                                                                {{ __('Position') }}: {{ $package->position }}
                                                            </li>
                                                        @endif
                                                    </ul>

                                                    <div class="mt-auto">
                                                        <div class="mb-3">
                                                            @if($package->isFree())
                                                                <span class="fw-bold" style="font-size:1.4rem;color:var(--main-color-one,#524EB7);">
                                                                    {{ __('Free') }}
                                                                </span>
                                                            @else
                                                                <span class="fw-bold" style="font-size:1.4rem;color:var(--main-color-one,#524EB7);">
                                                                    {{ amount_with_currency_symbol($package->price) }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Purchase form --}}
                                                        <form method="POST" action="{{ route('user.featuredAds.purchase') }}">
                                                            @csrf
                                                            <input type="hidden" name="package_id" value="{{ $package->id }}">

                                                            <div class="mb-2">
                                                                <select name="listing_id" class="form-select form-select-sm" required>
                                                                    <option value="">{{ __('-- Select listing --') }}</option>
                                                                    @foreach($listings as $listing)
                                                                        <option value="{{ $listing->id }}"
                                                                            {{ $selectedListingId === $listing->id ? 'selected' : '' }}>
                                                                            #{{ $listing->id }} — {{ Str::limit($listing->title, 40) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            @if(!$package->isFree() && $balance < $package->price)
                                                                <a href="{{ route('user.wallet.topup') }}" class="btn btn-warning btn-sm w-100">
                                                                    {{ __('Add Funds to Feature') }}
                                                                </a>
                                                            @else
                                                                <button type="submit" class="red-btn w-100">
                                                                    {{ $package->isFree() ? __('Feature for Free') : __('Feature Now') }}
                                                                </button>
                                                            @endif
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            </div>{{-- /main-body --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
