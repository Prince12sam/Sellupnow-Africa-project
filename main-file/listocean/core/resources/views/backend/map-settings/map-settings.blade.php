@extends('backend.admin-master')
@section('site-title')
    {{__('Google Map Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Google Map Settings')}}  <button class="btn btn-info mx-3"><a href="https://www.youtube.com/watch?v=2_HZObVbe-g" target="_blank">{{ __('Generate API keys Video Example') }}</a></button> </h2>
                <x-validation.error/>
                <form action="{{route('admin.map.settings.page')}}" method="POST">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single d-grid">
                            <label for="google_map_settings_on_off"><strong>{{__('On/Off Google Map Settings')}}</strong></label>
                            <div class="switch_box style_7">
                                <input type="checkbox" name="google_map_settings_on_off"  @if(!empty(get_static_option('google_map_settings_on_off'))) checked @endif>
                                <label></label>
                            </div>
                        </div>
                        <div class="form__input__single">
                            <label for="google_map_api_key" class="form__input__single__label">{{ __('Google Map Api Key') }}</label>
                            <input class="form__control" name="google_map_api_key"  id="google_map_api_key" value="{{get_static_option('google_map_api_key')}}" >
                        </div>
                        <div class="form__input__single">
                            <label for="google_map_search_placeholder_title" class="form__input__single__label">{{ __('Google Map Search Placeholder Title') }}</label>
                            <input class="form__control" name="google_map_search_placeholder_title"  id="google_map_search_placeholder_title" value="{{get_static_option('google_map_search_placeholder_title')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="google_map_search_button_title" class="form__input__single__label">{{ __('Search Button Title') }}</label>
                            <input class="form__control" name="google_map_search_button_title"  id="google_map_search_button_title" value="{{get_static_option('google_map_search_button_title')}}">
                        </div>
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
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection
