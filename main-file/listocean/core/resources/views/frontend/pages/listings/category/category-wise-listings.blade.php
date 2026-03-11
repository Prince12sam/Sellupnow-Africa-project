@extends('frontend.layout.master')
@section('site-title')
    @if($category !='')
        {{ $category->name }}
    @endif
@endsection
@section('page-title')
    @if($category !='')
        {{ $category->name }}
    @endif
@endsection
@section('inner-title')
    @if($category !='')
        {{ $category->name }}
    @endif
@endsection
@section('page-meta-data')
    {!!  render_page_meta_data_for_category($category) !!}
@endsection
@section('content')
    <div class="catagory-wise-listing section-padding2">
        <div class="container-1440">
            <x-breadcrumb.user-profile-breadcrumb
                :title="''"
                :innerTitle="$category->name"
                :subInnerTitle="''"
                :chidInnerTitle="''"
                :routeName="'#'"
                :subRouteName="'#'"
            />

            <x-validation.frontend-error/>

            {{-- Ad slot: top of category page --}}
            <x-ads.ad-slot placement="category_top_banner" class="mb-4 mt-2" />

                @if(!is_null($category->description))
                    <div class="row g-4 mt-4">
                        <div class="col-12">
                            <div class="category_info_new mb-5 mt-2">
                                {!! $category->description !!}
                            </div>
                        </div>
                    </div>
                @endif

                    <h3 class="catagory-wise-title">{{ sprintf(__('Available Listing Categories in :category', ['category' => $category->name])) }}</h3>
                    <div class="catagory-wise-list-wraper exploreCategories">
                        <div id="services_sub_category_load_wrap">
                            <div class="services_sub_category_load_wraper mt-4">
                                @if($subcategory_under_category->count() != 0)
                                    @foreach($subcategory_under_category as $sub_cat)
                                        <!-- Single -->
                                        <div class="singleCategories categories{{ ($loop->index % 9) + 1 }} wow fadeInUp" data-wow-delay="0.1s">
                                                <div class="categoriIcon text-center">
                                                    <a href="{{ route('frontend.show.listing.by.subcategory', $sub_cat->slug ?? 'x') }}">
                                                        {!! render_image_markup_by_attachment_id($sub_cat->image) !!}
                                                    </a>
                                                </div>
                                                <div class="categorie-text">
                                                    <h4 class="text-center">
                                                        <a href="{{ route('frontend.show.listing.by.subcategory', $sub_cat->slug ?? 'x') }}" class="title oneLine mt-2">
                                                            {{ $sub_cat->name }}
                                                        </a>
                                                    </h4>
                                                    <p> {{ __('Listing :total_listings', ['total_listings' => $sub_cat->total_listings ?? 0]) }}</p>
                                                </div>
                                        </div>
                                    @endforeach
                                @else
                                    <span>{{ __('No Category Yet') }}</span>
                                @endif
                            </div>
                            <div class="load-more-button">
                                @if($subcategory_under_category->count() >20)
                                    <div class="load_more_button_warp">
                                        <a href="#" id="load_more_btn" data-total="20" class="new-cmn-btn rounded-red-btn">{{__('Load More')}}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                <div class="row">
                    <div class="col-lg-12">
                        <section class="featureListing mb-5 mt-5">
                            <div class="container-1440">
                                <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
                                    <h3 class="catagory-wise-title">{{ sprintf(__('Available Listings in %s'), $category->name) }} </h3>
                                    <form id="filter_with_listing_page_category" action="{{ url('/') .'/'. get_static_option('listing_filter_page_url') ?? url('/listings') }}" method="get">
                                        <input type="hidden" name="cat" value="{{$category->id}}"/>
                                        <a href="#" id="submit_form_listing_filter_category" class="see-all">{{ __('See All') }}<i class="las la-angle-right"></i></a>
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
                        catId: "{{$category->id}}"
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
