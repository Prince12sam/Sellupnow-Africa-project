@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.css">
    <style>
        /*loader css start */
        .all_location_new_btn.btn-primary {
            background-color: var(--main-color-one);
            border-color: var(--main-color-one);
        }
        .loader-container {
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #map-container {
            display: none; /* Initially hide the map container */
        }
        /*loader css end */

        /* new ======================*/
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
        .slider-kilometer .slider-range {
            height: 8px;
            background: #ddd;
        }
        .noUi-handle:after,
        .noUi-handle:before {
            display: none;
        }
        .noUi-touch-area {
            height: 100%;
            width: 100%;
            background: var(--main-color-one);
            border-radius: 50%;
        }
        .noUi-pips-horizontal {
            padding: 10px 0;
            height: 80px;
            top: 100%;
            left: 0;
            width: 100%;
            visibility: hidden;
            opacity: 0;
        }
        .noUi-connect {
            background: gray;
        }
        .noUi-horizontal .noUi-handle {
            width: 20px;
            height: 20px;
            right: -10px;
            top: -6px;
            border-radius: 50%;
        }
        .range-input{
            position: relative;
        }
        .range-input input{
            position: absolute;
            width: 100%;
            height: 5px;
            top: -5px;
            background: none;
            pointer-events: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        input[type="range"]::-webkit-slider-thumb{
            height: 17px;
            width: 17px;
            border-radius: 50%;
            background: #17A2B8;
            pointer-events: auto;
            -webkit-appearance: none;
            box-shadow: 0 0 6px rgba(0,0,0,0.05);
        }
        input[type="range"]::-moz-range-thumb{
            height: 17px;
            width: 17px;
            border: none;
            border-radius: 50%;
            background: #17A2B8;
            pointer-events: auto;
            -moz-appearance: none;
            box-shadow: 0 0 6px rgba(0,0,0,0.05);
        }

        .singleFeatureCard.inside_google_map_card {
            max-width: 200px !important;
            height: 181px !important;
        }

        .singleFeatureCard.inside_google_map_card .main-card-image {
            height: 111px!important;
        }

        .new-style .singleFeatureCard.inside_google_map_card .featurebody {
            height: auto!important;
        }

    </style>
@endsection
<div class="all-listing" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}">
    <div class="container-1920 plr1">
        <!--Sidebar Icon-->
        <div class="sidebar-btn d-none">
            <a href="javascript:void(0)"><i class="las la-bars"></i></a>
        </div>

        <form method="get" action="{{$current_page_url}}" id="search_listings_form">
            <input type="hidden" name="listing_grid_and_list_view" id="listing_grid_and_list_view">
            <input type="hidden" name="date_posted_listing" id="date_posted_listing">
            <input type="hidden" name="listing_condition" id="listing_condition">
            <input type="hidden" name="listing_type_preferences" id="listing_type_preferences">

            <div class="catabody-wraper">
                <!-- Left Content -->
                <div class="cateLeftContent">
                    <div class="cateSidebar1">

                        <!--Search any title filter start -->
                        @if(!empty($listing_search_by_text_on_off))
                            <div class="catagoriesWraper mb-4">
                                <div class="catagories w-100">
                                    <div class="single-category-service">
                                        <div class="single-select">
                                            <input type="text" class="search-input form-control" id="search_by_query"
                                                   placeholder="{{$search_placeholder}}" name="q" value="{{$text_search_value}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!--Search any title filter end -->

                        <!--Distance google map filter -->
                        @if(!empty($location_on_off))
                            @if (!empty(get_static_option("google_map_settings_on_off")))
                                <div class="catagoriesWraper mb-4">
                                    <div class="catagories w-100">
                                            <!-- autocomplete address -->
                                            <div class="suburb_section_start">
                                                <input type="hidden" name="autocomplete_address" id="autocomplete_address">
                                                <input type="hidden" name="location_city_name" id="location_city_name">
                                                <input type="hidden" name="latitude" id="latitude">
                                                <input type="hidden" name="longitude" id="longitude">
                                                <label class="cateTitle mb-2">{{ __('Location') }}</label>
                                                <input class="search-input form-control w-100 border-1 bg-white autocomplete_disable" name="autocomplete" id="autocomplete" placeholder="{{ __('Enter a Location') }}" type="text">
                                            </div>

                                            <!-- Distance range-->
                                            <div id="distance-slider"></div>
                                            <div class="slider-container slider-kilometer">
                                                <input type="hidden" name="distance_kilometers_value" id="distance_kilometers_value">
                                                <div class="cateTitle mb-2">{{__('Distance')}}</div>
                                                <div id="slider" class="slider-range mt-2"></div>
                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                    <div id="slider-value" class="slider-range-value"></div>
                                                    <span class="km_title_text">{{ __('km') }}</span>
                                                </div>
                                            </div>

                                            <!-- cancel and apply button start -->
                                            <div class="cancel_apply_section_start text-end mb-2">
                                                <button type="button" class="filter-btn w-100" id="distance_wise_filter_apply">{{ __('Filter') }}</button>
                                            </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <!--google map Distance filter end -->

                        <!-- All Categories -->
                        <div class="catagoriesWraper mb-4">
                            @if(!empty($category_on_off))
                                <div class="catagories w-100">
                                    <select id="search_by_category" name="cat" class="categorySelect">
                                        <option value="">{{$category_text}}</option>
                                        @foreach($categories as $cat)
                                            <option @if(!empty(request()->get("cat")) && request()->get("cat") == $cat->id) selected @endif value="{{$cat->id}}">{{$cat->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                           @if(!empty($subcategory_on_off))
                                <div class="catagories w-100">
                                    <select id="search_by_subcategory" name="subcat" class="categorySelect">
                                        <option value="">{{$subcategory_text}}</option>
                                        @foreach($sub_categories as $sub_cat)
                                            <option @if(!empty(request()->get("subcat")) && request()->get("subcat") == $sub_cat->id) selected @endif value="{{$sub_cat->id}}">{{$sub_cat->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                             @endif
                           @if(!empty($child_category_on_off))
                                <div class="catagories">
                                    <select id="search_by_child_category" name="child_cat" class="categorySelect">
                                        <option value="">{{$child_category_text}}</option>
                                        @foreach($child_categories as $child_cat)
                                            <option @if(!empty(request()->get("child_cat")) &&  request()->get("child_cat") == $child_cat->id) selected @endif value="{{$child_cat->id}}">{{$child_cat->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                             @endif
                        </div>

                        <!-- Location -->
                        @if(empty(get_static_option("google_map_settings_on_off")))
                            <div class="locaton catagoriesWraper mb-4">
                                    @if(!empty($country_on_off))
                                    <div class="catagories">
                                        <select id="search_by_country" name="country" class="categorySelect">
                                            <option value="">{{$country_text}}</option>
                                            @foreach ($countries as $cont)
                                                <option @if(!empty(request()->get("country")) && request()->get("country") == $cont->id ) selected @endif  value="{{$cont->id}}">{{$cont->country}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                @endif

                                @if(empty(get_static_option("google_map_settings_on_off")))
                                    @if(!empty($state_on_off))
                                            @php  $fetch_cities = '';  @endphp
                                            @if ($country_on_off !== "on")
                                                @php
                                                    $get_listing_state_id = $all_listings->pluck('city_id');
                                                     $all_sates = \Modules\CountryManage\app\Models\City::whereIn("id", $get_listing_state_id)->where("status", 1)->get();
                                                     foreach ($all_sates as $states) {
                                                         $fetch_cities .=  "<option selected value=" .  $states->id .   ">" . $states->city .  "</option>";
                                                     }
                                                @endphp
                                            @endif
                                        <div class="catagories">
                                            <select id="search_by_state" name="state">
                                                <option value=""> {{$state_text}}</option>
                                                @foreach ($listings_state as $listing_state) {
                                                    <option @if(!empty(request()->get("state")) && request()->get("state") == $listing_state->id) selected @endif
                                                    value="{{$listing_state->id}}">{{$listing_state->state}}</option>
                                                @endforeach
                                                {{ $fetch_cities }}
                                            </select>
                                        </div>
                                    @endif
                                @endif

                                @if(empty(get_static_option("google_map_settings_on_off")))
                                    @if(!empty($city_on_off))
                                        <div class="catagories">
                                            <select id="search_by_city" name="city">
                                                <option value=""> {{$city_text}}</option>
                                                @foreach ($listings_city as $listing_city) {
                                                    <option @if(!empty(request()->get("city")) && request()->get("city") == $listing_city->id) selected @endif
                                                    value="{{$listing_city->id}}">{{$listing_city->city}}</option>
                                                @endforeach
                                                {{ $fetch_cities ?? 0 }}
                                            </select>
                                        </div>
                               @endif
                            </div>
                        @endif

                        <!--price range filter -->
                        @if(!empty($price_range_on_off))
                            <div class="catagoriesWraper mb-4">
                                <div class="catagories priceRange">
                                    <h5 class="cateTitle mb-2 postdateTitle">{{ __('Price Range') }}</h5>
                                    <input type="hidden" name="price_range_value" id="price_range_value">
                                    <div class="price-input">
                                        <div class="field">
                                            <div class="min_price_range priceRangeWraper">
                                                <span class="site_currency_symbol">{{ site_currency_symbol() }}</span>
                                                <input type="number" class="input-min">
                                            </div>
                                        </div>
                                        <div class="separator">-</div>
                                        <div class="field">
                                            <div class="max_price_range priceRangeWraper">
                                                <span class="site_currency_symbol">{{ site_currency_symbol() }}</span>
                                                <input type="number" class="input-max">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="price_range_setup">
                                        <div class="progress"></div>
                                    </div>
                                    <!-- cancel and apply button start -->
                                    <div class="cancel_apply_section_start mt-3">
                                        <button type="button" class="filter-btn w-100" id="price_wise_filter_apply">{{ __('Filter') }}</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!--price range filter end -->

                        @if(!empty($listing_type_preferences))
                            <div class="catagoriesWraper mb-4">
                                <div class="catagories">
                                    <h5 class="cateTitle mb-2 postdateTitle">{{ $listing_type_preferences_title }}</h5>
                                    <ul class="postdate">
                                        <li @if($listing_type_preferences_value == 'featured') class="active" @endif><a href="javascript:void(0)" id="featured">{{ __('Featured') }}</a></li>
                                        <li @if($listing_type_preferences_value == 'top_listing') class="active" @endif><a href="javascript:void(0)" id="top_listing">{{ __('Top Listing') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if(!empty($listing_condition))
                            <div class="catagoriesWraper mb-4">
                                <div class="catagories">
                                    <h5 class="cateTitle mb-2 postdateTitle">{{ $listing_condition_title }}</h5>
                                    <ul class="postdate">
                                        <li @if($listing_condition_value == 'new') class="active" @endif><a href="javascript:void(0)" id="new">{{ __('New') }}</a></li>
                                        <li @if($listing_condition_value == 'used') class="active" @endif><a href="javascript:void(0)" id="used">{{ __('Used') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if(!empty($date_posted))
                            <div class="catagoriesWraper mb-4">
                                <div class="catagories">
                                    <h5 class="cateTitle mb-2 postdateTitle">{{ $date_posted_title }}</h5>
                                    <ul class="postdate">
                                        <li @if($date_posted_value == 'today') class="active" @endif><a href="javascript:void(0)" id="today">{{ __('Today') }}</a></li>
                                        <li @if($date_posted_value == 'yesterday') class="active" @endif><a href="javascript:void(0)" id="yesterday">{{ __('Yesterday') }}</a></li>
                                        <li @if($date_posted_value == 'last_week') class="active" @endif><a href="javascript:void(0)" id="last_week">{{ __('Last Week') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- Sort-by filter start -->
                        @if(!empty($sort_by_on_off))
                            <div class="catagoriesWraper px-0">
                                <div class="catagories mx-3">
                                    <select id="search_by_sorting" name="sortby">
                                        <option value="">{{ __("Sort By") }}</option>
                                        @foreach($sortby_search as $value => $text)
                                            <option @if(!empty(request()->get("sortby")) && request()->get("sortby") == $value) selected @endif value="{{$value}}">{{$text}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Right Content -->
                <div class="cateRightContent @if(!empty(get_static_option('google_map_settings_on_off'))) active-map @endif ">

                    <!-- loader -->
                    <div id="loader" class="loader"> </div>

                    <div class="cateRightContentWraper">
                        <div class="content-part">
                            <div class="viewItems">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="SearchWrapper d-flex justify-content-between align-items-start">
                                            <!-- Custom Tab -->
                                            <div class="views align-items-center">
                                                <div class="sidebar-btn d-lg-none d-block">
                                                    <a href="javascript:void(0)"><i class="fa-solid fa-bars"></i></a>
                                                </div>
                                                <div class="reset-btn cmn-filter-btn">
                                                    <a href="{{ url()->current() }}">
                                                        <button type="button">
                                                            <i class="las la-undo-alt"></i> {{ __('Reset Filter') }}
                                                        </button>
                                                    </a>
                                                </div>
                                                <div class="listing-btn">
                                                    <button class="customTab @if($listing_grid_and_list_view == 'grid') active @endif" data-toggle-target=".customTab-content-1" id="card_grid">
                                                        <i class="las la-th-large fs-4"></i>
                                                    </button>
                                                    <button class="customTab @if($listing_grid_and_list_view == 'list') active @endif" data-toggle-target=".customTab-content-2" id="card_list">
                                                        <i class="las la-th-list fs-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Grid View -->
                            @if($listing_grid_and_list_view == 'grid')
                                <div class="gridView customTab-content customTab-content-1 @if($listing_grid_and_list_view == 'grid') active @endif">
                                    <div class="gridViews">
                                        <div class="singleFeatureCardWraper d-flex">
                                            <x-listings.listing-single :listings="$all_listings"/>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- List View -->
                            @if($listing_grid_and_list_view == 'list')
                               <div class="listingView customTab-content customTab-content-2 @if($listing_grid_and_list_view == 'list') active @endif">
                                <div class="singleFeatureCardWraper d-flex">
                                    <x-listings.listing-single-list-view :listings="$all_listings"/>
                                </div>
                              </div>
                             @endif

                        </div>

                        <!--Google Map-->
                        @if($listing_grid_and_list_view == 'grid' || $listing_grid_and_list_view == 'list')
                                @if (!empty(get_static_option("google_map_settings_on_off")))
                                @if($all_listings->count() > 0)
                                    <div class="googleWraper mt-70">
                                        <!-- loader -->
                                        <div class="loader-container">
                                            <div class="loader"></div>
                                        </div>
                                        <!--google map section start -->
                                        <div class="service-locationMap" id="map-container">
                                            <div class="fullwidth-sidebar-container">
                                                <div class="sidebar top-sidebar">
                                                    <div id="map-canvas" class="map-canvas"  style="height: 700px; width: 450px; position: relative; overflow: hidden;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                @endif
                            @endif
                        @endif
                    </div>

                    <!-- Pagination -->
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="pagination mt-60">
                                @if($all_listings->count() > 0)
                                    <div class="blog-pagination">
                                        <div class="custom-pagination mt-4 mt-lg-5">
                                            {!! $all_listings->links() !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.js"></script>
    @if(!empty(get_static_option("google_map_settings_on_off")))
        <script src="https://maps.googleapis.com/maps/api/js?key={{$google_api_key}}&libraries=places">
        <script defer src="//cdn.jsdelivr.net/npm/markerclustererplus/dist/markerclusterer.min.js"> </script>
        <script>
            // Wait for the page to fully load
            window.addEventListener('load', function() {
                var loaderContainer = document.querySelector('.loader-container');
                var mapContainer = document.getElementById('map-container');
                loaderContainer.style.display = 'none';
                mapContainer.style.display = 'block';
            });

            // Google map html markup show section
            function generateContent(place){
                  var content = `<div class="singleFeatureCard inside_google_map_card w-100">
                                    <div class="featureImg">
                                           <a href=\"{{$listing_details_route}}/`+place.slug+`\" class="main-card-image">
                                             `+place.image_url+`
                                           </a>
                                    </div>
                                    <div class="featurebody">
                                         <div class="btn-wrapper">
                                                 `+place.is_featured_item+`
                                         </div>
                                          <h4>
                                              <a href=\"{{$listing_details_route}}/`+place.slug+`\" title=\"View: `+place.title+`\" class="featureTittle head4 twoLine">
                                                   `+place.title+`
                                              </a>
                                           </h4>
                                           <span class="featurePricing d-flex justify-content-between align-items-center">
                                           <span class="money">`+place.listing_main_price+`</span>
                                           <span class="date">`+place.listing_published_at+`</span>
                                          </span>
                                    </div>
                                </div>`;
                return content;
            }

            var map;
            var markers = [];
            var infowindow = new google.maps.InfoWindow();
            var places = @json($all_listings_list_json);

            // first check lat, long if lat long not empty map initialize play
            var latitude;
            var longitude;

            @if(!empty($latitude) && !empty($longitude))
                latitude = '{{$latitude}}'
                longitude = '{{$latitude}}'
                // local storage
                if(latitude !== null && longitude !== null) {
                    localStorage.setItem('latitude', latitude);
                    localStorage.setItem('longitude', longitude);
                }
            @else
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    latitude = position.coords.latitude;
                    longitude = position.coords.longitude;
                    // local storage
                    localStorage.setItem('latitude', latitude);
                    localStorage.setItem('longitude', longitude);
                }, function (error) {
                    console.error('Error getting location:', error);
                    // Set default values in case of an error
                    latitude = 0;
                    longitude = 0;
                });
            }
                latitude = localStorage.getItem('latitude');
                longitude = localStorage.getItem('longitude');
                // $('#latitude').val(latitude)
                // $('#longitude').val(longitude)

            @endif
            var centerLatLng = new google.maps.LatLng(latitude, longitude);
            function initialize() {
                var mapOptions = {
                    zoom: 6,
                    minZoom: 2,
                    maxZoom: 14,
                    zoomControl: true,
                    zoomControlOptions: {
                        style: google.maps.ZoomControlStyle.DEFAULT
                    },
                    center: centerLatLng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    scrollwheel: true,
                    panControl: true,
                    mapTypeControl: true,
                    scaleControl: true,
                    overviewMapControl: true,
                    rotateControl: true,
                };
                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
                // all markers show this map
                addMarkers();
                initializeRangeSlider();
            }

            google.maps.event.addDomListener(window, 'load', initialize);
            function addMarkers() {
                var min = 0.999999;
                var max = 1.000001;

                for (var place in places) {
                    place = places[place];
                    if (place.lat !== null) {
                        var google_maker_icon = "{{ isset($google_map_maker_icon) ? $google_map_maker_icon : 'https://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_red.png' }}";
                        var image = new google.maps.MarkerImage(google_maker_icon, null, null, null, new google.maps.Size(40, 52));

                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(
                                place.lat * (Math.random() * (max - min) + min),
                                place.lon * (Math.random() * (max - min) + min)
                            ),
                            map: map,
                            title: place.title,
                            icon: image,
                        });

                        markers.push(marker);
                        google.maps.event.addListener(marker, 'click', (function (marker, place) {
                            return function () {
                                toggleMarkerAnimation(marker);
                                infowindow.setContent(generateContent(place));
                                infowindow.open(map, marker);
                                smoothZoomIn(map, marker, 12, 1000);
                            };

                        })(marker, place));
                    }
                }
            }

            // Smooth zoom-in animation centered on a marker
            function smoothZoomIn(map, marker, zoomLevel, duration) {
                var currentZoom = map.getZoom();
                var targetZoom = Math.min(zoomLevel, map.maxZoom);
                var step = 1;
                var delay = Math.round(duration / (targetZoom - currentZoom));

                // Recursively increase the zoom level
                function zoom() {
                    if (currentZoom < targetZoom) {
                        currentZoom += step;
                        map.setZoom(currentZoom);
                        setTimeout(zoom, delay);
                    } else {
                        map.panTo(marker.getPosition());
                    }
                }
                zoom();
            }

            // Function to toggle marker animation
            function toggleMarkerAnimation(marker) {
                if (marker.getAnimation() !== null) {
                    marker.setAnimation(null);
                } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(function() {
                        marker.setAnimation(null);
                    }, 3000); // Stop animation after 3 seconds
                }
            }


            @if(!empty($location_on_off))
            function initializeRangeSlider() {
                var slider = document.getElementById('slider');
                var sliderValue = document.getElementById('slider-value');

                noUiSlider.create(slider, {
                    start: {{ !empty($radius) ? $radius : 50 }},
                    range: {
                        'min': 1,
                        'max': 150
                    }
                });

                slider.noUiSlider.on('update', function (values) {
                    var newValue = Math.round(values[0]);
                    sliderValue.innerHTML = newValue;
                });
            }
        @else
              function initializeRangeSlider(){ return '' };
        @endif
          </script>

        <script>
            (function($){
                "use strict";
                $(document).ready(function(){

                    @if($all_listings->count() === 0)
                      initializeRangeSlider();
                    @endif

                    //========google map autocomplete address start
                    // Initialize Google Places autocomplete
                    var input = document.getElementById('autocomplete');
                    var countryCodesStr = "{{$countryCodesStr}}";
                    var allCountryCodes = countryCodesStr.split(',');
                    var autocompleteOptions = {
                        types: ['(regions)'],
                        componentRestrictions: {
                            country: allCountryCodes
                        }
                    };

                    // Initialize the autocomplete with the options
                    var autocomplete = new google.maps.places.Autocomplete(
                        document.getElementById('autocomplete'),
                        autocompleteOptions
                    );

                    // Get current location name and lat/long
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;

                        // Reverse geocode to get location name
                        var geocoder = new google.maps.Geocoder();
                        var latlng = new google.maps.LatLng(lat, lng);

                        geocoder.geocode({ 'location': latlng }, function(results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    // Extract city and division
                                    var addressComponents = results[0].address_components;
                                    var city = '';
                                    var division = '';

                                    for (var i = 0; i < addressComponents.length; i++) {
                                        var component = addressComponents[i];
                                        if (component.types.includes('locality')) {
                                            city = component.long_name;
                                        } else if (component.types.includes('administrative_area_level_1')) {
                                            division = component.long_name;
                                        }
                                    }

                                    // Format as "City, Division"
                                    var formattedLocation = city + ', ' + division;

                                    @if(!empty($location_city_name))
                                        var city_name_formatted_location = `{{$location_city_name}}`;
                                    @else
                                      var city_name_formatted_location = city;
                                    @endif

                                    // set address in input box current location
                                    @if(!empty($autocomplete_address))
                                        input.value = `{{$autocomplete_address}}`;
                                    @else
                                        input.value = formattedLocation;
                                    @endif

                                    if(formattedLocation){
                                        $('#location_city_name').val(city);

                                        $('#latitude').val(lat);
                                        $('#longitude').val(lng);
                                        // Set the filter title by combining the distance and formatted location
                                        var distance_set_default = `{{ $distance_radius_km_get ?? 50 }}`;
                                        $('.distance_wise_filter_title').text(`${distance_set_default}km ${city_name_formatted_location}`);
                                    }


                                } else {
                                    console.error('No results found');
                                }
                            } else {
                                console.error('Geocoder failed due to: ' + status);
                            }
                        });
                    });

                    // Autocomplete address get
                    autocomplete.addListener('place_changed', function() {
                        var place = autocomplete.getPlace();
                        if (!place.geometry) {
                            return;
                        }
                        var suburb = place.name;
                        var lat = place.geometry.location.lat();
                        var lng = place.geometry.location.lng();


                        var city_name = '';
                        for (var i = 0; i < place.address_components.length; i++) {
                            var component = place.address_components[i];
                            if (component.types.includes('locality')) {
                                city_name = component.long_name;
                                break;
                            }
                        }

                        // set lat long value
                        if(suburb){
                            $('#location_city_name').val(city_name);
                            $('#latitude').val(lat);
                            $('#longitude').val(lng);
                        }
                    });
                    //========== google map autocomplete address end

                    // set address in input box current location
                    @if(!empty($autocomplete_address))
                        var google_map_place_address = `{{$autocomplete_address}}`;
                        $('#autocomplete').val(google_map_place_address);
                    @endif


                    // google map distance, current location, autocomplete address wise filter jobs
                    $(document).on('click', '#distance_wise_filter_apply', function() {
                        let get_lan_value = $('#latitude').val();
                        let get_long_value = $('#longitude').val();
                        let distance_km_value = $('#slider-value').text();

                        $('#distance_kilometers_value').val(distance_km_value);
                        // get autocomplete address old value get
                        let get_autocomplete_value = $('#autocomplete').val();

                        $('#autocomplete_address').val(get_autocomplete_value);

                        // get price and set value
                        let left_value = $('.input-min').val();
                        let right_value = $('.input-max').val();
                        $('#price_range_value').val(left_value + ',' + right_value);

                        $('#search_listings_form').trigger('submit');
                    });

                });
            })(jQuery);
        </script>
      @endif

        <script>
            (function($){
                "use strict";
                $(document).ready(function(){
                    $(document).on('click', '#price_wise_filter_apply', function (){
                        let left_value = $('.input-min').val();
                        let right_value = $('.input-max').val();
                        $('#price_range_value').val(left_value + ',' + right_value);

                        // google map km set
                        let distance_km_value = $('#slider-value').text();
                        $('#distance_kilometers_value').val(distance_km_value);
                        let get_autocomplete_value = $('#autocomplete').val();
                        $('#autocomplete_address').val(get_autocomplete_value);

                        $('#search_listings_form').trigger('submit');
                    });
                });
            })(jQuery);
        </script>

    @if(!empty($price_range_on_off))
        <script>
            const rangeInput = document.querySelectorAll(".range-input input"),
                priceInput = document.querySelectorAll(".price-input input"),
                range = document.querySelector(".price_range_setup .progress");
            let priceGap = 10;

            var slider_price_div = document.querySelector('.price_range_setup .progress');
            var maxPriceValue = {{ $max_price_start_value ?? 10000}};
            noUiSlider.create(slider_price_div, {
                start: [@if(!empty($min_price)) {{$min_price}} @else 1 @endif, @if(!empty($max_price)) {{$max_price}} @else 10000 @endif],
                connect: true,
                range: {
                    'min': 1,
                    'max': maxPriceValue
                },
                pips: {
                    mode: 'steps',
                    stepped: true,
                    density: 4
                }
            });

            slider_price_div.noUiSlider.on('update', function (values) {
                $(".input-min").val(Math.round(values[0]));
                $(".input-max").val(Math.round(values[1]));
            });

            // INPUT
            priceInput.forEach(input => {
                input.addEventListener("input", e => {
                    let minPrice = parseInt(priceInput[0].value),
                        maxPrice = parseInt(priceInput[1].value);

                    if ((maxPrice - minPrice) >= priceGap && maxPrice <= slider_price_div.noUiSlider.options.range.max) {
                        if (e.target.className === "input-min") {
                            slider_price_div.noUiSlider.set([minPrice, null]);
                        } else {
                            slider_price_div.noUiSlider.set([null, maxPrice]);
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
