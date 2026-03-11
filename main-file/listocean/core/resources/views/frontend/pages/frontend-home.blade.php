@extends('frontend.layout.master')
@section('content')
    {{-- Homepage hero banner ad slot --}}
    <x-ads.ad-slot placement="homepage_hero_banner" class="container-fluid px-0 mb-0" />

    @include('frontend.pages.dynamic.partials.dynamic-page-builder-part',['page_post' => $page_details])

    {{-- Homepage banner strip rendered directly after the hero/page-builder section --}}
    <div class="container mt-3 mb-2">
        <x-ads.ad-slot placement="sellupnow:homepage_after_hero" />
    </div>

    {{-- Featured Listing section (most viewed active listings) --}}
    @if(isset($topListings) && $topListings->count() > 0)
    <section class="featureListing mb-4 mt-4">
        <div class="container-1440">
            <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                <h3 class="catagory-wise-title">
                    <svg width="16" height="16" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right:6px;">
                        <path d="M4 0V3.88889H7L3 10V6.11111H0L4 0Z" fill="#F59E0B"/>
                    </svg>
                    {{ __('Featured Listing') }}
                </h3>
                <a href="{{ route('frontend.show.listing.by.category') }}" class="btn btn-sm" style="font-size:12px;color:var(--main-color-one,#524EB7);">
                    {{ __('View all listings →') }}
                </a>
            </div>
            <div class="slider-inner-margin">
                <x-listings.listing-single :listings="$topListings"/>
            </div>
        </div>
    </section>
    @endif

    {{-- Homepage footer banner ad slot --}}
    <div class="container mt-4 mb-2">
        <x-ads.ad-slot placement="homepage_footer_banner" />
    </div>
@endsection
