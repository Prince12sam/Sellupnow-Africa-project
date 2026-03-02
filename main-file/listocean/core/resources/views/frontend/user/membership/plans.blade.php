@extends('frontend.layout.master')
@section('site_title')
    {{ __('Membership Plans') }}
@endsection
@section('style')
<style>
    .plan-card {
        border: 2px solid #e3e3e3;
        border-radius: 12px;
        padding: 1.25rem 1.25rem;
        transition: border-color .2s, box-shadow .2s;
        display: flex;
        flex-direction: column;
        background: #fff;
    }
    .plan-card.is-current {
        border-color: var(--main-color-one, #524EB7);
        box-shadow: 0 4px 24px rgba(82,78,183,.12);
    }
    .plan-card.is-popular {
        border-color: #f97316;
    }
    .plan-badge {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
    }
    .plan-price {
        font-size: 1.7rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
    }
    .plan-price sup {
        font-size: .85rem;
        vertical-align: super;
    }
    .plan-price sub {
        font-size: .8rem;
        font-weight: 400;
        color: #64748b;
    }
    .plan-features {
        list-style: none;
        padding: 0;
        margin: .75rem 0;
        flex: 1;
    }
    .plan-features li {
        padding: 3px 0;
        font-size: 13px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .plan-features li i {
        color: #22c55e;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .plan-features li.unavailable {
        color: #94a3b8;
    }
    .plan-features li.unavailable i {
        color: #cbd5e1;
    }
</style>
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

                                {{-- Header --}}
                                <div class="relevant-ads box-shadow1 p-24 mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <h4 class="mb-1" style="font-weight:700;">{{ __('Membership Plans') }}</h4>
                                            <p class="text-muted mb-0">{{ __('Choose a plan to unlock more listings, features, and visibility.') }}</p>
                                        </div>
                                        <div>
                                            @if($current)
                                                <div class="text-end">
                                                    <span class="text-muted" style="font-size:13px;">{{ __('Current Plan') }}</span><br>
                                                    <strong style="color:var(--main-color-one,#524EB7);">{{ $current->plan->name ?? 'Free' }}</strong>
                                                    @if($current->expires_at)
                                                        <br><small class="text-muted">{{ __('Expires') }}: {{ $current->expires_at->format('d M Y') }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Free Plan') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Wallet Balance --}}
                                    <div class="alert alert-info mt-3 mb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <span>
                                            <i class="las la-wallet me-1"></i>
                                            {{ __('Wallet Balance') }}: <strong>{{ amount_with_currency_symbol($walletBalance) }}</strong>
                                        </span>
                                        <a href="{{ route('user.wallet.topup') }}" style="font-size:13px;">
                                            {{ __('Add funds →') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Plans Grid --}}
                                @if($plans->isEmpty())
                                    <div class="relevant-ads box-shadow1 p-24 text-center text-muted py-5">
                                        <i class="las la-layer-group" style="font-size:3rem;opacity:.3;"></i>
                                        <p class="mt-2">{{ __('No membership plans are available yet. Check back soon!') }}</p>
                                    </div>
                                @else
                                    <div class="row g-4">
                                        @foreach($plans as $plan)
                                            @php
                                                $isCurrentPlan  = $current && $current->plan_id === $plan->id && $current->isActive();
                                                $rawBadgeLabel  = trim($plan->badge_label ?? '');
                                                $rawColor       = trim($plan->badge_color ?? '');
                                                $badgeColor     = $rawColor !== '' ? ltrim($rawColor, '#') : 'f97316';
                                                $isPopular      = $rawBadgeLabel !== '' || $rawColor !== '';
                                                $badgeLabel     = $rawBadgeLabel !== '' ? $rawBadgeLabel : 'Popular';
                                                $canAfford      = $walletBalance >= (float) $plan->price;
                                                $isFree         = (float) $plan->price == 0;
                                                $features       = is_array($plan->features) ? $plan->features : (json_decode($plan->features, true) ?? []);
                                            @endphp
                                            <div class="col-lg-3 col-md-6">
                                                <div class="plan-card {{ $isCurrentPlan ? 'is-current' : '' }} {{ $isPopular ? 'is-popular' : '' }}"
                                                     @if($isPopular) style="border-color: #{{ $badgeColor }};" @endif>

                                                    {{-- Badges --}}
                                                    <div class="mb-2 d-flex gap-2 flex-wrap">
                                                        @if($isCurrentPlan)
                                                            <span class="plan-badge" style="background:#eff6ff;color:#3b82f6;">{{ __('Active Plan') }}</span>
                                                        @endif
                                                        @if($isPopular)
                                                            <span class="plan-badge" style="background:#fff;border:1px solid #{{ $badgeColor }};color:#{{ $badgeColor }};">{{ __($badgeLabel) }}</span>
                                                        @endif
                                                    </div>

                                                    {{-- Plan Name --}}
                                                    <h6 style="font-weight:700;margin-bottom:.25rem;">{{ $plan->name }}</h6>
                                                    @if($plan->description)
                                                        <p class="text-muted mb-1" style="font-size:12px;">{{ $plan->description }}</p>
                                                    @endif

                                                    {{-- Price --}}
                                                    <div class="plan-price mb-0">
                                                        @if($isFree)
                                                            <sup>{{ get_static_option('site_currency_symbol') ?? '₵' }}</sup>0
                                                        @else
                                                            <sup>{{ get_static_option('site_currency_symbol') ?? '₵' }}</sup>{{ number_format($plan->price, 0) }}
                                                        @endif
                                                        <sub>
                                                            @if($isFree)
                                                                / {{ __('forever') }}
                                                            @elseif($plan->duration_days == 30)
                                                                / {{ __('month') }}
                                                            @elseif($plan->duration_days == 365)
                                                                / {{ __('year') }}
                                                            @else
                                                                / {{ $plan->duration_days }} {{ __('days') }}
                                                            @endif
                                                        </sub>
                                                    </div>

                                                    <hr>

                                                    {{-- Features List --}}
                                                    @php $featureLabels = config('membership.features', []); @endphp
                                                    <ul class="plan-features">
                                                        {{-- Listing quota: null / 0 = unlimited --}}
                                                        <li>
                                                            <i class="las la-check-circle"></i>
                                                            @if(empty($plan->listing_quota))
                                                                {{ __('Unlimited listings') }}
                                                            @else
                                                                {{ $plan->listing_quota }} {{ __('listings') }}
                                                            @endif
                                                        </li>
                                                        {{-- Auto-featured listings (only show if > 0) --}}
                                                        @if(($plan->auto_feature_count ?? 0) > 0)
                                                            <li>
                                                                <i class="las la-check-circle"></i>
                                                                {{ $plan->auto_feature_count }} {{ __('auto-featured listings') }}
                                                            </li>
                                                        @endif
                                                        {{-- Video quota --}}
                                                        @php $vq = (int)($plan->video_quota ?? 0); @endphp
                                                        @if($vq !== 0)
                                                            <li>
                                                                <i class="las la-check-circle"></i>
                                                                @if($vq === -1)
                                                                    {{ __('Unlimited video listings') }}
                                                                @else
                                                                    {{ $vq }} {{ __('video listing(s) with Reels') }}
                                                                @endif
                                                            </li>
                                                        @endif
                                                        {{-- Banner ad quota --}}
                                                        @php $bq = (int)($plan->banner_ad_quota ?? 0); @endphp
                                                        @if($bq !== 0)
                                                            <li>
                                                                <i class="las la-check-circle"></i>
                                                                @if($bq === -1)
                                                                    {{ __('Unlimited banner ad requests') }}
                                                                @else
                                                                    {{ $bq }} {{ __('banner ad request(s)') }}
                                                                @endif
                                                            </li>
                                                        @endif
                                                        {{-- Duration --}}
                                                        <li>
                                                            <i class="las la-check-circle"></i>
                                                            @if($isFree)
                                                                {{ __('Free forever') }}
                                                            @elseif($plan->duration_days == 30)
                                                                {{ __('30-day access') }}
                                                            @elseif($plan->duration_days == 365)
                                                                {{ __('1-year access') }}
                                                            @else
                                                                {{ $plan->duration_days }} {{ __('days access') }}
                                                            @endif
                                                        </li>
                                                        {{-- Feature flags mapped to human-readable labels --}}
                                                        @foreach($features as $featureKey)
                                                            @php $fLabel = $featureLabels[$featureKey] ?? ucwords(str_replace('_', ' ', $featureKey)); @endphp
                                                            <li>
                                                                <i class="las la-check-circle"></i>
                                                                {{ __($fLabel) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    {{-- CTA --}}
                                                    @if($isCurrentPlan)
                                                        <button class="red-btn w-100 d-block" disabled style="opacity:.6;cursor:default;">
                                                            <i class="las la-check me-1"></i> {{ __('Current Plan') }}
                                                        </button>
                                                        @if(!$isFree)
                                                            <form action="{{ route('user.membership.cancel') }}" method="POST" class="mt-2">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100"
                                                                        onclick="return confirm('{{ __('Cancel your membership?') }}')">
                                                                    {{ __('Cancel Membership') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @elseif($isFree)
                                                        {{-- Free plan: always available --}}
                                                        <form action="{{ route('user.membership.subscribe') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                            <button type="submit" class="cmn-btn w-100 d-block">
                                                                {{ __('Switch to Free') }}
                                                            </button>
                                                        </form>
                                                    @elseif($canAfford)
                                                        <form action="{{ route('user.membership.subscribe') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                            <button type="submit" class="red-btn w-100 d-block"
                                                                    onclick="return confirm('{{ __('Subscribe for') }} {{ amount_with_currency_symbol($plan->price) }}? {{ __('This will be deducted from your wallet.') }}')">
                                                                {{ __('Subscribe Now') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('user.wallet.topup') }}" class="cmn-btn w-100 d-block text-center">
                                                            <i class="las la-wallet me-1"></i>
                                                            {{ __('Add Funds to Subscribe') }}
                                                        </a>
                                                        <p class="text-center text-muted mt-1" style="font-size:11px;">
                                                            {{ __('Need') }}: {{ amount_with_currency_symbol($plan->price - $walletBalance) }} {{ __('more') }}
                                                        </p>
                                                    @endif

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
