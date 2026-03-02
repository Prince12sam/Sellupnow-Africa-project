@extends('backend.admin-master')
@section('site-title')
    {{__('Basic Settings')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
<div class="row g-4 mt-0">
    <div class="col-xl-6 col-lg-6 mt-0">
        <div class="dashboard__card bg__white padding-20 radius-10">
            <h2 class="dashboard__card__header__title mb-3">{{__('Basic Settings')}}</h2>
            <x-validation.error/>
            <form action="{{route('admin.general.basic.settings')}}" method="POST" class="validateForm" enctype="multipart/form-data">
                @csrf
                <div class="form__input__flex">
                    <div class="form__input__single">
                        <label for="site_title" class="form__input__single__label">{{ __('Site Title') }}</label>
                        <input type="text"  name="site_title" id="site_title" value="{{get_static_option('site_title')}}"  class="form__control radius-5">
                    </div>

                    <div class="form__input__single">
                        <label for="site_tag_line" class="form__input__single__label">{{ __('Site Tag Line') }}</label>
                        <input type="text" name="site_tag_line"  class="form__control radius-5" value="{{get_static_option('site_tag_line')}}" id="site_tag_line">
                    </div>

                    <div class="form__input__single">
                        <label for="site_footer_copyright" class="form__input__single__label">{{__('Footer Copyright')}}</label>
                        <input type="text" name="site_footer_copyright"  class="form__control radius-5" value="{{get_static_option('site_footer_copyright')}}" id="site_footer_copyright">
                        <strong class="form-text text-info">{{__('{copy} will replace by ©; and {year} will be replaced by current year.')}}</strong>
                    </div>

                    <div class="form__input__single">
                        <label for="site_canonical_url_type" class="form__input__single__label">{{__('Canonical URL Type')}}</label>
                        <select name="site_canonical_url_type" class="form__control radius-5">
                            <option @if(get_static_option('site_canonical_url_type') === 'self') selected @endif value="self">{{__('Self')}}</option>
                            <option @if(get_static_option('site_canonical_url_type') === 'alternative') selected @endif value="alternative">{{__('Alternative')}}</option>
                        </select>
                    </div>

                    <div class="form__input__single d-none">
                        <label for="language_select_option" class="form__input__single__label">
                            <strong>{{__('Language Select Show or Hide')}}</strong>
                        </label>
                        <label class="switch_box style_1 yes">
                            <input type="checkbox" name="language_select_option"  @if(!empty(get_static_option('language_select_option'))) checked @endif id="language_select_option">
                            <span class="slider onoff"></span>
                        </label>
                    </div>

                    <div class="form__input__single d-grid">
                        <label for="user_email_verify_enable_disable"><strong>{{__('User Email Verify')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox"  name="user_email_verify_enable_disable" id="user_email_verify_enable_disable" @if(!empty(get_static_option('user_email_verify_enable_disable'))) checked @endif>
                            <label></label>
                        </div>
                        <strong class="form-text text-info">{{__('enable, means user must have to verify their email account in order to access his/her dashboard.')}}</strong>
                    </div>

                    <div class="form__input__single mt-3 d-grid">
                        <label for="site_maintenance_mode"><strong>{{__('Maintenance Mode')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox" name="site_maintenance_mode"  @if(!empty(get_static_option('site_maintenance_mode'))) checked @endif id="site_maintenance_mode">
                            <label></label>
                        </div>
                    </div>
                    <div class="form__input__single d-grid">
                        <label for="site_google_captcha_enable"><strong>{{__('Enable/Disable Google Captcha')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox" name="site_google_captcha_enable"  @if(!empty(get_static_option('site_google_captcha_enable'))) checked @endif>
                            <label></label>
                        </div>
                    </div>
                    <div class="form__input__single d-grid">
                        <label for="site_force_ssl_redirection"><strong>{{__('Enable Force SSL Redirection')}}</strong></label>
                         <div class="switch_box style_7">
                            <input type="checkbox" name="site_force_ssl_redirection"  @if(!empty(get_static_option('site_force_ssl_redirection'))) checked @endif>
                            <label></label>
                        </div>
                    </div>
                    <div class="form__input__single">
                        <label for="site_default_phone_country" class="form__input__single__label">
                            {{ __('Default Phone Country') }}
                            <small class="text-muted d-block">{{ __('2-letter ISO code shown first in phone fields (e.g. gh, ng, za, ke)') }}</small>
                        </label>
                        @php
                            $allCountries = \Modules\CountryManage\app\Models\Country::all_countries();
                            $savedPhoneCountry = strtolower(get_static_option('site_default_phone_country') ?? '');
                        @endphp
                        <select name="site_default_phone_country" id="site_default_phone_country" class="form__control radius-5">
                            <option value="">— {{ __('Auto (first active country)') }} —</option>
                            @foreach($allCountries as $c)
                                @php $cc = strtolower($c->country_code ?? ''); @endphp
                                @if($cc)
                                    <option value="{{ $cc }}" {{ $savedPhoneCountry === $cc ? 'selected' : '' }}>
                                        {{ $c->country ?? $cc }} ({{ strtoupper($cc) }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form__input__single d-grid">
                        <label for="admin_loader_animation"><strong>{{__('Admin Preloader Animation')}}</strong></label>
                         <div class="switch_box style_7">
                            <input type="checkbox" name="admin_loader_animation"  @if(!empty(get_static_option('admin_loader_animation'))) checked @endif id="admin_loader_animation">
                             <label></label>
                        </div>
                    </div>

                    <div class="form__input__single d-grid">
                        <label for="site_loader_animation"><strong>{{__('Site Preloader Animation')}}</strong></label>
                         <div class="switch_box style_7">
                            <input type="checkbox" name="site_loader_animation"  @if(!empty(get_static_option('site_loader_animation'))) checked @endif id="site_loader_animation">
                             <label></label>
                        </div>
                    </div>
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
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-btn.update/>
                $(document).on("change","#user_email_verify_enable_disable",function (){
                    let current_value = $("#user_email_verify_enable_disable").is(':checked');
                    if(current_value == true){
                        $("#user_otp_verify_enable_disable").prop("checked", false)
                    }
                    if (!$(this).is(':checked')) {
                        $(".otp_time_settings_show_hide").hide();
                    }
                    if ($(this).is(':checked')) {
                        $(".otp_time_settings_show_hide").hide();
                    }
                });
                $(document).on("change","#user_otp_verify_enable_disable",function (){
                    let current_value = $("#user_otp_verify_enable_disable").is(':checked');
                    if(current_value == true){
                        $("#user_email_verify_enable_disable").prop("checked", false)
                    }
                    if ($(this).is(':checked')) {
                        $(".otp_time_settings_show_hide").show();
                    } else {
                        $(".otp_time_settings_show_hide").hide();
                    }
                });
            });
        }(jQuery));
    </script>
@endsection
