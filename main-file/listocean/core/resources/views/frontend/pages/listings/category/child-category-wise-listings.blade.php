@extends('frontend.layout.master')
@section('site-title')
    @if($child_category !='')
        {{ $child_category->name }}
    @endif
@endsection
@section('page-title')
    @if($child_category !='')
        {{ $child_category->name }}
    @endif
@endsection
@section('inner-title')
    @if($child_category !='')
        {{ $child_category->name }}
    @endif
@endsection
@section('page-meta-data')
    {!!  render_page_meta_data_for_child_category($child_category) !!}
@endsection
@section('content')
    <div class="catagory-wise-listing section-padding2">
        <div class="container-1440">
            <x-breadcrumb.user-profile-breadcrumb
                :title="''"
                :innerTitle="$child_category->category?->name"
                :subInnerTitle="$child_category->subcategory?->name"
                :chidInnerTitle="$child_category->name"
                :routeName="route('frontend.show.listing.by.category', $child_category->category?->slug ?? 'x')"
                :subRouteName="route('frontend.show.listing.by.subcategory', $child_category->subcategory?->slug ?? 'x')"
            />

            <x-validation.frontend-error/>

            {{-- Ad slot: top of child-category page --}}
            <x-ads.ad-slot placement="category_top_banner" class="mb-4 mt-2" />

            @if(!is_null($child_category->description))
            <div class="row g-4 mt-4">
                <div class="col-12">
                    <div class="category_info_new mb-5 mt-2">
                        {!! $child_category->description !!}
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-lg-12">

                    {{-- Featured listings section --}}
                    @if(isset($featured_listings) && $featured_listings->count() > 0)
                    <section class="featureListing mb-4 mt-4">
                        <div class="container-1440">
                            <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                                <h3 class="catagory-wise-title">
                                    <svg width="16" height="16" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right:6px;">
                                        <path d="M4 0V3.88889H7L3 10V6.11111H0L4 0Z" fill="#F59E0B"/>
                                    </svg>
                                    {{ __('Featured Listings') }}
                                </h3>
                            </div>
                            <div class="slider-inner-margin">
                                <x-listings.listing-single :listings="$featured_listings"/>
                            </div>
                        </div>
                    </section>
                    <hr class="mb-4"/>
                    @endif

                    <section class="featureListing mb-5 mt-5">
                        <div class="container-1440">
                            <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                                <h3 class="catagory-wise-title">{{ sprintf(__('Available Listings in :child_category', ['child_category' => $child_category->name]))  }}</h3>
                                <form id="filter_with_listing_page_subcategory" action="{{ url('/') .'/'. get_static_option('listing_filter_page_url') ?? url('/listings') }}" method="get">
                                    <input type="hidden" name="cat" value="{{$child_category->category_id}}"/>
                                    <input type="hidden" name="subcat" value="{{$child_category->sub_category_id}}"/>
                                    <input type="hidden" name="child_cat" value="{{$child_category->id}}"/>
                                    <a href="#" id="submit_form_listing_filter_subcategory" class="see-all">{{ __('See All') }}<i class="las la-angle-right"></i></a>
                                </form>
                            </div>
                            <div class="slider-inner-margin">
                                <x-listings.listing-single :listings="$all_listings"/>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
         </div>
     </div>
@endsection
