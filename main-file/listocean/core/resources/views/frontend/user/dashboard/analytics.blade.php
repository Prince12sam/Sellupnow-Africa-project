@extends('frontend.layout.master')
@section('site_title')
    {{ __('Listing Analytics') }}
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

                            {{-- ── Summary Cards ──────────────────────────────────────────── --}}
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-3">
                                    <div class="relevant-ads box-shadow1 p-3 text-center">
                                        <p class="text-muted mb-1" style="font-size:12px;">{{ __('Total Listings') }}</p>
                                        <h3 style="font-weight:700;color:var(--main-color-one,#524EB7);">{{ $totalListings }}</h3>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="relevant-ads box-shadow1 p-3 text-center">
                                        <p class="text-muted mb-1" style="font-size:12px;">{{ __('Total Views') }}</p>
                                        <h3 style="font-weight:700;color:var(--main-color-one,#524EB7);">{{ number_format($totalViews) }}</h3>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="relevant-ads box-shadow1 p-3 text-center">
                                        <p class="text-muted mb-1" style="font-size:12px;">{{ __('Total Saves') }}</p>
                                        <h3 style="font-weight:700;color:var(--main-color-one,#524EB7);">{{ number_format($totalFavorites) }}</h3>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="relevant-ads box-shadow1 p-3 text-center">
                                        <p class="text-muted mb-1" style="font-size:12px;">{{ __('Featured Listings') }}</p>
                                        <h3 style="font-weight:700;color:var(--main-color-one,#524EB7);">{{ $featuredCount }}</h3>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Per-Listing Table ─────────────────────────────────────── --}}
                            <div class="relevant-ads box-shadow1 p-24">
                                <h5 class="mb-4" style="font-weight:600;">{{ __('Listing Performance') }}</h5>

                                @if($listings->isEmpty())
                                    <div class="text-center py-5 text-muted">
                                        <i class="las la-chart-bar" style="font-size:3rem;opacity:.3;"></i>
                                        <p class="mt-2">{{ __('You have no listings yet.') }}</p>
                                        <a href="{{ route('user.add.listing') }}" class="red-btn mt-2 d-inline-block">
                                            {{ __('Post Your First Listing') }}
                                        </a>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="min-width:200px;">{{ __('Listing') }}</th>
                                                    <th class="text-center">{{ __('Status') }}</th>
                                                    <th class="text-center">
                                                        <i class="las la-eye"></i> {{ __('Views') }}
                                                    </th>
                                                    <th class="text-center">
                                                        <i class="las la-heart"></i> {{ __('Saves') }}
                                                    </th>
                                                    <th class="text-center">{{ __('Featured') }}</th>
                                                    <th class="text-center">{{ __('Boosted') }}</th>
                                                    <th class="text-center">{{ __('Posted') }}</th>
                                                    <th class="text-center">{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($listings as $listing)
                                                    @php
                                                        $isActive  = $listing->status == 1 && $listing->is_published == 1;
                                                        $isBoosted = isset($activeBoostedIds[$listing->id]);
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('frontend.listing.details', $listing->slug) }}"
                                                               class="text-dark text-decoration-none fw-semibold"
                                                               style="font-size:14px;"
                                                               target="_blank">
                                                                {{ \Illuminate\Support\Str::limit($listing->title, 55) }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($isActive)
                                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center fw-semibold" style="color:#334155;">
                                                            {{ number_format($listing->view ?? 0) }}
                                                        </td>
                                                        <td class="text-center fw-semibold" style="color:#334155;">
                                                            {{ number_format($listing->listing_favorites_count) }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if($listing->is_featured)
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="las la-star"></i> {{ __('Featured') }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted" style="font-size:12px;">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($isBoosted)
                                                                <span class="badge bg-info text-dark">
                                                                    <i class="las la-rocket"></i> {{ __('Boosted') }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted" style="font-size:12px;">—</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center" style="font-size:12px;white-space:nowrap;color:#64748b;">
                                                            {{ optional($listing->created_at)->format('d M Y') }}
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex gap-1 justify-content-center">
                                                                <a href="{{ route('user.edit.listing', $listing->id) }}"
                                                                   class="btn btn-sm btn-outline-primary px-2 py-1"
                                                                   title="{{ __('Edit') }}">
                                                                    <i class="las la-edit"></i>
                                                                </a>
                                                                <a href="{{ route('frontend.listing.details', $listing->slug) }}"
                                                                   class="btn btn-sm btn-outline-secondary px-2 py-1"
                                                                   target="_blank"
                                                                   title="{{ __('View') }}">
                                                                    <i class="las la-external-link-alt"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- ── Promotion prompt ──────────────────────────────── --}}
                                    <div class="mt-4 p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <div>
                                                <strong style="font-size:14px;">{{ __('Boost your visibility') }}</strong>
                                                <p class="mb-0 text-muted" style="font-size:13px;">
                                                    {{ __('Boost or Feature a listing to increase views and get more enquiries.') }}
                                                </p>
                                            </div>
                                            <div class="d-flex gap-2 flex-wrap ms-auto">
                                                <a href="{{ route('user.featuredAds.packages') }}" class="cmn-btn">
                                                    <i class="las la-star me-1"></i> {{ __('Feature a Listing') }}
                                                </a>
                                                <a href="{{ route('user.all.listing') }}" class="red-btn">
                                                    <i class="las la-rocket me-1"></i> {{ __('Boost a Listing') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>{{-- /main-body --}}
                    </div>{{-- /down-body-wraper --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
