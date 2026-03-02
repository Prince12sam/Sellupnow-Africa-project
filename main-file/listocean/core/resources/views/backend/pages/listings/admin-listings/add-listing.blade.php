@extends('backend.admin-master')
@section('site-title')
    {{__('Add New Listing')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <x-summernote.css/>
    <x-media.css/>
    <style>
        input#pac-input {
            background-color: ghostwhite;
        }
        .select2-container .select2-selection--single {
            background-color: var(--white-bg);
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            position: relative;
            padding: 10px 5px;
        }

        span.select2.select2-container.select2-container--default.select2-container--focus {
            width: 100% !important;
        }
        .select-itms span.select2{
            width: 100% !important;
        }


        .close{ border: none;  }
        .dashboard-switch-single{
            font-size: 20px;
        }
        .swal_delete_button{
            color: #da0000 !important;
        }
        /* Default styles for the input box */
        #pac-input {
            height: 3em;
            width:75%;
            margin-left: 140px;
            border: 1px solid;
            top: 4px;
            font-size: 16px;
        }

        /* Media query for screens smaller than 768px */
        @media (max-width: 1499px) {
            #pac-input {
                width: 100%;
                margin-left: 0;
            }
        }

        /*select tags start css*/
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e3e3e3;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border: 1px solid #e3e3e3;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            font-size: 23px;
        }
        .select2-selection__choice__display {
            font-size: 15px;
            color: #000;
            font-weight: 400;
        }
        /*select tags end css*/

    /* price and number css start   */
        label.infoTitle.position-absolute {
            top: 0;
            background-color: whitesmoke;
            left: 0;
            padding: 10px 15px;
        }
        .checkBox {
            margin-top: 10px;
            border: 1px solid whitesmoke;
            border-radius: 8px;
            padding: 10px 15px;
            display: inline-block;
        }
        input#price, input#phone {
            padding: 5px 0 5px 76px;
        }
        input.effectBorder.checkBox__input {
            border: 2px solid #a3a3a3;
        }
    /* price and number css end   */

        .condition {
            padding: 13px;
            border: 2px solid #e9e9e9;
            border-radius: 6px;
        }

        .radio input {
            height: 20px;
            width: 20px;
        }
        .form__input__single {
             flex: 1;
        }
    </style>
    <x-css.phone-number-css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="header-wrap d-flex justify-content-between mb-4">
                    <div class="left-content">
                        <h4 class="header-title">{{__('Add New Listing')}}   </h4>
                    </div>
                    <div class="right-content">
                        <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{route('admin.all.listings')}}">{{__('All Listings')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="add-listing-wrapper mt-4">
                            <!--Nav Bar Tabs markup start -->
                            <div class="nav nav-pills d-none" id="add-listing-tab"
                                 role="tablist" aria-orientation="vertical">
                                <a class="nav-link  stepIndicator active stepForm_btn__previous"
                                   id="listing-info-tab"
                                   data-bs-toggle="pill"
                                   href="#listing-info"
                                   role="tab"
                                   aria-controls="listing-info"
                                   aria-selected="true">
                                    <span class="nav-link-number">{{ __('1') }}</span>
                                    {{__('Listing Info')}}
                                </a>
                                <a class="nav-link  stepIndicator"
                                   id="location-tab"
                                   data-bs-toggle="pill"
                                   href="#location"
                                   role="tab"
                                   aria-controls="location"
                                   aria-selected="true">
                                    <span class="nav-link-number">{{ __('2') }}</span>
                                    {{__('Location')}}
                                </a>
                            </div>
                            <form action="{{route('admin.add.new.listing')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div  class="add-listing-content-wrapper mt-4">
                                    <div class="tab-content add-listing-content" id="add-listing-tabContent">

                                        <!-- listing Info start-->
                                        <div  class="tab-pane fade step active show" id="listing-info" role="tabpanel" aria-labelledby="listing-info-tab">
                                             <div class="row">
                                                    <div class="col-lg-8">
                                                        <!-- Title -->
                                                        <div class="form__input__single">
                                                            <label class="form__input__single__label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                            <input type="text" class="form__control radius-5" name="title" id="title" value="{{ old('title') }}" placeholder="{{__('Add title')}}">
                                                        </div>

                                                        <div class="form__input__single mt-2">
                                                            <div class="input-form input-form2 permalink_label">
                                                                <label for="title" class="form__input__single__label text-dark"> {{__('Permalink')}}  <span class="text-danger">*</span>  </label>
                                                                <span id="slug_show" class="display-inline"></span>
                                                                <span id="slug_edit" class="display-inline d-inline">
                                                                    <button class="btn btn-warning btn-sm slug_edit_button">  <i class="las la-edit"></i> </button>
                                                                    <input class="listing_slug form__control radius-5" name="slug" value="{{old('slug')}}" id="slug" style="display: none" type="text">
                                                                    <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                                               </span>
                                                            </div>
                                                        </div>

                                                            <div class="d-flex justify-content-between gap-3 flex-wrap mt-3">
                                                                <div class="form__input__single">
                                                                    <label class="form__input__single__label">{{ __('Category') }}  <span class="text-danger">*</span> </label>
                                                                    <select name="category_id" id="category" class="select-itms select2_activation">
                                                                        <option value="">{{__('Select Category')}}</option>
                                                                        @foreach($categories as $cat)
                                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="form__input__single" id="sub_category">
                                                                    <label for="subcategory" class="form__input__single__label"> {{__('Sub Category')}} </label>
                                                                    <select  name="sub_category_id" id="subcategory" class="subcategory select2_activation">
                                                                        <option value="">{{__('Select Sub Category')}}</option>
                                                                    </select>
                                                                </div>


                                                                <div class="form__input__single child_category_wrapper">
                                                                    <label for="child_category" class="form__input__single__label"> {{__('Child Category')}} </label>
                                                                    <select  name="child_category_id" id="child_category" class="select2_activation">
                                                                        <option value="">{{__('Select Child Category')}}</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        <div class="d-flex justify-content-between gap-3 flex-wrap mt-3 mb-3">
                                                            <div class="form__input__single">
                                                                <div class="condition">
                                                                    <input type="checkbox" class="custom-check-box  me-2" id="item-condition">
                                                                    <label for="item-condition" class="form__input__single__label">{{ __('This item has Condition') }}</label>

                                                                    <div class="cs_radio_btn d-flex gap-3 conditions condition_disable_enable">
                                                                        <div class="radio d-flex align-items-center gap-2">
                                                                            <input id="condition-1" name="condition" type="radio" tabindex="0" value="used" class="radio_disable_color">
                                                                            <label for="condition-1" class="radio-label">{{ __('Used') }}</label>
                                                                        </div>
                                                                        <div class="radio d-flex align-items-center gap-2">
                                                                            <input id="condition-2" name="condition" type="radio" tabindex="0" value="new" class="radio_disable_color">
                                                                            <label for="condition-2" class="radio-label">{{ __('New') }}</label>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                            <div class="form__input__single">
                                                                <div class="condition">
                                                                    <input type="checkbox" class="custom-check-box me-2" id="item-authenticity">
                                                                    <label for="item-authenticity" class="form__input__single__label">{{ __('This item has Authenticity') }}</label>

                                                                    <div class="cs_radio_btn d-flex gap-3 conditions authenticity_disable_enable">
                                                                        <div class="radio d-flex align-items-center gap-2">
                                                                            <input id="authenticity-1" name="authenticity" type="radio" tabindex="0" value="original" class="radio_disable_color">
                                                                            <label for="authenticity-1" class="radio-label">{{ __('Original') }}</label>
                                                                        </div>
                                                                        <div class="radio d-flex align-items-center gap-2">
                                                                            <input id="authenticity-2" name="authenticity" type="radio" tabindex="0" value="refurbished" class="radio_disable_color">
                                                                            <label for="authenticity-2" class="radio-label">{{ __('Refurbished') }}</label>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!--Brand -->
                                                        <div class="form__input__single w-100">
                                                            <label class="form__input__single__label">{{ __('Select Brand') }}</label>
                                                            <div class="select-itms">
                                                                <select name="brand_id" id="brand_id" class="select2_activation">
                                                                    <option  value="">{{ __('Select Brand') }}</option>
                                                                    @foreach($brands as $brand)
                                                                        <option value="{{ $brand->id }}">{{ $brand->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Description -->
                                                        <div class="form__input__single mt-3">
                                                            <label class="form__input__single__label">{{ __('Description') }} <span class="text-danger">*</span> <span class="text-danger">{{ __('minimum 150 characters.') }}</span> </label>
                                                            <div class="input-form input-form2">
                                                                <textarea class="textarea--form summernote" name="description" placeholder="{{__('Type Description')}}">{{ old('description') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <x-meta.meta-section/>

                                                    </div>

                                                    <!--2nd step -->
                                                     <div class="col-lg-4">
                                                         <!-- Price -->
                                                         <div class="col-lg-12 col-md-12 mt-5">
                                                             <div class="form__input__single position-relative">
                                                                 <label class="infoTitle position-absolute">{{ __('Price') }} <span class="text-danger">*</span></label>
                                                                 <div class="input-form input-form2">
                                                                     <input type="number" class="form__control radius-5" name="price" id="price" value="{{ old('price') }}" placeholder="{{__('0.00')}}">
                                                                 </div>
                                                                 <div class="checkBox ">
                                                                     <label class="negotiable d-flex flex-row-reverse gap-3">{{ __('Negotiable') }}
                                                                         <input class="effectBorder checkBox__input" type="checkbox" value="" name="negotiable">
                                                                     </label>
                                                                 </div>
                                                             </div>
                                                         </div>

                                                         <!-- Phone -->
                                                         <div class="col-12 mt-3">
                                                             <div class="form__input__single position-relative">
                                                                 <input type="hidden" id="country-code" name="country_code">
                                                                 <input type="tel"  class="form__control radius-5"  name="phone" id="phone"  value="{{ old('phone') }}" placeholder="{{__('Phone')}}">

                                                                 <span id="phone_availability"></span>
                                                                 <div class="d-none">
                                                                     <span id="error-msg" class="hide"></span>
                                                                     <p id="result" class="d-none"></p>
                                                                 </div>

                                                                 <br>

                                                                 <div class="checkBox">
                                                                     <label class="hide_phone_number  d-flex flex-row-reverse gap-2">{{ __('Hide Phone Number') }}
                                                                         <input class="effectBorder checkBox__input" type="checkbox" value="" name="hide_phone_number">
                                                                         <span class="checkmark"></span>
                                                                     </label>
                                                                 </div>
                                                             </div>
                                                         </div>




                                                         <div class="col-lg-12 mt-3">
                                                             <div class="upload-img">
                                                                 <div class="media-upload-btn-wrapper">
                                                                     <div class="img-wrap">
                                                                         <img src="{{ asset('assets/frontend/img/gallery/single-image-upload.png') }}" alt="images" class="w-100">
                                                                     </div>
                                                                     <input type="hidden" name="image">
                                                                     <button type="button" class="btn btn-info media_upload_form_btn"
                                                                             data-btntitle="{{__('Select Image')}}"
                                                                             data-modaltitle="{{__('Upload Image')}}"
                                                                             data-bs-toggle="modal"
                                                                             data-bs-target="#media_upload_modal">
                                                                         {{__('Upload Main Image')}}
                                                                     </button>
                                                                     <small>{{ __('image format: jpg,jpeg,png,gif,webp')}}</small> <br>
                                                                     <small>{{ __('recommended size 810x450') }}</small>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                         <div class="col-lg-12 mt-3">
                                                             <div class="upload-img">
                                                                 <div class="media-upload-btn-wrapper">
                                                                     <div class="img-wrap">
                                                                         <img src="{{ asset('assets/frontend/img/gallery/uploadeImg.png') }}" alt="images" class="w-100">
                                                                     </div>
                                                                     <input type="hidden" name="gallery_images">
                                                                     <button type="button" class="btn btn-info media_upload_form_btn"
                                                                             data-btntitle="{{__('Select Image')}}"
                                                                             data-modaltitle="{{__('Upload Image')}}"
                                                                             data-mulitple="true"
                                                                             data-bs-toggle="modal"
                                                                             data-bs-target="#media_upload_modal">
                                                                         {{__('Upload Gallery Images')}}
                                                                     </button>
                                                                     <small>{{ __('image format: jpg,jpeg,png,gif,webp')}}</small> <br>
                                                                     <small>{{ __('recommended size 810x450') }}</small>
                                                                 </div>
                                                             </div>
                                                         </div>

                                                         <!-- start previous / next buttons -->
                                                         <div  class="col-lg-12 mt-5">
                                                             <div class="btn_wrapper d-flex justify-content-end gap-3">
                                                                 <button class="cmnBtn btn_5 btn_bg_blue radius-5" id="nextBtn" type="button">{{__('Next')}}</button>
                                                             </div>
                                                         </div>

                                                    </div>
                                                </div>
                                        </div>
                                        <!-- listing Info end-->

                                        <!-- location start-->
                                        <div class="tab-pane fade step" id="location" role="tabpanel" aria-labelledby="location-tab">
                                            <div class="row">
                                                <div class="col-8">
                                                    @if(get_static_option('google_map_settings_on_off') == null)
                                                        <div class="d-flex justify-content-between gap-3">
                                                            <div class="input-form input-form2 w-100">
                                                                <label class="form__input__single__label">{{ __('Select Your Country') }}</label>
                                                                <select name="country_id" id="country_id" class="select2_activation">
                                                                    <option value="">{{ __('Select Country') }}</option>
                                                                    @foreach($all_countries as $country)
                                                                        <option value="{{ $country->id }}">{{ $country->country }}</option>
                                                                    @endforeach
                                                                </select><br>
                                                                <span class="country_info"></span>
                                                            </div>

                                                            <div class="input-form input-form2 w-100">
                                                                <label class="form__input__single__label">{{ __('Select Your State') }}</label>
                                                                <select name="state_id" id="state_id" class="get_country_state select2_activation">
                                                                    <option value="">{{ __('Select State') }}</option>
                                                                    @foreach($all_states as $state)
                                                                        <option value="{{ $state->id }}">{{ $state->state }}</option>
                                                                    @endforeach
                                                                </select> <br>
                                                                <span class="state_info"></span>
                                                            </div>
                                                            <div class="input-form input-form2 w-100">
                                                                <label class="form__input__single__label">{{ __('Select Your City') }}</label>
                                                                <select name="city_id" id="city_id" class="get_state_city select2_activation">
                                                                    <option value="">{{ __('Select City') }}</option>
                                                                    @foreach($all_cities as $city)
                                                                        <option value="{{ $city->id }}">{{ $city->city }}</option>
                                                                    @endforeach
                                                                </select><br>
                                                                <span class="city_info"></span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!--Google Map -->
                                                        <label class="form__input__single__label">{{ __('Google Map Location') }}
                                                            <a href="https://drive.google.com/file/d/1BwDAjSLAeb4LaxzOkrdsgGO_Io2jM6S6/view?usp=sharing" target="_blank">
                                                                <strong class="text-warning">{{__('Video link')}}</strong>
                                                            </a><small class="text-info">{{__('Search your location, pick a location')}} </small>
                                                        </label>
                                                        <div class="input-form input-form2">
                                                            <div class="map-warper dark-support rounded overflow-hidden">
                                                                <input id="pac-input" class="controls rounded" type="text" placeholder="{{ __('Search your location')}}"/>
                                                                <div id="map_canvas" style="height: 480px"></div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Address -->
                                                    <div class="form__input__single">
                                                        <input type="hidden" name="latitude" id="latitude">
                                                        <input type="hidden" name="longitude" id="longitude">
                                                        <label class="form__input__single__label">{{ __('Address') }}</label>
                                                        <div class="input-form input-form2">
                                                            <input  type="text" class="form__control radius-5" name="address" id="user_address" value="{{ old('address') }}" placeholder="{{__('Address')}}">
                                                        </div>
                                                    </div>

                                                    <!-- video url -->
                                                    <div class="form__input__single">
                                                        <label class="form__input__single__label">{{ __('Video Url') }} </label>
                                                        <div class="input-form input-form2">
                                                            <input type="text" class="form__control radius-5" name="video_url" id="video_url" value="{{ old('video_url') }}" placeholder="{{__('youtube url')}}">
                                                        </div>
                                                        <small class="text-danger video_url_design">{{ __('Example:') }} https://www.youtube.com/watch?v=IcM8_Llgxf4&t=1s </small>
                                                    </div>
                                                </div>

                                                <div class="col-4">
                                                    <!-- featured listings -->
                                                    <div class="form__input__single">
                                                        <div class="checkBox">
                                                            <label class="is_featured form__input__single__label d-flex gap-2">
                                                                <input class="checkBox__input effectBorder" type="checkbox" name="is_featured" id="is_featured" value="">
                                                                {{ __('Is Featured') }}
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- Tags -->
                                                    <div class="form__input__single">
                                                        <label class="form__input__single__label">{{ __('Tags') }}</label>
                                                        <select name="tags[]" id="tags" class="select2_activation" multiple autocomplete="off">
                                                            @foreach($tags as $tag)
                                                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small>{{ __('Select Your tags name or new tag name type') }}</small>
                                                    </div>

                                                    <!-- start previous / next buttons -->
                                                    <div  class="col-lg-12 mt-5">
                                                        <div class="btn_wrapper d-flex justify-content-end gap-3">
                                                            <button class="cmnBtn btn_5 btn_bg_info radius-5" id="prevBtn" type="button">{{__('Previous')}}</button>
                                                            <button class="cmnBtn btn_5 btn_bg_primary radius-5" id="submitBtn" type="submit">{{__('Submit')}}</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- location end-->
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-frontend.js.phone-number-check-for-listing/>
    <x-media.js />
    <x-summernote.js/>
    <x-frontend.js.new-tag-add-js/>
    @if(!empty(get_static_option('google_map_settings_on_off')))
    <x-map.google-map-api-key-set/>
    <x-map.google-map-listing-js/>
    @endif
    <script src="{{asset('assets/frontend/js/multi-step.js')}}"></script>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <x-listings.condition-authenticity/>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {

                // is featured
                $(document).on('click', '.is_featured', function () {
                    $('#is_featured').val($('#is_featured').is(':checked') ? '1' : '');
                });

                // Radio button change event
                $(document).on('click', 'input[name="condition"]', function() {
                    $('#hiddenCondition').val($(this).val());
                });

                // phone hidden
                $(document).on('change','#negotiable',function(e) {
                    e.preventDefault();
                    if ($(this).is(':checked')) {
                        let negotiable = 1;
                        $('#negotiable').val(negotiable);
                    }else{
                        let negotiable = 0;
                        $('#negotiable').val(negotiable);
                    }
                });

                // phone hidden
                $(document).on('change','#phone_hidden',function(e) {
                    e.preventDefault();
                    if ($(this).is(':checked')) {
                        let phone_hidden = 1;
                        $('#phone_hidden').val(phone_hidden);
                    }else{
                        let phone_hidden = 0;
                        $('#phone_hidden').val(phone_hidden);
                    }
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
                $(document).on('change','#subcategory', function() {
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
                });

            });
        })(jQuery)
    </script>
    @if(session('success'))
        <script>
            toastr.success('{{ session('success') }}', 'Success');
        </script>
    @endif
@endsection
