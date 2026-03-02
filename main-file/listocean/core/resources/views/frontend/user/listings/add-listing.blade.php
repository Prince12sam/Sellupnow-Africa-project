@extends('frontend.layout.master')
@section('site_title')
    {{ __('Add Listing') }}
@endsection
@section('style')
    <x-media.css/>
    <x-summernote.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <style>
        input#pac-input {
            background-color: ghostwhite;
        }
        .select2-container .select2-selection--single {
            background-color: var(--white-bg);
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            position: relative;
            height: auto;
            padding: 10px;
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
        .checkBox.position-absolute {
            right: 0;
            top: 0;
            background-color: whitesmoke;
            padding: 10px 15px;
        }

        input.effectBorder.checkBox__input {
            border: 2px solid #a3a3a3;
        }
        /* price and number css end   */

        button.btn.btn-info.media_upload_form_btn {
            background-color: rgb(239,246,255);
            border: none;
            color: rgb(59,130,246);
            outline: none;
            box-shadow: none;
            margin: auto;
        }

        .new_image_add_listing .attachment-preview {
            width: 200px;
            height: 200px;
            border-radius: 6px;
            overflow: hidden;
        }
        .new_image_add_listing .attachment-preview .thumbnail .centered img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transform: translate(-50%, -50%);
        }

        .new_image_gallery_add_listing .attachment-preview {
            width: 100px;
            height: 100px;
            border-radius: 6px;
            overflow: hidden;
        }
        .new_image_gallery_add_listing .attachment-preview .thumbnail .centered img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transform: translate(-50%, -50%);
        }

        .media-upload-btn-wrapper .img-wrap .rmv-span {
             padding: 0;
        }

        
    </style>
    <x-css.phone-number-css/>
