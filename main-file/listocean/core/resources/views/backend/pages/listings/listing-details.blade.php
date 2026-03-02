@extends('backend.admin-master')
@section('site-title')
    {{__('Listing Details')}}
@endsection
@section('style')
    <x-media.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <x-summernote.css/>
    <style>
        span {
            display: inline;
        }
        .dashboard__rates__card__thumb {
            gap: 6px;
            margin: 5px;
            padding: 7px;
            display: flex;
            flex-wrap: wrap;
        }

        .effectBorder {
            pointer-events: none; /* Disable interactions */
            cursor: not-allowed; /* Indicate non-interactivity */
        }
        .customer__account__details__item__flex {
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: flex-start;
        }

        .seller-img {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid #ddd;
            position: relative;
        }

        .seller-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="header-wrap d-flex justify-content-between">
                    <div class="left-content">
                        <h4 class="header-title">{{__('Listing Details')}}   </h4>
                    </div>
                    <div class="right-content">
                        <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{route('admin.user.all.listings')}}">{{__('All Listings')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <div class="product__details__single">
                    <div class="editProduct">
                        <div class="row g-4">
                            <div class="col-xxl-3 col-lg-4">
                                <div class="editProduct__contents__category mb-2">
                                    <strong class="editProduct__contents__sku__para">{{ __('Thumb Image:') }}</strong>
                                </div>
                                <div class="editProduct__thumb">
                                    <div class="editProduct__thumb__main">
                                        {!! render_image_markup_by_attachment_id($listing->image, '', 'thumb') !!}
                                    </div>
                                </div>
                                <div class="editProduct__contents__category mt-3">
                                    <strong class="editProduct__contents__sku__para">{{ __('Gallery Images:') }}</strong>
                                </div>
                                <div class="dashboard__rates__card__thumb">
                                    {!! render_gallery_image_attachment_preview($listing->gallery_images ?? '') !!}
                                </div>

                                 <div class="customer__details__author__item__header mt-3">
                                    <div class="customer__details__author__item__header__flex">
                                        <div class="customer__details__author__item__header__left">
                                            <h4 class="customer__details__author__item__title">
                                                @if($listing->user_id != null && $listing->user_id != 0)
                                                    {{ __('User Info:') }}
                                                @elseif($listing->user_id === 0)
                                                    {{ __('Guest Info:') }}
                                                @else
                                                    {{ __('Admin Info:') }}
                                                @endif
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="customer__details__author__item__inner border_top_1 top_15">
                                    <div class="customer__account__details">
                                        @if($listing->user_id != null && $listing->user_id != 0)
                                            <!-- User Info -->
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <a href="{{route('about.user.profile', optional($listing->user)->username)}}" target="_blank">
                                                        <div class="customer__details__author__thumb">
                                                            <div class="seller-img">
                                                               {!! render_image_markup_by_attachment_id($listing->user->image, '', 'thumb') !!}
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Name') }}</strong>
                                                    <a href="{{route('about.user.profile', optional($listing->user)->username)}}" target="_blank">
                                                    <span>{{ optional($listing->user)->fullname }}</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Email') }}</strong>
                                                    <span>{{ optional($listing->user)->email }}</span>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Phone') }}</strong>
                                                    <span>{{ optional($listing->user)->phone }}</span>
                                                </div>
                                            </div>

                                        @elseif($listing->user_id === 0)
                                            <!-- Guest Info -->
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <div class="customer__details__author__thumb">
                                                      <img src="{{ asset('assets/frontend/img/static/user-no-image.webp') }}" alt="No Image">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Name') }}</strong>
                                                    <span>{{ optional($listing->guestListing)->guestfullname }}</span>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Email') }}</strong>
                                                    <span>{{ optional($listing->guestListing)->email }}</span>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Phone') }}</strong>
                                                    <span>{{ optional($listing->guestListing)->phone }}</span>
                                                </div>
                                            </div>

                                        @else
                                        <!-- Admin Info -->
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong></strong>
                                                    <a href="{{route('about.user.profile', optional($listing->admin)->username)}}" target="_blank">
                                                        <div class="customer__details__author__thumb">
                                                            {!! render_image_markup_by_attachment_id($listing->admin->image, '', 'thumb') !!}
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Name') }}</strong>
                                                    <a href="{{route('about.user.profile', optional($listing->admin)->username)}}" target="_blank">
                                                        <span>{{ optional($listing->admin)->name }}</span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Email') }}</strong>
                                                    <span>{{ optional($listing->admin)->email }}</span>
                                                </div>
                                            </div>
                                            <div class="customer__account__details__item">
                                                <div class="customer__account__details__item__flex">
                                                    <strong>{{ __('Phone') }}</strong>
                                                    <span>{{ optional($listing->admin)->phone }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!--step two -->
                            <div class="col-xxl-4 col-lg-4">
                                <div class="editProduct__contents">
                                    <div class="editProduct__contents__category mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Item Name:') }}</strong> {{ $listing->title }}</span>
                                    </div>
                                    <div class="editProduct__contents__category mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Price:') }}</strong> {{ float_amount_with_currency_symbol($listing->price) }}</span>
                                    </div>
                                    <div class="editProduct__contents__category mt-3">
                                           <span class="editProduct__contents__sku__para"><strong>{{ __('Negotiable:') }}</strong></span>
                                           <input class="effectBorder" type="checkbox" @if(!empty($listing->negotiable)) checked @endif>
                                           <span class="checkmark"></span>
                                    </div>
                                    <div class="editProduct__contents__category mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Category:') }}</strong> {{ optional($listing->category)->name }}</span>
                                    </div>
                                    <div class="editProduct__contents__category mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Sub Category:') }}</strong> {{ optional($listing->subcategory)->name }}</span>
                                    </div>
                                    <div class="editProduct__contents__category mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Child Category:') }}</strong> {{ optional($listing->childcategory)->name }}</span>
                                    </div>
                                    <div class="editProduct__contents__brand mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Brand:') }}</strong> {{ optional($listing->brand)->title }}</span>
                                    </div>
                                    <div class="editProduct__contents__brand mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Condition:') }}</strong> {{ $listing->condition }}</span>
                                    </div>
                                    <div class="editProduct__contents__brand mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Authenticity:') }}</strong> {{ $listing->authenticity }}</span>
                                    </div>

                                    <div class="editProduct__contents__brand mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Phone:') }}</strong> {{ $listing->phone }}</span>
                                    </div>
                                    <div class="editProduct__contents__category mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Hide Phone Number:') }}</strong></span>
                                        <input class="effectBorder" type="checkbox" @if(!empty($listing->phone_hidden)) checked @endif>
                                        <span class="checkmark"></span>
                                    </div>

                                    <div class="editProduct__contents__brand mt-3">
                                          <span class="editProduct__contents__sku__para">
                                                <strong>{{ __('Tags:') }}</strong>
                                                @forelse($listing->tags as $tag)
                                                  {{ $tag->name }}
                                                  @if (!$loop->last) , @endif
                                              @empty
                                                  {{ __('No tags available') }}
                                              @endforelse
                                          </span>
                                    </div>

                                    <div class="editProduct__contents__brand mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('View Count:') }}</strong> {{ $listing->view }}</span>
                                    </div>

                                    <div class="editProduct__contents__brand mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Status:') }}</strong>
                                            @if($listing->status==1)
                                                <span class="status_btn completed">{{__('Approved')}}</span>
                                            @else
                                                <span class="status_btn cancelled">{{__('Pending')}}</span>
                                            @endif
                                        </span>
                                    </div>

                                    @if(empty(get_static_option('google_map_settings_on_off')))
                                        <div class="editProduct__contents__brand mt-3">
                                            <span class="editProduct__contents__sku__para"><strong>{{ __('Country:') }}</strong> {{ optional($listing->country)->country }}</span>
                                        </div>

                                        <div class="editProduct__contents__brand mt-3">
                                            <span class="editProduct__contents__sku__para"><strong>{{ __('State:') }}</strong> {{ optional($listing->state)->state }}</span>
                                        </div>

                                        <div class="editProduct__contents__brand mt-3">
                                            <span class="editProduct__contents__sku__para"><strong>{{ __('City:') }}</strong> {{ optional($listing->city)->city }}</span>
                                        </div>
                                    @endif

                                    <div class="editProduct__contents__brand mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Address:') }}</strong> {{ $listing->address }}</span>
                                    </div>

                                    <div class="editProduct__contents__category mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Is Featured:') }}</strong></span>
                                        <input class="effectBorder" type="checkbox" @if(!empty($listing->is_featured)) checked @endif>
                                        <span class="checkmark"></span>
                                    </div>

                                </div>
                            </div>

                            <!--step two -->
                            <div class="col-xxl-4 col-lg-4">
                                <div class="editProduct__contents">
                                    <div class="product__details__description mt-3">
                                        <span class="editProduct__contents__sku__para"><strong>{{ __('Description:') }}</strong></span>
                                        <p class="product__details__para">{!! $listing->description !!}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js />
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script src="{{asset('assets/backend/js/fontawesome-iconpicker.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/backend/css/fontawesome-iconpicker.min.css')}}">
    <x-summernote.js/>
    <script>
        <x-icon.icon-picker/>
    </script>
    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {
                //zone
                $(document).ready(function () {
                    $('.zone_settings').select2();
                });

                // Disable clicks on the checkbox
                $('#checkbox').on('click', function(e) {
                    e.preventDefault();
                });

                // Optionally, prevent keyboard events (spacebar) to toggle checkbox
                $('#checkbox').on('keydown', function(e) {
                    if (e.which === 32) {
                        e.preventDefault();
                    }
                });

                //Permalink Code
                var sl =  $('.category_slug').val();
                var url = `{{url('/service-list/category/')}}/` + sl;
                var data = $('#slug_show').text(url).css('color', 'blue');

                function converToSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }
                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.category_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.category_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/service-list/category/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.category_slug').val(slug)
                    $('.category_slug').hide();
                });

                // for summernote
                $('.summernote').summernote({
                    height: 400,   //set editable area's height
                    codemirror: { // codemirror options
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
                if ($('.summernote').length > 0) {
                    $('.summernote').each(function (index, value) {
                        $(this).summernote('code', $(this).data('content'));
                    });
                }
            });
        })(jQuery)
    </script>
@endsection
