@extends('backend.admin-master')
@section('site-title')
    {{__('404 Page Settings')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('404 Page Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.404.page.settings')}}" method="POST" class="validateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="error_404_page_title" class="form__input__single__label">{{ __('Title') }}</label>
                            <input type="text" class="form__control radius-5" name="error_404_page_title" id="error_404_page_title" value="{{get_static_option('error_404_page_title')}}" placeholder="{{__('error 404 page title')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="error_404_page_subtitle" class="form__input__single__label">{{ __('Subtitle') }}</label>
                            <input class="form__control" name="error_404_page_subtitle" id="error_404_page_subtitle" value="{{get_static_option('error_404_page_subtitle')}}">
                        </div>

                        <div class="form__input__single">
                            <label for="error_404_page_paragraph" class="form__input__single__label">{{__('Paragraph')}}</label>
                            <textarea class="form__control" name="error_404_page_paragraph" id="error_404_page_paragraph">{{get_static_option('error_404_page_paragraph')}}</textarea>
                        </div>

                        <div class="form__input__single">
                            <label for="error_404_page_button_text" class="form__input__single__label">{{ __('Button Text') }}</label>
                            <input class="form__control" name="error_404_page_button_text" id="error_404_page_button_text" value="{{get_static_option('error_404_page_button_text')}}">
                        </div>

                        <x-image.image :title="__('Error Image')" :name="'error_image'" :dimentions="'172x290'"/>

                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $("#maintenance_duration").flatpickr({
                    dateFormat: "Y-m-d",
                });
            });
        })(jQuery)
    </script>
@endsection