@endsection
@section('content')
        <div class="add-listing-wrapper mt-5 mb-5">
            <!--check user verification -->
            @if($user_listing_limit_check === true)
                <div class="row w-50 align-items-center mx-auto">
                    <div class="col-lg-12 mt-1">
                        <div class="alert alert-warning d-flex justify-content-between">
                            <a href="{{ route('user.membership.all') }}">
                             {{ __('Your membership package listing limit has been reached. Please consider upgrading your membership to continue listing:') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if(get_static_option('listing_create_settings') == 'verified_user')
                @if(is_null($user_identity_verifications))
                    <div class="row w-50 align-items-center mx-auto mt-5 mb-5">
                        <div class="col-lg-12 mt-5 mb-5">
                            <div class="mt-5 mb-5">
                                <x-notice.general-notice :description="__('You cannot add listings until your account is verified.')" />
                                <button class="new-cmn-btn browse-ads mb-4"><a href="{{route('user.account.settings')}}">{{ __('Verify Your Account Now') }}</a></button>
                            </div>
                        </div>
                    </div>
                @elseif($user_identity_verifications->status != 1)
                    <div class="row w-50 align-items-center mx-auto mt-5 mb-5">
                        <div class="col-lg-12 mt-5 mb-5">
                            <div class="mt-5 mb-5">
                                <x-notice.general-notice :description="__('You cannot add listings until your account is verified.')" />
                                <button class="new-cmn-btn browse-ads mb-4"><a href="{{route('user.account.settings')}}">{{ __('Verify Your Account Now') }}</a></button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- check user verification -->
            @if(get_static_option('listing_create_settings') == 'all_user' || !is_null($user_identity_verifications) && $user_identity_verifications->status == 1)
            <!--Nav Bar Tabs markup start -->
            <div style="display: none" class="nav nav-pills" id="add-listing-tab"
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
                   href="#media-uploads"
                   role="tab"
                   aria-controls="media-uploads"
                   aria-selected="true">
                    <span class="nav-link-number">{{ __('2') }}</span>
                    {{__('Location')}}
                </a>
            </div>
            <form action="{{route('user.add.listing')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div  class="add-listing-content-wrapper">
                    <div class="tab-content add-listing-content" id="add-listing-tabContent">
                        <!-- listing Info start-->
                        <div  class="tab-pane fade step active show" id="listing-info" role="tabpanel" aria-labelledby="listing-info-tab">
                            <!--Post your add-->
                            <div class="post-your-add">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mt-3 mb-2">
                                                <x-validation.frontend-error />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-lg-8">
                                            <div class="post-add-wraper">
                                                <div class="item-name box-shadow1 p-24">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <label for="item-name" class="mb-0">{{ __('Item Name') }} <span class="text-danger">*</span></label>
                                                        @if(get_static_option('ai_listing_assistant_enabled') && in_array(strtolower(trim(get_static_option('ai_listing_assistant_enabled'))), ['1','true','yes','on','enabled']))
                                                        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" id="aiSuggestBtn" data-bs-toggle="modal" data-bs-target="#aiListingModal" title="{{ __('Let AI help you write a great listing') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zm-.5 12.5v-1a.5.5 0 0 1 1 0v1a.5.5 0 0 1-1 0zm.5-3a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0-1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/></svg>
                                                            {{ __('✨ AI Suggest') }}
                                                        </button>
                                                        @endif
                                                    </div>
                                                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="input-filed w-100" placeholder="{{ __('Item Name') }}">

                                                    <div class="input-form input-form2 permalink_label">
                                                        <label for="title" class="mt-4"> {{__('Permalink')}}  <span class="text-danger">*</span>  </label>
                                                        <span id="slug_show" class="display-inline"></span>
                                                        <span id="slug_edit" class="display-inline">
                                                        <button class="btn btn-warning btn-sm slug_edit_button">  <i class="las la-edit"></i> </button>
                                                        <input class="listing_slug input-filed w-100"  name="slug" value="{{old('slug')}}" id="slug" style="display: none" type="text">
                                                        <button class="red-btn btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                                    </span>
                                                    </div>

                                                </div>
                                                <div class="about-item box-shadow1 p-24 mt-4">
                                                    <h3 class="head4">{{ __('About Item') }}</h3>
                                                    <form action="#" class="about-item-form">
                                                        <div class="row g-3 mt-3">
                                                            <div class="col-sm-4">
                                                                <div class="item-catagory-wraper">
                                                                    <label for="item-catagory">{{ __('Item Category') }} <span class="text-danger">*</span> </label>
                                                                    <select name="category_id" id="category" class="select-itms select2_activation">
                                                                        <option value="">{{__('Select Category')}}</option>
                                                                        @foreach($categories as $cat)
                                                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="item-subcatagory-wraper">
                                                                    <label for="item-subcatagory">{{__('Sub Category')}}</label>
                                                                    <select  name="sub_category_id" id="subcategory" class="subcategory select2_activation">
                                                                        <option value="">{{__('Select Sub Category')}}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="item-subcatagory-wraper">
                                                                    <label for="item-subcatagory">{{__('Child Category')}} </label>
                                                                    <select  name="child_category_id" id="child_category" class="select2_activation">
                                                                        <option value="">{{__('Select Child Category')}}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3 g-3">
                                                            <div class="col-sm-6">
                                                                <div class="item-condition-wraper input-filed p-24 mb-sm-0 mb-3">
                                                                    <input type="checkbox" class="custom-check-box" id="item-condition">
                                                                    <label for="item-condition">{{ __('This item has Condition') }}</label>
                                                                    <div class="conditions condition_disable_enable mt-2">
                                                                        <label>
                                                                            <input type="radio" id="condition-1" name="condition" value="used" class="custom-radio-button radio_disable_color">
                                                                            <span>{{ __('Used') }}</span>
                                                                        </label>
                                                                        <label class="ms-3">
                                                                            <input type="radio" id="condition-2" name="condition" value="new" class="custom-radio-button radio_disable_color">
                                                                            <span>{{ __('New') }}</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-sm-6">
                                                                <div class="item-condition-wraper input-filed p-24">
                                                                    <input type="checkbox" class="custom-check-box" id="item-authenticity">
                                                                    <label for="item-authenticity">{{ __('This item has authenticity') }}</label>
                                                                    <div class="conditions authenticity_disable_enable mt-2">
                                                                        <label>
                                                                            <input type="radio" id="authenticity-1" name="authenticity" value="original" class="custom-radio-button radio_disable_color">
                                                                            <span>{{ __('Original') }}</span>
                                                                        </label>
                                                                        <label class="ms-3">
                                                                            <input type="radio" id="authenticity-2" name="authenticity" value="refurbished" class="custom-radio-button radio_disable_color">
                                                                            <span>{{ __('Refurbished') }}</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <div class="brand">
                                                                    <label for="item-catagory">{{ __('Brand') }}</label>
                                                                    <select name="brand_id" id="brand_id" class="select-itms select2_activation">
                                                                        <option value="">{{ __('Select Brand') }}</option>
                                                                        @foreach($brands as $brand)
                                                                            <option value="{{ $brand->id }}">{{ $brand->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="description box-shadow1 p-24 mt-4">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <label for="description" class="mb-0">{{ __('Description') }} <span class="text-danger">*</span> <span class="text-danger" style="font-size:12px;">{{ __('(minimum 150 characters.)') }}</span></label>
                                                        @if(get_static_option('ai_listing_assistant_enabled') && in_array(strtolower(trim(get_static_option('ai_listing_assistant_enabled'))), ['1','true','yes','on','enabled']))
                                                        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#aiListingModal" title="{{ __('Generate description with AI') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828l.645-1.937zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.734 1.734 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.734 1.734 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.734 1.734 0 0 0 3.407 2.31l.387-1.162zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L10.863.1z"/></svg>
                                                            {{ __('✨ AI Describe') }}
                                                        </button>
                                                        @endif
                                                    </div>
                                                    <textarea name="description" id="description" rows="6" class="input-filed w-100 textarea--form summernote" placeholder="{{__('Enter a Description')}}">{{ old('description') }}</textarea>
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="right-sidebar">
                                                <div class="box-shadow1 price p-24">
                                                    <div class="price-wraper">
                                                        <label for="price">{{ __('Price') }} <span class="text-danger">*</span> </label>
                                                        <input type="number" name="price" id="price" value="{{ old('price') }}" class="input-filed w-100 mb-3" placeholder="{{__('0.00')}}">
                                                        <label class="negotiable">
                                                            <input type="checkbox" class="custom-check-box" name="negotiable" id="negotiable">
                                                            <span class="ms-2">{{ __('Negotiable') }}</span>
                                                        </label>
                                                        <label class="negotiable mt-2 d-block">
                                                            <input type="checkbox" class="custom-check-box" name="escrow_enabled" id="escrow_enabled" value="1">
                                                            <span class="ms-2">{{ __('Enable Escrow for this listing') }}</span>
                                                        </label>
                                                        <small class="text-muted d-block mt-1" style="font-size:11px;">
                                                            {{ __('Buyers can pay securely via escrow. Funds are held until delivery is confirmed.') }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="box-shadow1 hode-phone-number p-24 mt-3">
                                                    <label class="hide-number">
                                                        <input type="checkbox" class="custom-check-box" name="hide_phone_number" value="">
                                                        <span class="black-font"> {{ __('Hide My Phone Number') }}</span>
                                                    </label>
                                                    <div class="input-group mt-3">
                                                        <input type="hidden" id="country-code" name="country_code">
                                                        <input type="tel" class="input-filed w-100" name="phone" id="phone" value="{{ old('phone') }}" placeholder="{{__('Phone')}}">
                                                        <span id="phone_availability"></span>
                                                        <div class="d-none">
                                                            <span id="error-msg" class="hide"></span>
                                                            <p id="result" class="d-none"></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="upload-img text-center mt-3">
                                                    <div class="media-upload-btn-wrapper">
                                                        <div class="img-wrap new_image_add_listing">
                                                            <img src="{{ asset('assets/common/img/listing_single_image.jpg') }}" alt="images" class="w-100">
                                                        </div>
                                                        <input type="hidden" name="image">
                                                        <button type="button" class="btn btn-info media_upload_form_btn"
                                                                data-btntitle="{{__('Select Image')}}"
                                                                data-modaltitle="{{__('Upload Image')}}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#media_upload_modal">
                                                            {{ __('Click to browse & Upload Featured Image') }}
                                                        </button>
                                                        <small>{{ __('image format: jpg,jpeg,png,gif,webp')}}</small> <br>
                                                        <small>{{ __('recommended size 810x450') }}</small>
                                                    </div>
                                                </div>


                                                <div class="picture mt-3">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <div class="upload-img text-center mt-3">
                                                                <div class="media-upload-btn-wrapper">
                                                                    <div class="img-wrap new_image_gallery_add_listing">
                                                                        <img src="{{ asset('assets/common/img/listing_single_image.jpg') }}" alt="images" class="w-100">
                                                                    </div>
                                                                    <input type="hidden" name="gallery_images">
                                                                    <button type="button" class="btn btn-info media_upload_form_btn"
                                                                            data-btntitle="{{__('Select Image')}}"
                                                                            data-modaltitle="{{__('Upload Image')}}"
                                                                            data-mulitple="true"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#media_upload_modal">
                                                                        {{__('Click to Upload Gallery Images')}}
                                                                    </button>
                                                                    <small>{{ __('image format: jpg,jpeg,png,gif,webp')}}</small> <br>
                                                                    <small>{{ __('recommended size 810x450') }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Video upload (Pro / Business members only) --}}
                                                @if($canUploadVideo ?? false)
                                                <div class="item-name box-shadow1 p-24 mt-4">
                                                    <h5 class="title">{{ __('Video (Optional)') }}
                                                        @if(($videoQuota ?? 0) !== -1 && ($videoQuota ?? 0) > 0)
                                                            <span class="badge bg-secondary small ms-2">{{ ($videoUsed ?? 0) }}/{{ $videoQuota }} {{ __('used') }}</span>
                                                        @else
                                                            <span class="badge bg-primary small ms-2">{{ __('Unlimited') }}</span>
                                                        @endif
                                                    </h5>
                                                    <div class="alert alert-warning small py-2 px-3 mb-2" style="border-left:4px solid #f0a500;background:#fffbf0;">
                                                        📱 <strong>{{ __('For best results on Reels:') }}</strong>
                                                        {{ __('Upload a vertical (portrait/9:16) video — filmed on a phone held upright. Landscape (horizontal) videos will be cropped on the Reels feed.') }}
                                                    </div>
                                                    <div class="mt-3">
                                                        <label class="form-label">{{ __('Video File') }}</label>
                                                        <input type="file" name="listing_video_file" id="listing_video_file"
                                                               class="form-control"
                                                               accept="video/mp4,video/webm,video/quicktime">
                                                        <small class="text-muted">{{ __('MP4, WebM or MOV — max 200 MB. Appears in Reels after approval.') }}</small>
                                                    </div>
                                                    {{-- Auto-captured thumbnail (shown after file chosen) --}}
                                                    <div class="mt-3" id="lv-thumb-section" style="display:none;">
                                                        <label class="form-label">{{ __('Thumbnail') }}
                                                            <small class="text-muted fw-normal">{{ __('(auto-captured from your video)') }}</small>
                                                        </label>
                                                        <canvas id="lv-canvas" style="display:none;"></canvas>
                                                        <input type="hidden" name="listing_video_thumb" id="listing_video_thumb">
                                                        <div class="d-flex align-items-start gap-3 flex-wrap">
                                                            <div style="flex:0 0 auto;">
                                                                <img id="lv-preview" src="" alt="thumbnail"
                                                                     style="width:160px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;background:#000;">
                                                            </div>
                                                            <div style="flex:1;min-width:200px;">
                                                                <label class="form-label mb-1" style="font-size:12px;">{{ __('Drag to pick a different frame') }}</label>
                                                                <input type="range" id="lv-seek" class="form-range" min="0" max="100" value="5" step="1">
                                                                <small class="text-muted" id="lv-time-label" style="font-size:11px;">{{ __('Frame at: 0s') }}</small>
                                                            </div>
                                                        </div>
                                                        <video id="lv-video" crossorigin="anonymous"
                                                               style="display:none;width:1px;height:1px;" muted playsinline></video>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="alert alert-info mt-4">
                                                    <i class="fas fa-video me-2"></i>
                                                    {{ __('Video reels are available on Pro and Business plans.') }}
                                                    <a href="{{ route('user.membership.plans') }}" class="alert-link ms-1">{{ __('Upgrade now →') }}</a>
                                                </div>
                                                @endif

                                                <!-- start previous / next buttons -->
                                                <div class="continue-btn mt-3">
                                                    <div class="btn-wrapper mb-10 d-flex justify-content-end gap-3">
                                                        <button class="red-btn w-100 d-block" style="border: none" id="nextBtn" type="button">{{__('Continue')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- listing Info end-->

                        <!-- location start-->
                        <div class="tab-pane fade step" id="media-uploads" role="tabpanel" aria-labelledby="location-tab">
                            <div class="post-your-add add-location section-padding2">
                                <div class="container-1920 plr1">
                                    <div class="row">
                                        <div class="col-xl-2">
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="address box-shadow1 p-24">
                                                @if(get_static_option('google_map_settings_on_off') == null)
                                                <div class="address-wraper">
                                                    <div class="row g-3">
                                                        <div class="col-sm-4">
                                                            <div class="country">
                                                                <label for="country">{{ __('Select Your Country') }}</label>
                                                                <select name="country_id" id="country_id" class="select2_activation">
                                                                    <option value="">{{ __('Select Country') }}</option>
                                                                    @foreach($all_countries as $country)
                                                                        <option value="{{ $country->id }}" @if(Auth::guard('web')->check() && $country->id == Auth::guard('web')->user()->country_id) selected @endif>{{ $country->country }}</option>
                                                                    @endforeach
                                                                </select><br>
                                                                <span class="country_info"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="country">
                                                                <label for="country">{{ __('Select Your State') }}</label>
                                                                <select name="state_id" id="state_id" class="get_country_state select2_activation">
                                                                    <option value="">{{ __('Select State') }}</option>
                                                                    @foreach($all_states as $state)
                                                                        <option value="{{ $state->id }}" @if(Auth::guard('web')->check() && $state->id == Auth::guard('web')->user()->state_id) selected @endif>{{ $state->state }}</option>
                                                                    @endforeach
                                                                </select> <br>
                                                                <span class="state_info"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="country">
                                                                <label for="country">{{ __('Select Your City') }}</label>
                                                                <select name="city_id" id="city_id" class="get_state_city select2_activation">
                                                                    <option value="">{{ __('Select City') }}</option>
                                                                    @foreach($all_cities as $city)
                                                                        <option value="{{ $city->id }}" @if($city->id == Auth::guard('web')->user()->city_id) selected @endif>{{ $city->city }}</option>
                                                                    @endforeach
                                                                </select><br>
                                                                <span class="city_info"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                    <!--Google Map -->
                                                    <div class="location-map mt-3">
                                                        <label class="infoTitle">{{ __('Google Map Location') }}
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
                                                    </div>
                                                @endif
                                                <div class="address-text mt-3">
                                                    <input type="hidden" name="latitude" id="latitude">
                                                    <input type="hidden" name="longitude" id="longitude">
                                                    <label for="address-text">{{ __('Address') }}</label>
                                                    <input type="text" class="w-100 input-filed" name="address" id="user_address" value="{{ old('address') }}" placeholder="{{__('Address')}}">
                                                </div>
                                            </div>
                                            <div class="video box-shadow1 p-24 mt-3 mb-3">
                                                <label for="vedio-link">{{ __('Video Url') }}</label>
                                                <input type="text"  class="input-filed w-100" name="video_url" id="video_url" value="{{ old('video_url') }}" placeholder="{{__('youtube url')}}">
                                                <div class="alert alert-warning small py-2 px-3 mt-2 mb-0" style="border-left:4px solid #f0a500;background:#fffbf0;">
                                                    📱 <strong>{{ __('For best results on Reels:') }}</strong>
                                                    {{ __('Upload a vertical (portrait/9:16) video — filmed on a phone held upright. Landscape (horizontal) videos will be cropped on the Reels feed.') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3">
                                            <div class="right-sidebar">

                                                <div class="box-shadow1 feature p-24">
                                                    <label>
                                                        <input type="checkbox" name="is_featured" id="is_featured" value="" class="custom-check-box feature_disable_color">
                                                        <span class="ms-2">{{ __('Feature This Ad') }}</span>
                                                    </label>
                                                    @if($user_featured_listing_enable === false)
                                                        <p>{{ __('To feature this ad, you will need to subscribe to a.') }}
                                                            <a href="{{ url('/' . $membership_page_url ?? 'x') }}">{{ __('paid membership') }}</a>
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="box-shadow1 tags p-24 mt-3">
                                                    <label for="tags">{{ __('Tags') }}</label>
                                                    <div class="select-itms">
                                                        <select name="tags[]" id="tags" class="select2_activation" multiple>
                                                            @foreach($tags as $tag)
                                                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small>{{ __('Select Your tags name or new tag name type') }}</small>
                                                    </div>
                                                </div>
                                                <div class="box-shadow1 tags p-24">
                                                    <x-meta.listing-meta-section/>
                                                </div>    

                                                <!-- start previous / next buttons -->
                                                <div class="continue-btn mt-3">
                                                    <div class="btn-wrapper mb-10 d-flex justify-content-end gap-3">
                                                        <button class="red-btn w-100 d-block" id="prevBtn" type="button">{{__('Previous')}}</button>
                                                        <button class="red-btn w-100 d-block" id="submitBtn" type="submit">{{__('Submit Listing')}}</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xl-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- location end-->
                    </div>
                </div>
            </form>
            @endif
        </div>
    <x-media.markup :type="'web'"/>
@endsection
@section('scripts')
    <x-frontend.js.phone-number-check-for-listing/>
    @if(!empty(get_static_option('google_map_settings_on_off')))
    <x-map.google-map-api-key-set/>
    <x-map.google-map-listing-js/>
    @endif

    <x-media.js :type="'web'"/>

    <script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
    <script src="{{asset('assets/frontend/js/multi-step.js')}}"></script>
    <x-summernote.js/>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <x-frontend.js.new-tag-add-js/>

    @if(moduleExists('Membership'))
        @if(membershipModuleExistsAndEnable('Membership'))
            @include('membership::frontend.listing.membership-listing-add-js')
        @endif
    @endif
    <x-listings.feature-ad-js :featuredenable="$user_featured_listing_enable"/>
    <x-listings.condition-authenticity/>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {


                // phone hidden
                $(document).on('change','#negotiable',function(e) {
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

                $(document).on('click', '#prevBtn', function() {
                    $("#nextBtn").show();
                    $("#submitBtn, #prevBtn").hide();
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
                    });
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
                    });
                });

            });
        })(jQuery)
    </script>

    <script>
    (function () {
        var fileInput  = document.getElementById('listing_video_file');
        if (!fileInput) return;
        var video      = document.getElementById('lv-video');
        var canvas     = document.getElementById('lv-canvas');
        var preview    = document.getElementById('lv-preview');
        var seekSlider = document.getElementById('lv-seek');
        var timeLabel  = document.getElementById('lv-time-label');
        var thumbSec   = document.getElementById('lv-thumb-section');
        var hiddenIn   = document.getElementById('listing_video_thumb');
        var duration   = 0;
        function captureFrame(t) {
            canvas.width  = video.videoWidth  || 640;
            canvas.height = video.videoHeight || 360;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            var url = canvas.toDataURL('image/jpeg', 0.82);
            hiddenIn.value = url;
            preview.src    = url;
            timeLabel.textContent = '{{ __("Frame at:") }} ' + t.toFixed(1) + 's';
        }
        fileInput.addEventListener('change', function () {
            if (!this.files[0]) return;
            video.src = URL.createObjectURL(this.files[0]);
            video.addEventListener('loadedmetadata', function onMeta() {
                video.removeEventListener('loadedmetadata', onMeta);
                duration = video.duration || 10;
                video.currentTime = Math.min(duration * 0.05, Math.min(duration - 0.1, 1));
            });
            video.addEventListener('seeked', function onFirst() {
                video.removeEventListener('seeked', onFirst);
                captureFrame(video.currentTime);
                thumbSec.style.display = 'block';
                seekSlider.addEventListener('input', function () {
                    video.currentTime = (this.value / 100) * duration;
                });
                video.addEventListener('seeked', function () { captureFrame(video.currentTime); });
            });
        });
    })();
    </script>

    @if(session('success'))
        <script>
            toastr.success('{{ session('success') }}', 'Success');
        </script>
    @endif

    @if(get_static_option('ai_listing_assistant_enabled') && in_array(strtolower(trim(get_static_option('ai_listing_assistant_enabled'))), ['1','true','yes','on','enabled']))
    {{-- ===== SmartAI Listing Assistant Modal ===== --}}
    <script>
    (function () {
        'use strict';

        var aiModal       = document.getElementById('aiListingModal');
        var aiKeywordsEl  = document.getElementById('aiKeywords');
        var aiGenerateBtn = document.getElementById('aiGenerateBtn');
        var aiSpinner     = document.getElementById('aiSpinner');
        var aiResultBox   = document.getElementById('aiResultBox');
        var aiResultTitle = document.getElementById('aiResultTitle');
        var aiResultDesc  = document.getElementById('aiResultDesc');
        var aiUsageInfo   = document.getElementById('aiUsageInfo');

        // Pre-fill keywords with whatever is already typed in the title
        if (aiModal) {
            aiModal.addEventListener('show.bs.modal', function () {
                var currentTitle = document.getElementById('title').value.trim();
                if (aiKeywordsEl && currentTitle) {
                    aiKeywordsEl.value = currentTitle;
                }
                // Reset result area
                if (aiResultBox) aiResultBox.style.display = 'none';
            });
        }

        if (aiGenerateBtn) {
            aiGenerateBtn.addEventListener('click', function () {
                var keywords  = aiKeywordsEl ? aiKeywordsEl.value.trim() : '';
                if (!keywords) {
                    alert('{{ __("Please describe what you are selling.") }}');
                    return;
                }

                var categorySelect = document.getElementById('category');
                var categoryText   = categorySelect && categorySelect.selectedIndex > 0
                    ? categorySelect.options[categorySelect.selectedIndex].text.trim()
                    : '';

                var conditionEl = document.querySelector('input[name="condition"]:checked');
                var condition   = conditionEl ? conditionEl.value : '';

                var priceEl  = document.getElementById('price');
                var price    = priceEl ? priceEl.value.trim() : '';

                // Show spinner
                aiGenerateBtn.disabled = true;
                if (aiSpinner) aiSpinner.style.display = 'inline-block';
                if (aiResultBox) aiResultBox.style.display = 'none';

                var formData = new FormData();
                formData.append('keywords',  keywords);
                formData.append('category',  categoryText);
                formData.append('condition', condition);
                formData.append('price',     price);
                formData.append('_token',    '{{ csrf_token() }}');

                fetch('{{ route("user.ai.listing.suggest") }}', {
                    method: 'POST',
                    body:   formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (resp) { return resp.json(); })
                .then(function (data) {
                    aiGenerateBtn.disabled = false;
                    if (aiSpinner) aiSpinner.style.display = 'none';

                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    if (aiResultTitle) aiResultTitle.textContent = data.title       || '';
                    if (aiResultDesc)  aiResultDesc.textContent  = data.description || '';
                    if (aiUsageInfo)   aiUsageInfo.textContent   = '{{ __("Uses today") }}: ' + (data.used_today || 1) + ' / ' + (data.daily_limit || 20);
                    if (aiResultBox)   aiResultBox.style.display = 'block';
                })
                .catch(function (err) {
                    aiGenerateBtn.disabled = false;
                    if (aiSpinner) aiSpinner.style.display = 'none';
                    alert('{{ __("AI request failed. Please try again.") }}');
                });
            });
        }

        // Apply title
        var applyTitleBtn = document.getElementById('aiApplyTitle');
        if (applyTitleBtn) {
            applyTitleBtn.addEventListener('click', function () {
                var titleInput = document.getElementById('title');
                if (titleInput && aiResultTitle) {
                    titleInput.value = aiResultTitle.textContent;
                    titleInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }

        // Apply description
        var applyDescBtn = document.getElementById('aiApplyDesc');
        if (applyDescBtn) {
            applyDescBtn.addEventListener('click', function () {
                var text = aiResultDesc ? aiResultDesc.textContent : '';
                if (!text) return;
                var descEl = document.getElementById('description');
                if (descEl) {
                    // Try Summernote API first, fallback to plain value
                    if (typeof $ !== 'undefined' && $(descEl).data('summernote')) {
                        $(descEl).summernote('code', '<p>' + text.replace(/\n/g, '<br>') + '</p>');
                    } else if (typeof $ !== 'undefined' && $(descEl).hasClass('summernote')) {
                        try { $(descEl).summernote('code', '<p>' + text.replace(/\n/g, '<br>') + '</p>'); } catch(e) { descEl.value = text; }
                    } else {
                        descEl.value = text;
                    }
                    descEl.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }

        // Apply both
        var applyBothBtn = document.getElementById('aiApplyBoth');
        if (applyBothBtn) {
            applyBothBtn.addEventListener('click', function () {
                var applyTitle = document.getElementById('aiApplyTitle');
                var applyDesc  = document.getElementById('aiApplyDesc');
                if (applyTitle) applyTitle.click();
                if (applyDesc)  applyDesc.click();
                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('aiListingModal'));
                if (modal) modal.hide();
            });
        }
    }());
    </script>

    {{-- AI Modal --}}
    <div class="modal fade" id="aiListingModal" tabindex="-1" aria-labelledby="aiListingModalLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg,#667eea,#764ba2); color:#fff;">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="aiListingModalLabel">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-stars" viewBox="0 0 16 16"><path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828l.645-1.937zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.734 1.734 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.734 1.734 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.734 1.734 0 0 0 3.407 2.31l.387-1.162zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L10.863.1z"/></svg>
                        {{ __('SmartAI Listing Assistant') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3" style="font-size:14px;">{{ __('Describe what you are selling and the AI will write a compelling title and description for you.') }}</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('What are you selling?') }} <span class="text-danger">*</span></label>
                        <textarea id="aiKeywords" class="form-control" rows="3" placeholder="{{ __('e.g. iPhone 13 Pro, 256GB, midnight black, excellent condition, original box') }}" maxlength="300"></textarea>
                        <small class="text-muted">{{ __('Include model, size, colour, condition, and any key features.') }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" id="aiGenerateBtn" class="btn btn-primary px-4">
                            <span id="aiSpinner" class="spinner-border spinner-border-sm me-2" role="status" style="display:none;"></span>
                            {{ __('Generate Suggestions') }}
                        </button>
                        <small id="aiUsageInfo" class="text-muted"></small>
                    </div>

                    {{-- Result Box --}}
                    <div id="aiResultBox" style="display:none;" class="border rounded p-3 bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-primary">{{ __('Suggested Title') }}</label>
                            <p id="aiResultTitle" class="border rounded p-2 bg-white mb-1" style="font-size:15px;line-height:1.4;min-height:38px;"></p>
                            <button type="button" id="aiApplyTitle" class="btn btn-sm btn-outline-success">{{ __('Use This Title') }}</button>
                        </div>
                        <div>
                            <label class="form-label fw-semibold text-primary">{{ __('Suggested Description') }}</label>
                            <p id="aiResultDesc" class="border rounded p-2 bg-white mb-1" style="font-size:14px;line-height:1.6;min-height:80px;white-space:pre-wrap;"></p>
                            <button type="button" id="aiApplyDesc" class="btn btn-sm btn-outline-success me-2">{{ __('Use This Description') }}</button>
                            <button type="button" id="aiApplyBoth" class="btn btn-sm btn-success">{{ __('Use Both & Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
