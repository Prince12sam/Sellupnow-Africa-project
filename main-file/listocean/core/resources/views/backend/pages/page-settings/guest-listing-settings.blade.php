@extends('backend.admin-master')
@section('site-title')
    {{__('Guest Listing Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <x-validation.error/>
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Guest Listing Settings')}}</h2>
                <form action="{{route('admin.listing.guest.settings')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form__input__single d-grid">
                        <label for="guest_listing_allowed_disallowed"><strong>{{__('Guest Listing allowed/disallowed')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox" name="guest_listing_allowed_disallowed"  @if(!empty(get_static_option('guest_listing_allowed_disallowed'))) checked @endif>
                            <label></label>
                        </div>
                        <small class="form-text text-muted">
                            {{__('Enable to Allow Guest Listings: By enabling this option, guests will be allowed to create listings without registering or logging in.
                                    When a user checks the registration agreement button, they will be automatically registered and logged in, and their user information will be used to create the listing.')}}
                        </small>
                    </div>

                    <div class="form__input__single">
                        <label for="guest_add_listing_info_section_title" class="form__input__single__label">{{__('Guest add listing info section title')}}</label>
                        <input class="form--control" name="guest_add_listing_info_section_title"  value="{{ get_static_option('guest_add_listing_info_section_title') }}" placeholder="{{ __('title') }}">
                    </div>

                    <div class="form__input__single">
                        <label for="guest_registration_agreement_title" class="form__input__single__label">{{__('Guest Registration Agreement title')}}</label>
                        <input class="form--control" name="guest_registration_agreement_title"  value="{{ get_static_option('guest_registration_agreement_title') }}" placeholder="{{ __('title') }}">
                    </div>

                    <div class="form__input__single">
                        <label for="guest_listing_gallery_image_upload_limit" class="form__input__single__label">{{__('Guest Listing Gallery Image Upload Limit')}}</label>
                        <input class="form--control" name="guest_listing_gallery_image_upload_limit"  value="{{ get_static_option('guest_listing_gallery_image_upload_limit') }}" placeholder="{{ __('0') }}">
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-summernote.js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection
