@extends('frontend.layout.master')
@section('site_title')
    {{ __('My Listings') }}
@endsection
@section('style')
    <x-media.css/>
    <x-summernote.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <style>
        .btn-wrapper a{
            margin-right: 8px;
            margin-bottom: 8px;
        }
        .recentImg img {
            height: 102px;
        }

    </style>
@endsection
@section('content')
    <div class="profile-setting my-listing section-padding2">
        <div class="container-1920 plr1">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>
                                <div class="relevant-ads all-listings box-shadow1">
                                    <div class="add-wraper">
                                        @if($listings->count() > 0)
                                        @foreach($listings as $listing)
                                        <div class="single-add-card">
                                            <div class="single-add-image">
                                                {!! render_image_markup_by_attachment_id($listing->image,'','thumb') !!}
                                            </div>
                                            <div class="single-add-body-wraper">
                                                <div class="single-add-body">
                                                    <h5 class="add-heading head4 oneLine">
                                                        <a href="{{route('frontend.listing.details', $listing->slug ?? 'x')}}" target="_blank"> {{ $listing->title }}</a>
                                                    </h5>
                                                    <div class="pricing head4">{{ amount_with_currency_symbol($listing->price)}}</div>
                                                    <div class="btn-wrapper">
                                                        @if($listing->is_featured === 1)
                                                            <span class="pro-btn2">
                                                                <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M4 0V3.88889H7L3 10V6.11111H0L4 0Z" fill="white"/>
                                                                </svg>
                                                                {{ __('FEATURED') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="location-date d-flex">
                                                        @if(!empty($listing->address))
                                                            <div class="locations">
                                                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5.99984 7.83332C5.99984 8.36376 6.21055 8.87246 6.58562 9.24754C6.9607 9.62261 7.4694 9.83332 7.99984 9.83332C8.53027 9.83332 9.03898 9.62261 9.41405 9.24754C9.78912 8.87246 9.99984 8.36376 9.99984 7.83332C9.99984 7.30289 9.78912 6.79418 9.41405 6.41911C9.03898 6.04404 8.53027 5.83332 7.99984 5.83332C7.4694 5.83332 6.9607 6.04404 6.58562 6.41911C6.21055 6.79418 5.99984 7.30289 5.99984 7.83332Z" stroke="#64748B" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M11.7712 11.6047L8.94251 14.4333C8.6925 14.6831 8.35356 14.8234 8.00017 14.8234C7.64678 14.8234 7.30785 14.6831 7.05784 14.4333L4.22851 11.6047C3.48265 10.8588 2.97473 9.90845 2.76896 8.8739C2.5632 7.83934 2.66883 6.767 3.07251 5.79247C3.47618 4.81795 4.15977 3.98501 5.03683 3.39899C5.91388 2.81297 6.94502 2.50018 7.99984 2.50018C9.05466 2.50018 10.0858 2.81297 10.9629 3.39899C11.8399 3.98501 12.5235 4.81795 12.9272 5.79247C13.3308 6.767 13.4365 7.83934 13.2307 8.8739C13.0249 9.90845 12.517 10.8588 11.7712 11.6047Z" stroke="#64748B" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                                <span>{{ $listing->address }}</span>
                                                            </div>
                                                        @endif
                                                        <div class="dates">
                                                            <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9 7.83333L7 6.5V3.16667M1 6.5C1 7.28793 1.15519 8.06815 1.45672 8.7961C1.75825 9.52405 2.20021 10.1855 2.75736 10.7426C3.31451 11.2998 3.97595 11.7417 4.7039 12.0433C5.43185 12.3448 6.21207 12.5 7 12.5C7.78793 12.5 8.56815 12.3448 9.2961 12.0433C10.0241 11.7417 10.6855 11.2998 11.2426 10.7426C11.7998 10.1855 12.2417 9.52405 12.5433 8.7961C12.8448 8.06815 13 7.28793 13 6.5C13 5.71207 12.8448 4.93185 12.5433 4.2039C12.2417 3.47595 11.7998 2.81451 11.2426 2.25736C10.6855 1.70021 10.0241 1.25825 9.2961 0.956723C8.56815 0.655195 7.78793 0.5 7 0.5C6.21207 0.5 5.43185 0.655195 4.7039 0.956723C3.97595 1.25825 3.31451 1.70021 2.75736 2.25736C2.20021 2.81451 1.75825 3.47595 1.45672 4.2039C1.15519 4.93185 1 5.71207 1 6.5Z" stroke="#64748B" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                            <span>{{ $listing->created_at?->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="btn-wraper">
                                                        @if($listing->status === 1)
                                                           <a href="javascript:void(0)" class="approved-btn">{{ __('APPROVED') }}</a>
                                                        @else
                                                            <a href="javascript:void(0)" class="pending-btn">{{ __('PENDING') }}</a>
                                                        @endif
                                                        <a href="javascript:void(0)" class="listing-view-btn"><i class="fa-regular fa-eye"></i>{{ $listing->view }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="right-buttons">
                                                <span class="text">
                                                    @if($listing->is_published === 1)
                                                        {{ __('Published') }}
                                                    @else
                                                        {{ __('Unpublished') }}
                                                    @endif
                                                </span>
                                                    <div class="publish-btn">
                                                      <x-status.frontend-listing-published-change :title="__('Change Status')" :url="route('user.listing.published.status', $listing->id)" :published="$listing->is_published"/>
                                                    </div>
                                                <div class="setting-btn-wraper">
                                                    <a href="javascript:void(0)" class="setting-btn">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="settings-wraper">
                                                        <a href="{{ route('user.edit.listing', $listing->id) }}">
                                                            <i class="las la-pen-alt"></i>
                                                            {{ __('Edit Ad') }}
                                                        </a>
                                                        <a href="{{route('frontend.listing.details',$listing->slug ?? 'x')}}" target="_blank">
                                                            <i class="las la-eye"></i>
                                                            {{ __('View Ad') }}
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                            class="boost-listing-btn"
                                                            data-id="{{ $listing->id }}"
                                                            data-title="{{ addslashes($listing->title) }}"
                                                            data-price="{{ $boostPrice }}"
                                                            data-free="{{ $freeBoostsLeft }}"
                                                            data-url="{{ route('user.listing.boost', $listing->id) }}">
                                                            <i class="las la-rocket"></i>
                                                            {{ __('Boost Ad') }}
                                                        </a>
                                                        {{-- hidden boost form --}}
                                                        <form id="boost-form-{{ $listing->id }}" method="POST" action="{{ route('user.listing.boost', $listing->id) }}" style="display:none;">
                                                            @csrf
                                                        </form>
                                                        <x-popup.frontend-listing-delete-popup :url="route('user.delete.listing',$listing->id)"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       <div class="devider"></div>
                                        @endforeach
                                            <div class="blog-pagination">
                                                <div class="custom-pagination mt-4 mt-lg-5">
                                                    {!! $listings->links() !!}
                                                </div>
                                            </div>
                                        @else
                                            <x-pagination.empty-data-placeholder :title="__('No Listing Created Yet')"/>
                                        @endif
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
    <x-media.markup :type="'web'"/>
@endsection
@section('scripts')
    <x-media.js :type="'web'"/>
    <script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
    <script src="{{asset('assets/frontend/js/multi-step.js')}}"></script>
    <x-summernote.js/>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {

                $(document).on('click','.swal_delete_button',function(e){
                    e.preventDefault();
                    Swal.fire({
                        title: '{{__("Are you sure?")}}',
                        text: '{{__("You would not be able to revert this item!")}}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{__('Yes, delete it!')}}",
                        cancelButtonText: "{{__('Cancel')}}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).next().find('.swal_form_submit_btn').trigger('click');
                        }
                    });
                });

                // change status
                $(document).on('click','.swal_status_change',function(e){
                    e.preventDefault();
                    Swal.fire({
                        title: '{{__("Are you sure?")}}',
                        text: '{{__("You would not be able to revert this item!")}}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{{ __('Yes, change it!') }}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).next().find('.swal_form_submit_btn').trigger('click');
                        }
                    });
                });

                // Boost listing
                $(document).on('click', '.boost-listing-btn', function(e) {
                    e.preventDefault();
                    const id    = $(this).data('id');
                    const title = $(this).data('title');
                    const price = parseFloat($(this).data('price'));
                    const free  = parseInt($(this).data('free'));
                    const costText = free > 0
                        ? '{{ __("Free (membership entitlement)") }} — ' + free + ' {{ __("remaining this month") }}'
                        : '{{ __("Cost") }}: {{ amount_with_currency_symbol($boostPrice) }}';

                    Swal.fire({
                        title: '{{ __("Boost Listing") }}',
                        html: '<p>{{ __("Boost") }} <strong>' + title + '</strong>?</p>' +
                              '<p style="font-size:13px;color:#64748b;">' + costText + '</p>' +
                              '<p style="font-size:13px;color:#64748b;">{{ __("Your listing will appear at the top of results for 48 hours.") }}</p>',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#524EB7',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '{{ __("Yes, Boost it!") }}',
                        cancelButtonText: '{{ __("Cancel") }}',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#boost-form-' + id).submit();
                        }
                    });
                });

                //Permalink Code
                $('.permalink_label').hide();
                $(document).on('keyup', '#title', function (e) {
                    let slug = converToSlug($(this).val());
                    let url = "{{url('/listing/')}}/" + slug;
                    $('.permalink_label').show();
                    let data = $('#slug_show').text(url).css('color', '#3c3cf7');
                    $('.listing_slug').val(slug);

                });

                function converToSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    //remove multiple space to single
                    finalSlug = slug.replace(/  +/g, ' ');
                    // remove all white spaces single or multiple spaces
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }

                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.listing_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.listing_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/listing/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.listing_slug').hide();
                });

                $('#category').on('change',function(){
                    let category_id = $(this).val();
                    $.ajax({
                        method:'post',
                        url:"{{route('get.subcategory')}}",
                        data:{category_id:category_id},
                        success:function(res){
                            if(res.status=='success'){
                                let alloptions = "<option value=''>{{__('Select Sub Category')}}</option>";
                                let allSubCategory = res.sub_categories;
                                $.each(allSubCategory,function(index,value){
                                    alloptions +="<option value='" + value.id + "'>" + value.name + "</option>";
                                });
                                $(".subcategory").html(alloptions);
                                $('#subcategory').niceSelect('update');
                            }
                        }
                    })
                });

                // listing sub category and child category
                $(document).on('click','#subcategory', function() {
                    var sub_cat_id = $(this).val();
                    $.ajax({
                        method: 'post',
                        url: "{{ route('get.subcategory.with.child.category') }}",
                        data: {
                            sub_cat_id: sub_cat_id
                        },
                        success: function(res) {

                            if (res.status == 'success') {
                                var alloptions = "<option value=''>{{__('Select Child Category')}}</option>";
                                var allList = "<li data-value='' class='option'>{{__('Select Child Category')}}</li>";
                                var allChildCategory = res.child_category;

                                $.each(allChildCategory, function(index, value) {
                                    alloptions += "<option value='" + value.id +
                                        "'>" + value.name + "</option>";
                                    allList += "<li class='option' data-value='" + value.id +
                                        "'>" + value.name + "</li>";
                                });

                                $("#child_category").html(alloptions);
                                $(".child_category_wrapper ul.list").html(allList);
                                $(".child_category_wrapper").find(".current").html("Select Child Category");
                            }
                        }
                    });
                });

                // change country and get state
                $(document).on('change','#country_id', function() {
                    let country = $(this).val();
                    $.ajax({
                        method: 'post',
                        url: "{{ route('au.state.all') }}",
                        data: {
                            country: country
                        },
                        success: function(res) {
                            if (res.status == 'success') {
                                let all_options = "<option value=''>{{__('Select State')}}</option>";
                                let all_state = res.states;
                                $.each(all_state, function(index, value) {
                                    all_options += "<option value='" + value.id +
                                        "'>" + value.state + "</option>";
                                });
                                $(".get_country_state").html(all_options);
                                $(".state_info").html('');
                                if(all_state.length <= 0){
                                    $(".state_info").html('<span class="text-danger"> {{ __('No state found for selected country!') }} <span>');
                                }
                            }
                        }
                    })
                })

                // change state and get city
                $('#state_id').on('change', function() {
                    let state = $(this).val();
                    $.ajax({
                        method: 'post',
                        url: "{{ route('au.city.all') }}",
                        data: {
                            state: state
                        },
                        success: function(res) {
                            if (res.status == 'success') {
                                let all_options = "<option value=''>{{__('Select City')}}</option>";
                                let all_city = res.cities;
                                $.each(all_city, function(index, value) {
                                    all_options += "<option value='" + value.id +
                                        "'>" + value.city + "</option>";
                                });
                                $(".get_state_city").html(all_options);

                                $(".city_info").html('');
                                if(all_city.length <= 0){
                                    $(".city_info").html('<span class="text-danger"> {{ __('No city found for selected state!') }} <span>');
                                }
                            }
                        }
                    })
                })

            });
        })(jQuery)
    </script>

    @if(session('success'))
        <script>
            toastr.success('{{ session('success') }}', 'Success');
        </script>
    @endif
@endsection
