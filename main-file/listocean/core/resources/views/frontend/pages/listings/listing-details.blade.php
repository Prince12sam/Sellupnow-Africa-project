@extends('frontend.layout.master')
@section('page-title')
    <?php
    $page_info = request()->url();
    $str = explode("/",request()->url());
    $page_info = $str[count($str)-2];
    ?>
    {{ __(ucwords(str_replace("-", " ", $page_info))) }}
@endsection

@section('page-meta-data')
    {!!  render_page_meta_data_for_listing($listing) !!}
@endsection
@section('style')
    <style>
        .recentImg {
            height: 72px !important;
            width: 72px !important;
        }
        .phone_number_hide_show {
            display: flex;
            flex-direction: row-reverse;
            font-size: 18px;
            font-weight: 600;
            justify-content: flex-end;
            gap: 7px;
        }
        .select2-container {
            z-index: 900000;
        }
        img.no-image {
            /* width: auto; */
            max-width: 400px;
            margin: auto;
        }
        .btn-group-sm>.btn, .btn-sm {
            padding: .25rem 0;
            font-size: .875rem;
            border-radius: .2rem;
        }

        .slick_slider_item {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: max-content;
        }

        .slick_slider_item a {
            display: flex;
            align-items: center;
            height: 40px;
            border-radius: 20px;
            background-color: rgb(243, 243, 247);
            padding: 8px 16px 8px 12px;
            font-size: 15px;
            font-weight: initial;
            line-height: 16px;
            letter-spacing: 0.25px;
            transition: all;
        }



        .sliderArrow {
            position: relative;
        }

        .sliderArrow .prev-icon,
        .sliderArrow .next-icon {
            cursor: pointer;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .sliderArrow .prev-icon {
            left: 10px; /* Adjust this value as needed */
        }

        .sliderArrow .next-icon {
            right: 10px; /* Adjust this value as needed */
        }

        .sliderArrow .prev-icon i,
        .sliderArrow .next-icon i {
            font-size: 24px; /* Adjust the size of the icon */
        }

        @media (max-width: 576px) {
            .sliderArrow .prev-icon,
            .sliderArrow .next-icon {
                width: 30px;
                height: 30px;
            }
            .sliderArrow .prev-icon i,
            .sliderArrow .next-icon i {
                font-size: 18px;
            }
        }

        .zoom-img {
            width: 100%;
            display: block;
        }

        .sliderArrow .prev-icon, .sliderArrow .next-icon {
            width: 30px;
            height: 30px;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/magnific-popup.min.css')}}">
@endsection
@section('content')
    <!--Listing Details-->
    <div class="proDetails section-padding2">
        <div class="container-1310">
            <div class="bradecrumb-wraper-div">
                <x-breadcrumb.user-profile-breadcrumb
                    :title="''"
                    :innerTitle="__('Listing Details')"
                    :subInnerTitle="''"
                    :chidInnerTitle="''"
                    :routeName="'#'"
                    :subRouteName="'#'"
                />
                <x-validation.frontend-error/>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-8 ">
                    <div class="short-description">
                        <div class="left-part mb-4">
                            <div class="product-name-price">
                                <div class="product-name">{{ $listing->title }}</div>
                                <div class="right-part text-right">
                                    <div class="price text-end"><span>{{ float_amount_with_currency_symbol($listing->price) }}</span>
                                        @if($listing->negotiable === 1)
                                            <div class="token">{{ __('NEGOTIABLE') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="date-location">
                                <span>{{ __('Posted on') }}  <span class="posted">{{ \Carbon\Carbon::parse($listing->created_at)->format('j F Y') }}</span></span>
                                <span class="vartical-devider"></span>
                                <span>{{ get_static_option('listing_location_title') ?? __('Location') }}
                                     <span class="posted"> {{ userListingLocation($listing) }} </span>
                                </span>
                            </div>


                        </div>

                    </div>

                    <!-- Image Slider -->
                    <div class="product-view-wrap" id="myTabContent">
                        <div class="shop-details-gallery-slider global-slick-init slider-inner-margin sliderArrow"
                             data-asNavFor=".shop-details-gallery-nav"
                             data-infinite="true"
                             data-arrows="true"
                             data-dots="false"
                             data-slidesToShow="1"
                             data-swipeToSlide="true"
                             data-fade="true"
                             data-autoplay="false"
                             data-autoplaySpeed="3000"
                             data-prevArrow='<div class="prev-icon"><i class="las la-angle-left"></i></div>'
                             data-nextArrow='<div class="next-icon"><i class="las la-angle-right"></i></div>'
                             data-responsive='[{"breakpoint": 1800,"settings": {"slidesToShow": 1}},{"breakpoint": 1600,"settings": {"slidesToShow": 1}},{"breakpoint": 1400,"settings": {"slidesToShow": 1}},{"breakpoint": 1200,"settings": {"slidesToShow": 1}},{"breakpoint": 991,"settings": {"slidesToShow": 1}},{"breakpoint": 768, "settings": {"slidesToShow": 1}},{"breakpoint": 576, "settings": {"slidesToShow": 1}}]'>

                        @if(!is_null($listing->gallery_images))
                                @php
                                    $thumb_image = $listing->image;
                                    $gallery_images = $listing->gallery_images;
                                    $all_images_list = $thumb_image . '|' . $gallery_images;
                                    $images = explode("|", $all_images_list);
                                @endphp
                                @foreach($images as $img)
                                    @if(!empty($img))
                                        <div class="single-main-image">
                                            <a href="#"
                                               data-mfp-src="{{ get_image_url_id_wise($img) }}"
                                               class="long-img image-link" tabindex="-1">
                                                {!! render_image_markup_by_attachment_id($img) !!}
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="single-main-image">
                                    <a href="#" class="long-img">
                                        {!! render_image_markup_by_attachment_id($listing->image) !!}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <!-- Nav -->
                        @if(!is_null($listing->gallery_images))
                        <div class="thumb-wrap">
                            <div class="shop-details-gallery-nav global-slick-init slider-inner-margin sliderArrow"
                                 data-asNavFor=".shop-details-gallery-slider"
                                 data-focusOnSelect="true"
                                 data-infinite="false"
                                 data-arrows="false"
                                 data-dots="false"
                                 data-slidesToShow="6"
                                 data-autoplay="false"
                                 data-swipeToSlide="true"
                                 data-prevArrow='<div class="prev-icon"><i class="las la-angle-left"></i></div>'
                                 data-nextArrow='<div class="next-icon"><i class="las la-angle-right"></i></div>'
                                 data-responsive='[{"breakpoint": 1200,"settings": {"slidesToShow": 5}}, {"breakpoint": 992,"settings": {"slidesToShow": 4}}, {"breakpoint": 450,"settings": {"slidesToShow": 3}}, {"breakpoint": 350,"settings": {"slidesToShow": 2}}]'>

                                @if(!is_null($listing->gallery_images))
                                    @php
                                        $thumb_image = $listing->image;
                                        $gallery_images = $listing->gallery_images;
                                        $all_images_list = $thumb_image . '|' . $gallery_images;
                                        $images = explode("|", $all_images_list);
                                    @endphp
                                    @foreach($images as $img)
                                        @if(!empty($img))
                                            <div class="single-thumb">
                                                <a class="thumb-link"
                                                   data-mfp-src="{{ get_image_url_id_wise($img) }}"
                                                   data-toggle="tab"
                                                   href="#image-{{$img}}">
                                                    {!! render_image_markup_by_attachment_id($img) !!}
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    @if(!empty($listing->gallery_images))
                                        <div class="single-thumb">
                                            <a class="thumb-link" data-toggle="tab" href="#">
                                                {!! render_image_markup_by_attachment_id($listing->image) !!}
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Ad slot: under gallery -->
                    <div class="googleAdd-wraper after-product-slider">
                        <x-ads.ad-slot placement="listing_details_under_gallery" :listing-id="$listing->id" />
                        @if(!empty($right_add_markup))
                        <div class="add">
                            <div class="text-{{$right_custom_container}} single-banner-ads ads-banner-box" id="home_advertisement_store">
                                <input type="hidden" id="add_id" value="{{$right_add_id}}">
                                {!! $right_add_markup !!}
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- proDescription -->
                    <div class="proDescription box-shadow1">
                        <!-- Top -->
                        <div class="descriptionTop">
                            <div class="row gy-4">
                                @if(!empty($listing->condition))
                                <div class="col-4">
                                    {{ __('Condition:') }} <span class="text-bold"> {{ $listing->condition }} </span>
                                </div>
                                @endif
                                @if(!empty($listing->authenticity))
                                <div class="col-4">
                                    {{ __('Authenticity:') }} <span class="text-bold"> {{ $listing->authenticity }} </span>
                                </div>
                                @endif
                                @if(!empty($listing->brand))
                                    <div class="col-4">
                                        {{ __('Brand:') }} <span class="text-bold">{{ $listing->brand?->title }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="devider"></div>
                        <!-- Mid -->
                        <div class="descriptionMid">
                            <h4 class="disTittle">{{ get_static_option('listing_description_title') ?? __('Description') }}</h4>
                            <p class="product__details__para" id="description">{!! $listing->description !!}</p>
                            <button id="showMoreButton" class="show-more-btn">{{ __('Show More') }}</button>
                        </div>
                        <!-- Footer -->

                        <div class="descriptionFooter">
                            <h4 class="disTittle">{{ get_static_option('listing_tag_title') ?? __('Tags') }}</h4>
                            @if(isset($listing->tags) && count($listing->tags) > 0)
                                @if(!empty($listing->tags))
                                    <div class="tags">
                                        <form id="filter_with_listing_page_tag" action="{{ url(get_static_option('listing_filter_page_url') ?? '/listings') }}" method="get">
                                            <input type="hidden" name="tag_id" id="tag_id" value="" />
                                            @foreach($listing->tags as $tag)
                                                <a href="#" class="submit_form_listing_filter_tag" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                                            @endforeach
                                        </form>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Ad slot: listing detail left column (below description) --}}
                    <x-ads.ad-slot placement="listing_details_left" :listing-id="$listing->id" class="mt-3 mb-3" />

                    <!--for mobile device user info -->
                    <div class="seller-part mt-3 d-md-none">
                        <x-listings.user-listing-phone-for-responsive :listing="$listing"/>
                        <x-listings.listing-details-page-user-info :listing="$listing" :userTotalListings="$user_total_listings"/>
                    </div>

                    <!--Relevant Ads-->
                    @include('frontend.pages.listings.relevant-listing')

                    @if(get_static_option('ai_recommendations_enabled') && in_array(strtolower(trim(get_static_option('ai_recommendations_enabled'))), ['1','true','yes','on','enabled']))
                    {{-- AI-powered recommendations (lazy-loaded via AJAX) --}}
                    <div id="aiRecommendationsSection" class="relevant-ads box-shadow1 mt-4" style="display:none;">
                        <h4 class="disTittle d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6366f1" viewBox="0 0 16 16"><path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828l.645-1.937z"/></svg>
                            {{ __('AI Picks For You') }}
                        </h4>
                        <div id="aiRecommendationsWrapper" class="add-wraper relevant-listing-wrapper"></div>
                    </div>
                    @endif

                </div>
                <div class="col-xl-4 col-lg-4 col-md-4">
                    <div class="seller-part">
                        <!--user info -->
                         <div class="d-none d-md-block">
                             <x-listings.user-listing-phone :listing="$listing"/>
                             <x-listings.listing-details-page-user-info :listing="$listing" :userTotalListings="$user_total_listings"/>
                         </div>

                        {{-- Ad slot: listing detail sidebar --}}
                        <x-ads.ad-slot placement="listing_details_right" :listing-id="$listing->id" class="mb-3" />
                        <!--Adds left (legacy adsense)-->
                        @if(get_static_option('google_adsense_status') == 'on' && !empty($add_markup))
                            <div class="googleAdd-wraper">
                                <div class="add">
                                    <div class="text-{{$custom_container}} single-banner-ads ads-banner-box" id="home_advertisement_store">
                                        <input type="hidden" id="add_id" value="{{$add_id}}">
                                        {!! $add_markup !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(get_static_option('safety_tips_info') !== null)
                            <div class="safety-tips">
                                <h3 class="head5">{{ get_static_option('listing_safety_tips_title') ?? __('Safety Tips') }}</h3>
                                <div class="safety-wraper">
                                    {!! get_static_option('safety_tips_info') !!}
                                </div>
                            </div>
                        @endif

                        {{-- Buy with Escrow CTA — shown below safety tips to all non-owners --}}
                        @if(!empty($listing->escrow_enabled) && !empty($listing->price) && $listing->price > 0 && !(auth()->check() && auth()->id() == $listing->user_id))
                            <div class="safety-tips mt-3">
                                <a href="{{ auth()->check() ? route('user.escrow.start', $listing->slug) : route('user.login') }}"
                                   class="cmn-btn w-100 d-flex align-items-center justify-content-center gap-2">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ __('Buy with Escrow') }}
                                </a>
                                <small class="d-block text-muted text-center mt-1" style="font-size:11px;">
                                    {{ __('Payment held securely until you confirm delivery') }}
                                </small>
                            </div>
                        @endif

                        <div class="share-on-wraper">
                            <div class="d-flex gap-3 align-items-center mb-3">
                                <div class="text-center w-50 report-btn listing-details-page-favorite">
                                    <x-listings.favorite-item-add-remove-for-details-page :favorite="$listing->id ?? 0" />
                                </div>
                                <div class="report-btn w-50 text-center">
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#reportModal">
                                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 10H15L10.5 5.5L15 1H1V17" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span id="addReportModal">{{ __('Report') }}</span>
                                    </a>
                                </div>
                            </div>

                            <div class="share-on">
                                <span class="social-icons">
                                     @php
                                         $image_url = get_attachment_image_by_id($listing->image);
                                         $img_url = $image_url['img_url'] ?? '';
                                     @endphp
                                    {!! single_post_share(route('frontend.listing.details',$listing->slug), $listing->title, $img_url) !!}
                                </span>
                            </div>
                        </div>

                        @include('frontend.pages.listings.frontend-business-hours')
                        @include('frontend.pages.listings.frontend-enquiry-form')

                        <div class="map-wraper box-shadow1">
                            <h3 class="head5">{{ __('Map') }}</h3>
                            <p>{{ $listing->address }}</p>
                            <div class="map">
                                @if (!empty(get_static_option("google_map_settings_on_off")))
                                    <div id="single-map-canvas" style="height: 230px; width: 100%; position: relative; overflow: hidden;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Ad slot: listing video (per-listing or global fallback) — shown under the map -->
                        <x-ads.ad-slot placement="listing_video_{{ $listing->id }}" :listing-id="$listing->id" class="mt-3" />
                        <x-ads.ad-slot placement="listing_video_slot" :listing-id="$listing->id" class="mt-3" />

                        @php
                            // Step 1 – get the active placement record
                            $_placement = \Illuminate\Support\Facades\DB::table('reel_ad_placements')
                                ->where('placement', 'listing_detail_video')
                                ->where('status', 1)
                                ->where(function ($q) { $q->whereNull('start_at')->orWhere('start_at', '<=', now()); })
                                ->where(function ($q) { $q->whereNull('end_at')->orWhere('end_at', '>', now()); })
                                ->orderByDesc('id')
                                ->first();

                            // Step 2 – resolve the video source based on reel_type
                            $videoPlacement = null;
                            if ($_placement) {
                                if ($_placement->reel_type === 'ad_video') {
                                    // Source: ad_videos table (approved, not rejected)
                                    $src = \Illuminate\Support\Facades\DB::table('ad_videos')
                                        ->where('id', $_placement->reel_id)
                                        ->where('is_approved', 1)
                                        ->where('is_rejected', 0)
                                        ->first();
                                    if ($src) {
                                        $videoPlacement = $src;
                                    } else {
                                        // Fallback: try listings in case reel_id was set to a listing ID
                                        $src = \Illuminate\Support\Facades\DB::table('listings')
                                            ->where('id', $_placement->reel_id)
                                            ->where('status', 1)
                                            ->whereNotNull('video_url')
                                            ->where('video_url', '!=', '')
                                            ->select('id', 'title', 'video_url')
                                            ->first();
                                        if ($src) {
                                            $videoPlacement = (object)[
                                                'video_url'     => $src->video_url,
                                                'thumbnail_url' => null,
                                                'caption'       => null,
                                                'cta_text'      => null,
                                                'cta_url'       => null,
                                            ];
                                        }
                                    }
                                } elseif ($_placement->reel_type === 'listing') {
                                    // Source: listing's own video_url
                                    $src = \Illuminate\Support\Facades\DB::table('listings')
                                        ->where('id', $_placement->reel_id)
                                        ->where('status', 1)
                                        ->whereNotNull('video_url')
                                        ->where('video_url', '!=', '')
                                        ->select('id', 'title', 'video_url')
                                        ->first();
                                    if ($src) {
                                        $videoPlacement = (object)[
                                            'video_url'     => $src->video_url,
                                            'thumbnail_url' => null,
                                            'caption'       => null,
                                            'cta_text'      => null,
                                            'cta_url'       => null,
                                        ];
                                    }
                                }
                            }
                        @endphp
                        @if($videoPlacement)
                            <div class="map-wraper box-shadow1">
                                <h3 class="head5">{{ __('Video') }}</h3>
                                <div style="position:relative;width:100%;background:#000;border-radius:4px;overflow:hidden;">
                                    <video
                                        src="{{ Str::startsWith($videoPlacement->video_url, 'http') ? $videoPlacement->video_url : asset('storage/' . ltrim($videoPlacement->video_url, '/')) }}"
                                        @if(!empty($videoPlacement->thumbnail_url))
                                        poster="{{ Str::startsWith($videoPlacement->thumbnail_url, 'http') ? $videoPlacement->thumbnail_url : asset('storage/' . ltrim($videoPlacement->thumbnail_url, '/')) }}"
                                        @endif
                                        controls autoplay muted loop playsinline
                                        style="width:100%;max-height:370px;display:block;"
                                    ></video>
                                    @if(!empty($videoPlacement->cta_url) && !empty($videoPlacement->cta_text))
                                        <a href="{{ $videoPlacement->cta_url }}" target="_blank" rel="noopener"
                                           style="position:absolute;bottom:12px;right:12px;background:var(--main-color-one,#524EB7);color:#fff;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;">
                                            {{ $videoPlacement->cta_text }}
                                        </a>
                                    @endif
                                </div>
                                @if(!empty($videoPlacement->caption))
                                    <p class="text-muted mt-2 mb-0" style="font-size:12px;">{{ $videoPlacement->caption }}</p>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.pages.listings.listing-report-add-modal')
    <x-frontend.login/>
@endsection
@section('scripts')
@if(!empty(get_static_option('google_map_settings_on_off')))
    <x-map.google-map-listing-details-page-js :lat="$listing->lat ?? 0" :lon="$listing->lon ?? 0"/>
@endif
@if($user_enquiry_form === true)
    <x-listings.enquiry-form-submit-js/>
@endif

<x-listings.listing-report-add-js/>
<script src="{{asset('assets/frontend/js/jquery.magnific-popup.min.js')}}"></script>
    <script>
        (function($){
            "use strict";

            $(document).ready(function(){

                // Initialize Magnific Popup
                $('.image-link').magnificPopup({
                    type: 'image',
                    gallery: {
                        enabled: true
                    },
                    zoom: {
                        enabled: true,
                        duration: 300,
                        easing: 'ease-in-out'
                    }
                });


                let page = 1;
                $(document).on('click', '#load-more-ads', function() {
                    page++;
                    let listingId = $(this).data('listing-id');
                    $.ajax({
                        url: "{{ route('frontend.listing.load-more-relevant') }}",
                        type: "POST",
                        data: {
                            page: page,
                            listing_id: listingId
                        },
                        success: function(response) {
                            if (response.html) {
                                $('.relevant-listing-wrapper').append(response.html);
                            }

                            // Check if total relevant items is 0
                            if (response.total_relevant_items === 0) {
                                $('#load-more-ads').prop('disabled', true); // Disable the button
                                $('#load-more-ads').hide(); // hide the button
                            } else {
                                $('#load-more-ads').prop('disabled', false); // Enable the button
                            }

                        },
                        error: function(xhr) {
                        }
                    });
                });


                // Toggle for business hour
                $(".hours-wraper").slideToggle(300);
                $(".business-hour .business-head").on('click', function(){
                    $(".hours-wraper").slideToggle(300)
                });

                $(".enquiry-wraper").show();
                $(".enquiry-hour .enquiry-head").on('click', function() {
                    $(".enquiry-wraper").slideToggle(300);
                });

                let description = document.getElementById('description');
                let showMoreButton = document.getElementById('showMoreButton');
                $('#showMoreButton').show();
                let isExpanded = false;
                let originalContent = description.textContent;
                if (description.textContent.length > 700) {
                    description.textContent = description.textContent.substring(0, 700) + '...';
                }else {
                    $('#showMoreButton').hide();
                }
                showMoreButton.addEventListener('click', function() {
                    if (!isExpanded) {
                        description.textContent = originalContent;
                        showMoreButton.textContent = 'Show Less';
                    } else {
                        description.textContent = description.textContent.substring(0, 700) + '...';
                        showMoreButton.textContent = 'Show More';
                    }
                    isExpanded = !isExpanded;
                });


                // for web
                $('#phoneNumber').hide();
                $('#default_phone_number_show').show;
                $('.show-number').show();
                $(document).on('click', '#userPhoneNumberBtn', function(event) {
                    event.preventDefault();
                    $('#default_phone_number_show').hide();
                    $('#phoneNumber').show();
                    $('.show-number').hide();
                });

                // for mobile responsive
                $('#phoneNumberForResponsive').hide();
                $('#default_phone_number_show_for_responsive').show();
                $(document).on('click', '#userPhoneNumberBtnForResponsive', function(event) {
                    event.preventDefault();
                    $('#default_phone_number_show_for_responsive').hide();
                    $('#phoneNumberForResponsive').show();
                    $('.show-number').hide();
                });

                // for mobile responsive with call to number
                $(document).on('click', '#phoneNumberForResponsive', function(event) {
                    event.preventDefault();
                    let phoneNumber = $('#phoneNumber').text().trim();
                    let tempLink = document.createElement('a');
                    tempLink.href = 'tel:' + phoneNumber;
                    document.body.appendChild(tempLink);
                    tempLink.trigger('click');
                    document.body.removeChild(tempLink);
                });

            });
        })(jQuery);
    </script>

    @if(get_static_option('ai_recommendations_enabled') && in_array(strtolower(trim(get_static_option('ai_recommendations_enabled'))), ['1','true','yes','on','enabled']))
    <script>
    (function () {
        'use strict';
        // Lazy-load AI recommendations after main content is ready
        function loadAiRecommendations() {
            var section  = document.getElementById('aiRecommendationsSection');
            var wrapper  = document.getElementById('aiRecommendationsWrapper');
            if (!section || !wrapper) return;

            var formData = new FormData();
            formData.append('listing_id', '{{ $listing->id }}');
            formData.append('title',      '{{ addslashes($listing->title) }}');
            formData.append('category', '{{ optional($listing->category)->name ?? optional($listing->subcategory)->name ?? '' }}');
            formData.append('_token',     '{{ csrf_token() }}');

            fetch('{{ route("frontend.ai.recommendations") }}', {
                method: 'POST',
                body:   formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.count && data.count > 0 && data.html) {
                    wrapper.innerHTML = data.html;
                    section.style.display = 'block';
                }
            })
            .catch(function () { /* silent fail — not critical */ });
        }

        // Load after a short delay so it doesn't block LCP
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { setTimeout(loadAiRecommendations, 800); });
        } else {
            setTimeout(loadAiRecommendations, 800);
        }
    }());
    </script>
    @endif
@endsection
