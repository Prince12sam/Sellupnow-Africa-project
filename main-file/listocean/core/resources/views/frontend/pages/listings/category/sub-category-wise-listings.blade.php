@extends('frontend.layout.master')
@section('site-title')
    @if($subcategory !='')
        {{ $subcategory->name }}
    @endif
@endsection
@section('page-title')
    @if($subcategory !='')
        {{ $subcategory->name }}
    @endif
@endsection
@section('inner-title')
    @if($subcategory !='')
        {{ $subcategory->name }}
    @endif
@endsection
@section('page-meta-data')
    {!!  render_page_meta_data_for_subcategory($subcategory) !!}
@endsection
@section('content')
    <div class="catagory-wise-listing section-padding2">
        <div class="container-1440">
            <x-breadcrumb.user-profile-breadcrumb
                :title="''"
                :innerTitle="$subcategory->category?->name"
                :subInnerTitle="$subcategory->name"
                :chidInnerTitle="''"
                :routeName="route('frontend.show.listing.by.category', $subcategory->category?->slug ?? 'x')"
                :subRouteName="'#'"
            />

            <x-validation.frontend-error/>

            {{-- Ad slot: top of subcategory page --}}
            <x-ads.ad-slot placement="category_top_banner" class="mb-4 mt-2" />

            @if(!is_null($subcategory->description))
            <div class="row g-4 mt-4">
                <div class="col-12">
                    <div class="category_info_new mb-5 mt-2">
                        {!! $subcategory->description !!}
                    </div>
                </div>
            </div>
            @endif

            <h3 class="catagory-wise-title">{{ sprintf(__('Available Listing Sub Categories in :subcategory', ['subcategory' => $subcategory->name])) }}</h3>
            <div class="catagory-wise-list-wraper exploreCategories">
                <div id="services_sub_category_load_wrap">
                    <div class="services_sub_category_load_wraper mt-4">
                        @if($child_category_under_category->count() != 0)
                            @foreach($child_category_under_category as $child_cat)
                              <div class="singleCategories categories{{ ($loop->index % 9) + 1 }} wow fadeInUp" data-wow-delay="0.1s">
                                            <div class="categoriIcon text-center">
                                                <a href="{{ route('frontend.show.listing.by.child.category', $child_cat->slug ?? 'x') }}">
                                                       {!! render_image_markup_by_attachment_id($child_cat->image) !!}
                                                </a>
                                            </div>
                                            <div class="categorie-text">
                                                <h4 class="text-center">
                                                    <a href="{{ route('frontend.show.listing.by.child.category', $child_cat->slug ?? 'x') }}" class="title oneLine mt-2">
                                                    {{ $child_cat->name }}
                                                    </a>
                                                </h4>
                                                <p> {{ __('Listing :total_listings', ['total_listings' => $child_cat->total_listings ?? 0]) }}</p>
                                            </div>
                                    </div>
                            @endforeach
                        @else
                            <span>{{ __('No Category Yet') }}</span>
                        @endif
                    </div>
                    <div class="load-more-button">
                        @if($child_category_under_category->count() >20)
                            <div class="load_more_button_warp">
                                <a href="#" id="load_more_btn" data-total="20" class="new-cmn-btn rounded-red-btn">{{__('Load More')}}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Featured listings section --}}
            @if(isset($featured_listings) && $featured_listings->count() > 0)
            <div class="row mb-2">
                <div class="col-lg-12">
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
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <section class="featureListing mb-5 mt-5">
                        <div class="container-1440">
                            <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                                <h3 class="catagory-wise-title">{{ sprintf(__('Available Listings in %s'), $subcategory->name) }}</h3>
                                <form id="filter_with_listing_page_subcategory" action="{{ url('/') .'/'. get_static_option('listing_filter_page_url') ?? url('/listings') }}" method="get">
                                    <input type="hidden" name="cat" value="{{$subcategory->category_id}}"/>
                                    <input type="hidden" name="subcat" value="{{$subcategory->id}}"/>
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
@section('scripts')
    <script>
        (function($){
            "use strict";

            $(document).on('click','#load_more_btn',function(e){
                e.preventDefault();

                let totalNo = $(this).data('total');
                let el = $(this);
                let container = $('#services_sub_category_load_wrap > .row');

                $.ajax({
                    type: "POST",
                    url: "{{route('frontend.listing.load.more.subcategories')}}",
                    beforeSend: function(e){
                        el.text("{{__('loading...')}}")
                    },
                    data : {
                        _token: "{{csrf_token()}}",
                        total: totalNo,
                        catId: "{{$subcategory->id}}"
                    },
                    success: function(data){

                        el.text("{{__('Load More')}}");
                        if(data.markup === ''){
                            el.hide();
                            container.append("<div class='col-lg-12'><div class='text-center text-warning mt-3'>{{__('no more subcategory found')}}</div></div>");
                            return;
                        }

                        $('#load_more_btn').data('total',data.total);

                        container.append(data.markup);
                    }
                });
            });
        })(jQuery);
    </script>
@endsection
