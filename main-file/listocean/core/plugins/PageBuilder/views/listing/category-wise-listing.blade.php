<!-- top Listings  S t a r t -->
<section class="featureListing" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color:{{$section_bg}}">
    <div class="container-1440">
        <div class="titleWithBtn d-flex justify-content-between align-items-center mb-40">
            <h2 class="head3">{{ $section_title ?? '' }} {{ $category_name ?? 'Category Wise Listing' }}</h2>
            <form id="filter_with_listing_page_category_wise_listing" action="{{ url(get_static_option('listing_filter_page_url') ?? '/listings') }}" method="get">
                 <input type="hidden" name="cat" value="{{$category_id}}"/>
                  <a href="#" class="submit_form_listing_filter_category_wise_listing see-all">{{ $explore_text }} <i class="las la-angle-right"></i></a>
            </form>
        </div>
        <div class="slider-inner-margin">
            <!-- Single -->
            <x-listings.listing-single-list-view :listings="$listings"/>
        </div>
    </div>
</section>
<!-- End-of top -->
