@extends('backend.admin-master')
@section('site-title')
    {{__('Listing User Public Profile Page Settings')}}
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
        <form action="{{route('admin.user.public.profile.settings')}}" method="POST">
            @csrf

             <div class="col-6">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__('User Public Profile Advertisements')}}</h2>
                        <div class="form__input__single">
                            <label for="user_public_profile_page_advertisement_type" class="form__input__single__label">{{__('Advertisement Type')}}</label>
                            <select class="form-control" name="user_public_profile_page_advertisement_type" id="user_public_profile_page_advertisement_type">
                                <option selected disabled >{{__('Select a Type')}}</option>
                                <option @if(get_static_option('user_public_profile_page_advertisement_type') === 'image') selected @endif value="image">{{__('Image')}}</option>
                                <option @if(get_static_option('user_public_profile_page_advertisement_type') === 'google_adsense') selected  @endif value="google_adsense">{{__('Google Adsense')}}</option>
                                <option @if(get_static_option('user_public_profile_page_advertisement_type') === 'scripts') selected  @endif value="scripts">{{__('Scripts')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="user_public_profile_page_advertisement_size" class="form__input__single__label">{{__('Advertisement Size')}}</label>
                            <select class="form-control" name="user_public_profile_page_advertisement_size" id="user_public_profile_page_advertisement_size">
                                    <option selected disabled>{{__('Select a Size')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '350*250') selected @endif value="350*250">{{__('350 x 250')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '320*50') selected @endif value="320*50">{{__('320 x 50')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '160*600') selected @endif value="160*600">{{__('160 x 600')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '300*600') selected @endif value="300*600">{{__('300 x 600')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '336*280') selected @endif value="336*280">{{__('336 x 280')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '728*90') selected @endif value="728*90">{{__('728 x 90')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '730*180') selected @endif value="730*180">{{__('730 x 180')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '730*210') selected @endif value="730*210">{{__('730 x 210')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '300*1050') selected @endif value="300*1050">{{__('300 X 1050')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '950*160') selected @endif value="950*160">{{__('950 X 160')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '950*200') selected @endif value="950*200">{{__('950 X 200')}}</option>
                                    <option @if(get_static_option('user_public_profile_page_advertisement_size') === '250*1110') selected @endif value="250*1110">{{__('250 X 1110')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="user_public_profile_page_advertisement_alignment" class="form__input__single__label">{{__('Advertisement Alignment')}}</label>
                            <select class="form-control" name="user_public_profile_page_advertisement_alignment" id="user_public_profile_page_advertisement_alignment">
                                <option selected disabled>{{__('Select a Size')}}</option>
                                <option @if(get_static_option('user_public_profile_page_advertisement_alignment') === 'start') selected @endif value="start">{{__('Left')}}</option>
                                <option @if(get_static_option('user_public_profile_page_advertisement_alignment') === 'end') selected @endif value="end">{{__('Right')}}</option>
                                <option @if(get_static_option('user_public_profile_page_advertisement_alignment') === 'center') selected @endif value="center">{{__('Center')}}</option>
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
