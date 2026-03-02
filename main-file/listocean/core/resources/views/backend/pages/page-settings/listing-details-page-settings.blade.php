@extends('backend.admin-master')
@section('site-title')
    {{__('Listing Details Page Settings')}}
@endsection
@section('style')
    <x-summernote.css/>
    <style>
        .dashboard__card {
            height: auto;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <x-validation.error/>
        <form action="{{route('admin.listing.details.settings')}}" method="POST">
            @csrf
            <div class="col-xl-6 col-lg-6 mt-4">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__('Listing Details Page Settings')}}</h2>
                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Default Phone Number Title')}}</label>
                            <input class="form--control" name="listing_default_phone_number_title"  value="{{ get_static_option('listing_default_phone_number_title') }}" placeholder="{{ __('Default Phone Number Title') }}">
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Show/Hide Button Title')}}</label>
                            <input class="form--control" name="listing_phone_number_show_hide_button_title" value="{{ get_static_option('listing_phone_number_show_hide_button_title') }}"  placeholder="{{ __('Show/Hide Button Title') }}">
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Report Button Title')}}</label>
                            <input class="form--control" name="listing_report_button_title" value="{{ get_static_option('listing_report_button_title') }}"  placeholder="{{ __('Report Button Title') }}">
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Share Button Title')}}</label>
                            <input class="form--control" name="listing_share_button_title" value="{{ get_static_option('listing_share_button_title') }}"  placeholder="{{ __('Share Button Title') }}">
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Show Phone Number Title')}}</label>
                            <input class="form--control" name="listing_show_phone_number_title" value="{{ get_static_option('listing_show_phone_number_title') }}"  placeholder="{{ __('Show Phone Number Title') }}">
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Safety Tips Title')}}</label>
                            <input class="form--control" name="listing_safety_tips_title" value="{{ get_static_option('listing_safety_tips_title') }}"  placeholder="{{ __('Safety Tips Title') }}">
                        </div>

                      <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Location Title')}}</label>
                            <input class="form--control" name="listing_location_title" value="{{ get_static_option('listing_location_title') }}"  placeholder="{{ __('Location Title') }}">
                        </div>

                      <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Description Title')}}</label>
                            <input class="form--control" name="listing_description_title" value="{{ get_static_option('listing_description_title') }}"  placeholder="{{ __('Description Title') }}">
                        </div>

                     <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Tag Title')}}</label>
                            <input class="form--control" name="listing_tag_title" value="{{ get_static_option('listing_tag_title') }}"  placeholder="{{ __('Tag Title') }}">
                        </div>
                       <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Relevant Ads Title')}}</label>
                            <input class="form--control" name="listing_relevant_title" value="{{ get_static_option('listing_relevant_title') }}"  placeholder="{{ __('Relevant Ads Title') }}">
                        </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 mt-4">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__('Safety Tips')}}</h2>
                        <input type="hidden" name="safety_tips_check" value="safety_tips_add">
                        <!-- Safety -->
                        <div class="form__input__single">
                            <label class="form__input__single__label">{{ __('Safety Tips') }} </label>
                            <div class="input-form input-form2">
                                <textarea class="textarea--form summernote" name="safety_tips_info" placeholder="{{__('Type Safety Tips')}}">{{ get_static_option('safety_tips_info') }}</textarea>
                            </div>
                        </div>
                </div>
            </div>
             <div class="col-6 mt-4">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__('Left side Advertisements')}}</h2>
                        <div class="form__input__single">
                            <label for="left_listing_details_page_advertisement_type" class="form__input__single__label">{{__('Advertisement Type')}}</label>
                            <select class="form-control" name="left_listing_details_page_advertisement_type" id="left_listing_details_page_advertisement_type">
                                <option selected disabled >{{__('Select a Type')}}</option>
                                <option @if(get_static_option('left_listing_details_page_advertisement_type') === 'image') selected @endif value="image">{{__('Image')}}</option>
                                <option @if(get_static_option('left_listing_details_page_advertisement_type') === 'google_adsense') selected  @endif value="google_adsense">{{__('Google Adsense')}}</option>
                                <option @if(get_static_option('left_listing_details_page_advertisement_type') === 'scripts') selected  @endif value="scripts">{{__('Scripts')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="left_listing_details_page_advertisement_size" class="form__input__single__label">{{__('Advertisement Size')}}</label>
                            <select class="form-control" name="left_listing_details_page_advertisement_size" id="left_listing_details_page_advertisement_size">
                                    <option selected disabled>{{__('Select a Size')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '350*250') selected @endif value="350*250">{{__('350 x 250')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '320*50') selected @endif value="320*50">{{__('320 x 50')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '160*600') selected @endif value="160*600">{{__('160 x 600')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '300*600') selected @endif value="300*600">{{__('300 x 600')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '336*280') selected @endif value="336*280">{{__('336 x 280')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '728*90') selected @endif value="728*90">{{__('728 x 90')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '730*180') selected @endif value="730*180">{{__('730 x 180')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '730*210') selected @endif value="730*210">{{__('730 x 210')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '300*1050') selected @endif value="300*1050">{{__('300 X 1050')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '950*160') selected @endif value="950*160">{{__('950 X 160')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '950*200') selected @endif value="950*200">{{__('950 X 200')}}</option>
                                    <option @if(get_static_option('left_listing_details_page_advertisement_size') === '250*1110') selected @endif value="250*1110">{{__('250 X 1110')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="left_listing_details_page_advertisement_alignment" class="form__input__single__label">{{__('Advertisement Alignment')}}</label>
                            <select class="form-control" name="left_listing_details_page_advertisement_alignment" id="left_listing_details_page_advertisement_alignment">
                                <option selected disabled>{{__('Select a Size')}}</option>
                                <option @if(get_static_option('left_listing_details_page_advertisement_alignment') === 'start') selected @endif value="start">{{__('Left')}}</option>
                                <option @if(get_static_option('left_listing_details_page_advertisement_alignment') === 'end') selected @endif value="end">{{__('Right')}}</option>
                                <option @if(get_static_option('left_listing_details_page_advertisement_alignment') === 'center') selected @endif value="center">{{__('Center')}}</option>
                            </select>
                        </div>
                </div>
            </div>
            <div class="col-6 mt-4">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__('Right side Advertisements')}}</h2>
                        <div class="form__input__single">
                            <label for="right_listing_details_page_advertisement_type" class="form__input__single__label">{{__('Advertisement Type')}}</label>
                            <select class="form-control" name="right_listing_details_page_advertisement_type" id="right_listing_details_page_advertisement_type">
                                <option selected disabled>{{__('Select a Type')}}</option>
                                <option @if(get_static_option('right_listing_details_page_advertisement_type') === 'image') selected @endif value="image">{{__('Image')}}</option>
                                <option @if(get_static_option('right_listing_details_page_advertisement_type') === 'google_adsense') selected  @endif value="google_adsense">{{__('Google Adsense')}}</option>
                                <option @if(get_static_option('right_listing_details_page_advertisement_type') === 'scripts') selected  @endif value="scripts">{{__('Scripts')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="right_listing_details_page_advertisement_size" class="form__input__single__label">{{__('Advertisement Size')}}</label>
                            <select class="form-control" name="right_listing_details_page_advertisement_size" id="right_listing_details_page_advertisement_size">
                                    <option selected disabled>{{__('Select a Size')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '350*250') selected @endif value="350*250">{{__('350 x 250')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '320*50') selected @endif value="320*50">{{__('320 x 50')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '160*600') selected @endif value="160*600">{{__('160 x 600')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '300*600') selected @endif value="300*600">{{__('300 x 600')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '336*280') selected @endif value="336*280">{{__('336 x 280')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '728*90') selected @endif value="728*90">{{__('728 x 90')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '730*180') selected @endif value="730*180">{{__('730 x 180')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '730*210') selected @endif value="730*210">{{__('730 x 210')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '300*1050') selected @endif value="300*1050">{{__('300 X 1050')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '950*160') selected @endif value="950*160">{{__('950 X 160')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '950*200') selected @endif value="950*200">{{__('950 X 200')}}</option>
                                    <option @if(get_static_option('right_listing_details_page_advertisement_size') === '250*1110') selected @endif value="250*1110">{{__('250 X 1110')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="right_listing_details_page_advertisement_alignment" class="form__input__single__label">{{__('Advertisement Alignment')}}</label>
                            <select class="form-control" name="right_listing_details_page_advertisement_alignment" id="right_listing_details_page_advertisement_alignment">
                                <option selected disabled>{{__('Select a Size')}}</option>
                                <option @if(get_static_option('right_listing_details_page_advertisement_alignment') === 'start') selected @endif value="start">{{__('Left')}}</option>
                                <option @if(get_static_option('right_listing_details_page_advertisement_alignment') === 'end') selected @endif value="end">{{__('Right')}}</option>
                                <option @if(get_static_option('right_listing_details_page_advertisement_alignment') === 'center') selected @endif value="center">{{__('Center')}}</option>
                            </select>
                        </div>

                </div>
            </div>
            <div class="btn_wrapper mt-4">
                <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
            </div>
        </form>
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
