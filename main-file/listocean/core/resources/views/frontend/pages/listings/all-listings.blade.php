@extends('frontend.layout.master')
@section('site-title')
    {{ __('All Listings') }}
@endsection
@section('page-title')
    {{ __('All Listings') }}
@endsection
@section('inner-title')
    {{ __('Browse All Listings') }}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/nouislider.css') }}">
@endsection
@section('content')
<div class="catagory-wise-listing section-padding2 new-style">
    <div class="container-1440">

        <x-breadcrumb.user-profile-breadcrumb
            :title="''"
            :innerTitle="__('All Listings')"
            :subInnerTitle="''"
            :chidInnerTitle="''"
            :routeName="'#'"
            :subRouteName="'#'"
        />

        <x-validation.frontend-error/>

        <form id="search_listings_form" action="{{ route('frontend.all.listings') }}" method="get">
            {{-- Hidden filter state inputs --}}
            <input type="hidden" name="price_range"               id="price_range_value"            value="{{ request('price_range') }}">
            <input type="hidden" name="distance_kilometers_value" id="distance_kilometers_value"     value="{{ request('distance_kilometers_value', 0) }}">
            <input type="hidden" name="autocomplete_address"      id="autocomplete_address"         value="{{ request('autocomplete_address') }}">
            <input type="hidden" name="lat"                       id="latitude"                     value="{{ request('lat', 0) }}">
            <input type="hidden" name="lon"                       id="longitude"                    value="{{ request('lon', 0) }}">
            <input type="hidden" name="location_city_name"        id="location_city_name"           value="{{ request('location_city_name') }}">
            <input type="hidden" name="listing_type_preferences"  id="listing_type_preferences"     value="{{ request('listing_type_preferences') }}">
            <input type="hidden" name="listing_condition"         id="listing_condition"            value="{{ request('listing_condition') }}">
            <input type="hidden" name="date_posted_listing"       id="date_posted_listing"          value="{{ request('date_posted_listing') }}">
            <input type="hidden" name="listing_grid_and_list_view" id="listing_grid_and_list_view"  value="{{ request('listing_grid_and_list_view', 'grid') }}">

            <div class="row g-4 mt-2 catabody-wraper">

                {{-- ==================== SIDEBAR ==================== --}}
                <div class="col-lg-3">
                    <div class="cateSidebar1 d-flex flex-column gap-3">

                        {{-- Location + Distance --}}
                        <div class="catagoriesWraper">
                            <p class="cateTitle fw-semibold mb-2">{{ __('Location') }}</p>
                            <input id="autocomplete" type="text"
                                class="search-input w-100 form-control"
                                placeholder="{{ __('Enter your location') }}"
                                value="{{ request('autocomplete_address') }}">
                            <div class="suburb_section_start mt-3">
                                <label class="cateTitle">
                                    {{ __('Distance') }}:
                                    <span id="slider-value" class="fw-bold">{{ request('distance_kilometers_value', 150) }}</span> km
                                </label>
                                <div id="distance-slider" class="mt-2"></div>
                            </div>
                            <button type="submit" class="filter-btn w-100 mt-3">{{ __('Filter') }}</button>
                        </div>

                        {{-- Category → Subcategory → Child Category --}}
                        <div class="catagoriesWraper">
                            <p class="cateTitle fw-semibold mb-2">{{ __('Category') }}</p>
                            <select id="search_by_category" name="cat" class="search-input w-100 form-select">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach($all_categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('cat') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="search_by_subcategory" name="subcat"
                                class="search-input w-100 form-select mt-2 {{ $all_subcategories->isEmpty() ? 'd-none' : '' }}">
                                <option value="">{{ __('All Subcategories') }}</option>
                                @foreach($all_subcategories as $subcat)
                                    <option value="{{ $subcat->id }}" {{ request('subcat') == $subcat->id ? 'selected' : '' }}>
                                        {{ $subcat->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="search_by_child_category" name="childcat"
                                class="search-input w-100 form-select mt-2 {{ $all_child_categories->isEmpty() ? 'd-none' : '' }}">
                                <option value="">{{ __('All Sub-subcategories') }}</option>
                                @foreach($all_child_categories as $childcat)
                                    <option value="{{ $childcat->id }}" {{ request('childcat') == $childcat->id ? 'selected' : '' }}>
                                        {{ $childcat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div class="catagoriesWraper">
                            <p class="cateTitle fw-semibold mb-2">{{ __('Price Range') }}</p>
                            <div class="price-input">
                                <div class="priceRangeWraper">
                                    <span class="site_currency_symbol">{{ get_static_option('site_currency_symbol') ?? '$' }}</span>
                                    <input type="number" class="input-min form-control" placeholder="0" value="{{ $price_range_min }}">
                                </div>
                                <span class="mx-2 align-self-center">—</span>
                                <div class="priceRangeWraper">
                                    <span class="site_currency_symbol">{{ get_static_option('site_currency_symbol') ?? '$' }}</span>
                                    <input type="number" class="input-max form-control" placeholder="{{ $max_price }}" value="{{ $price_range_max }}">
                                </div>
                            </div>
                            <div id="price-range-slider" class="mt-2 mb-1"></div>
                            <button type="submit" class="filter-btn w-100 mt-2">{{ __('Filter') }}</button>
                        </div>

                        {{-- Types --}}
                        <div class="catagoriesWraper">
                            <p class="cateTitle fw-semibold mb-2">{{ __('Types') }}</p>
                            <ul class="list-unstyled postdate mb-0">
                                <li class="{{ request('listing_type_preferences') === 'featured' ? 'active' : '' }}">
                                    <a href="#" id="featured">{{ __('Featured') }}</a>
                                </li>
                                <li class="{{ request('listing_type_preferences') === 'top_listing' ? 'active' : '' }}">
                                    <a href="#" id="top_listing">{{ __('Top Listing') }}</a>
                                </li>
                            </ul>
                        </div>

                        {{-- Condition --}}
                        <div class="catagoriesWraper">
                            <p class="cateTitle fw-semibold mb-2">{{ __('Condition') }}</p>
                            <ul class="list-unstyled postdate mb-0">
                                <li class="{{ request('listing_condition') === 'new' ? 'active' : '' }}">
                                    <a href="#" id="new">{{ __('New') }}</a>
                                </li>
                                <li class="{{ request('listing_condition') === 'used' ? 'active' : '' }}">
                                    <a href="#" id="used">{{ __('Used') }}</a>
                                </li>
                            </ul>
                        </div>

                        {{-- Date Posted --}}
                        <div class="catagoriesWraper">
                            <p class="cateTitle fw-semibold mb-2">{{ __('Date Posted') }}</p>
                            <ul class="list-unstyled postdate mb-0">
                                <li class="{{ request('date_posted_listing') === 'today' ? 'active' : '' }}">
                                    <a href="#" id="today">{{ __('Today') }}</a>
                                </li>
                                <li class="{{ request('date_posted_listing') === 'yesterday' ? 'active' : '' }}">
                                    <a href="#" id="yesterday">{{ __('Yesterday') }}</a>
                                </li>
                                <li class="{{ request('date_posted_listing') === 'last_week' ? 'active' : '' }}">
                                    <a href="#" id="last_week">{{ __('Last Week') }}</a>
                                </li>
                            </ul>
                        </div>

                        {{-- Sort By + Reset --}}
                        <div class="catagoriesWraper">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="cateTitle fw-semibold mb-0">{{ __('Sort By') }}</p>
                                <a href="{{ route('frontend.all.listings') }}" class="text-danger small">{{ __('Reset Filter') }}</a>
                            </div>
                            <select id="search_by_sorting" name="sort_by" class="search-input w-100 form-select">
                                <option value="">{{ __('Default') }}</option>
                                <option value="newest"     {{ request('sort_by') === 'newest'     ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                                <option value="oldest"     {{ request('sort_by') === 'oldest'     ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                                <option value="price_asc"  {{ request('sort_by') === 'price_asc'  ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                <option value="price_desc" {{ request('sort_by') === 'price_desc' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                            </select>
                        </div>

                    </div>{{-- cateSidebar1 --}}
                </div>{{-- sidebar col --}}

                {{-- ==================== LISTINGS CONTENT ==================== --}}
                <div class="col-lg-9 cateRightContent">

                    {{-- Top bar: search input + view toggle --}}
                    <div class="viewItems">
                        <div class="SearchWrapper d-flex justify-content-between align-items-center mb-4">
                            <div class="flex-grow-1 me-3">
                                <input type="text" id="search_by_query" name="search"
                                    class="form-control search-input"
                                    placeholder="{{ __('Search listings...') }}"
                                    value="{{ request('search') }}">
                            </div>
                            <div class="views d-flex gap-2">
                                <button type="button" id="card_grid"
                                    class="btn btn-outline-secondary {{ request('listing_grid_and_list_view', 'grid') === 'grid' ? 'active' : '' }}"
                                    title="{{ __('Grid View') }}">
                                    <i class="las la-th-large"></i>
                                </button>
                                <button type="button" id="card_list"
                                    class="btn btn-outline-secondary {{ request('listing_grid_and_list_view') === 'list' ? 'active' : '' }}"
                                    title="{{ __('List View') }}">
                                    <i class="las la-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Result count --}}
                    <p class="text-muted small mb-3">
                        {{ sprintf(__('%d listings found'), $all_listings->total()) }}
                    </p>

                    {{-- Loader shown during AJAX --}}
                    <div id="loader" class="text-center py-5" style="display:none;">
                        <div class="spinner-border text-secondary" role="status"></div>
                    </div>

                    {{-- Listings grid / list --}}
                    @php $isListView = request('listing_grid_and_list_view') === 'list'; @endphp
                    <div class="customTab-content active featureListing {{ $isListView ? 'listingView' : 'gridView' }} customTab-content-1">
                        @if($all_listings->count() > 0)
                            @if($isListView)
                                <div class="singleFeatureCardWraper d-flex flex-wrap">
                                    <x-listings.listing-single :listings="$all_listings"/>
                                </div>
                            @else
                                <div class="slider-inner-margin">
                                    <x-listings.listing-single :listings="$all_listings"/>
                                </div>
                            @endif
                            <div class="mt-4 d-flex justify-content-center custom-pagination">
                                {{ $all_listings->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <h5 class="text-muted">{{ __('No listings found') }}</h5>
                                <a href="{{ route('frontend.all.listings') }}" class="new-cmn-btn rounded-red-btn mt-3 d-inline-block">
                                    {{ __('Clear Filters') }}
                                </a>
                            </div>
                        @endif
                    </div>

                </div>{{-- content col --}}
            </div>{{-- row --}}
        </form>

    </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('assets/frontend/js/nouislider.js') }}"></script>
<script>
(function($){
    "use strict";

    // ── Distance slider ───────────────────────────────────────────────────
    var distanceSliderEl = document.getElementById('distance-slider');
    if (distanceSliderEl) {
        noUiSlider.create(distanceSliderEl, {
            start: [{{ (int) request('distance_kilometers_value', 150) }}],
            range: { min: 0, max: 500 },
            step: 5,
            tooltips: false,
        });
        distanceSliderEl.noUiSlider.on('update', function(values) {
            var val = Math.round(parseFloat(values[0]));
            document.getElementById('slider-value').textContent = val;
            document.getElementById('distance_kilometers_value').value = val;
        });
    }

    // ── Price range slider ────────────────────────────────────────────────
    var priceSliderEl = document.getElementById('price-range-slider');
    if (priceSliderEl) {
        var priceMin = {{ (int) $price_range_min }};
        var priceMax = {{ (int) $price_range_max }};
        var absMax   = {{ (int) $max_price > 0 ? (int) $max_price : 10000 }};
        noUiSlider.create(priceSliderEl, {
            start: [priceMin, priceMax],
            connect: true,
            range: { min: 0, max: absMax },
        });
        priceSliderEl.noUiSlider.on('update', function(values) {
            document.querySelector('.input-min').value = Math.round(parseFloat(values[0]));
            document.querySelector('.input-max').value = Math.round(parseFloat(values[1]));
        });
        document.querySelector('.input-min').addEventListener('change', function() {
            priceSliderEl.noUiSlider.set([this.value, null]);
        });
        document.querySelector('.input-max').addEventListener('change', function() {
            priceSliderEl.noUiSlider.set([null, this.value]);
        });
    }

    // ── Dynamic subcategory load ──────────────────────────────────────────
    $('#search_by_category').on('change', function() {
        var catId = $(this).val();
        $('#search_by_subcategory').addClass('d-none').html('<option value="">{{ __("All Subcategories") }}</option>');
        $('#search_by_child_category').addClass('d-none').html('<option value="">{{ __("All Sub-subcategories") }}</option>');
        if (catId) {
            $.ajax({
                type: 'POST',
                url: '{{ route("get.subcategory") }}',
                data: { _token: '{{ csrf_token() }}', category_id: catId },
                success: function(data) {
                    if (data.status === 'success' && data.sub_categories.length > 0) {
                        var opts = '<option value="">{{ __("All Subcategories") }}</option>';
                        $.each(data.sub_categories, function(i, item) {
                            opts += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        $('#search_by_subcategory').html(opts).removeClass('d-none');
                    }
                }
            });
        }
        $('#search_listings_form').trigger('submit');
    });

    // ── Dynamic child category load ───────────────────────────────────────
    $('#search_by_subcategory').on('change', function() {
        var subcatId = $(this).val();
        $('#search_by_child_category').addClass('d-none').html('<option value="">{{ __("All Sub-subcategories") }}</option>');
        if (subcatId) {
            $.ajax({
                type: 'POST',
                url: '{{ route("get.subcategory.with.child.category") }}',
                data: { _token: '{{ csrf_token() }}', sub_cat_id: subcatId },
                success: function(data) {
                    if (data.status === 'success' && data.child_category.length > 0) {
                        var opts = '<option value="">{{ __("All Sub-subcategories") }}</option>';
                        $.each(data.child_category, function(i, item) {
                            opts += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        $('#search_by_child_category').html(opts).removeClass('d-none');
                    }
                }
            });
        }
        $('#search_listings_form').trigger('submit');
    });

})(jQuery);
</script>
@include('frontend.pages.listings.listings-search-js')
@endsection
