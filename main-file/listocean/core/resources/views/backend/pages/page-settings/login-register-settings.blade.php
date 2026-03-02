@extends('backend.admin-master')
@section('site-title')
    {{__('Login Register Settings')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Login Register Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.login.register.page.settings')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!--register page start -->
                        <div class="form__input__single">
                            <label for="register_page_title" class="form__input__single__label">{{__('Register Page Title')}}</label>
                            <input type="text" name="register_page_title"  class="form-control" value="{{get_static_option('register_page_title')}}" id="register_page_title">
                        </div>
                        <div class="form__input__single mb-3">
                            <label for="register_page_description" class="form__input__single__label">{{__('Register Page Description')}}</label>
                            <input type="text" name="register_page_description"  class="form-control" value="{{get_static_option('register_page_description')}}" id="register_page_description">
                        </div>
                      <x-image.image :title="__('Register Page Image')" :name="'register_page_image'" :dimentions="'160x50'"/>
                    <!--register page end -->

                    <div class="form__input__single">
                        <label for="login_form_title" class="form__input__single__label">{{__('Login Form Title')}}</label>
                        <input type="text" name="login_form_title"  class="form-control" value="{{get_static_option('login_form_title')}}" id="login_form_title">
                    </div>

                    @php
                        $all_pages = \App\Models\Backend\Page::select('id','title','slug')->latest()->get();
                    @endphp

                    <div class="form__input__single">
                        <label for="register_buyer_title" class="form__input__single__label">{{__('Set Terms & Condition')}}</label>
                        <select name="select_terms_condition_page" id="select_terms_condition_page" class="form-control select2_activation">
                            <option value="">{{ __('Select Page') }}</option>
                            @foreach($all_pages as $page)
                                <option @if(get_static_option('select_terms_condition_page') == $page->slug ) selected @endif value="{{ $page->slug }}">{{ $page->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form__input__single d-grid mt-3">
                        <label for="register_page_social_login_show_hide"><strong>{{__('Social Login register page show/hide')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox" name="register_page_social_login_show_hide"  @if(!empty(get_static_option('register_page_social_login_show_hide'))) checked @endif>
                            <label></label>
                        </div>
                        <small class="form-text text-muted">  {{__('Enable, means Frontend register page show social login')}} </small>
                    </div>

                    <div class="form__input__single">
                        <label for="recaptcha_2_site_key" class="form__input__single__label">{{__('Google Recaptcha 2 (Site Key)')}} </label>
                        <input type="text" name="recaptcha_2_site_key"  class="form-control" value="{{get_static_option('recaptcha_2_site_key')}}">
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
    <x-media.js />
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection
