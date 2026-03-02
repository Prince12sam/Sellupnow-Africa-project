@extends('backend.admin-master')
@section('site-title')
    {{__('License Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('License Settings')}}</h2>
                <x-validation.error/>
                <div class="btn_wrapper mt-4 mb-4">
                    <button type="submit"
                            class="cmnBtn btn_5 btn_bg_info radius-5"
                            data-bs-toggle="modal"
                            data-bs-target="#licenseRequestModal">
                           {{ __('Get License Key') }}
                    </button>
                </div>
                @if('verified' == get_static_option('item_license_status'))
                    <div class="alert alert-success">{{__('Your Application is Registered')}}</div>
                @endif
                <form action="{{route('admin.general.license.settings')}}" method="POST">
                    @csrf
                    <div class="form__input__flex">
                        <x-notice.general-notice :description="__('Notice: enter license key, which you get in your email after verify your license while install or you can get your license by click on Get License Key then system will send you a license code into your email, check your email inbox and spam folder as well.')" />
                        <div class="form__input__single">
                            <label for="site_license_key" class="form__input__single__label">{{ __('License Key') }}</label>
                            <input type="text" class="form__control radius-5" name="site_license_key" id="site_license_key" value="{{get_static_option('site_license_key')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="envato_username" class="form__input__single__label">{{__('Envato Username')}}</label>
                            <textarea class="form__control" name="envato_username" id="envato_username">{{get_static_option('envato_username')}}</textarea>
                        </div>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Submit Information') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- License Modal -->
    <div class="modal fade" id="licenseRequestModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="popup_contents modal-content">
                    <div class="popup_contents__header">
                        <div class="popup_contents__header__flex">
                            <div class="popup_contents__header__contents">
                                <h2 class="popup_contents__header__title">{{ __('Request for license key...') }}</h2>
                            </div>
                            <div class="popup_contents__header__close" data-bs-dismiss="modal">
                                <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                            </div>
                        </div>
                    </div>
                    <div class="popup_contents__body">
                        <form action="{{route("admin.general.license.key.generate")}}" id="user_password_change_modal_form" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form__input__single">
                                <label for="email" class="form__input__single__label">{{ __('Your Email') }}</label>
                                <input type="email" class="form__control radius-5" name="email" value="{{get_static_option('license_email')}}">
                                <small class="text-dark">{{__("Make sure you have given valid email, we will send you license key for enable one click update, We'll email you script updates - no spam, just the good stuff!")}} 🌟✉️</small>
                            </div>
                            <div class="form__input__single">
                                <label for="envato_username" class="form__input__single__label">{{ __('Envato Username') }}</label>
                                <input type="text" class="form__control radius-5" name="envato_username" value="{{get_static_option('license_username')}}">
                            </div>
                            <div class="form__input__single">
                                <label for="envato_purchase_code" class="form__input__single__label">{{ __('Envato Purchase code') }}</label>
                                <input type="text" class="form__control radius-5" name="envato_purchase_code" value="{{get_static_option('license_purchase_code')}}">
                                <small class="text-dark">{{__('follow this article to know how you will get your envato purchase code for this script')}}
                                <a href="https://xgenious.com/where-can-i-find-my-purchase-code-at-codecanyon/" target="_blank" class="text-primary">{{__('how to get envato purchase code')}}</a>
                                </small>
                            </div>
                            <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                                <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                                <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Submit') }}</button>
                            </div>
                        </form>
                    </div>
            </div>
        </div>
     </div>
@endsection
