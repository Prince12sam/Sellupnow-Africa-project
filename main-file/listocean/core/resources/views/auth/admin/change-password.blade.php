@extends('backend.admin-master')
@section('site-title')
    {{__('Change Password')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Change Password') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="dashboard__inner__item">
                    <x-validation.error />
                    <form action="{{route('admin.profile.password.change')}}" class="custom_form" method="POST">
                        @csrf
                        <div class="single_input">
                            <label class="label_title">{{__('Old Password')}}</label>
                            <div class="include_icon">
                                <input class="form--control radius-5" type="password" id="old_password" name="old_password" placeholder="{{ __('Old Password') }}">
                                <div class="icon"><span class="material-symbols-outlined">{{ __('lock') }}</span></div>
                            </div>
                        </div>
                        <div class="single_input">
                            <label class="label_title">{{ __('New Password') }}</label>
                            <div class="include_icon">
                                <input class="form--control radius-5" type="password" name="password" id="password" placeholder="{{ __('New Password') }}">
                                <div class="icon"><span class="material-symbols-outlined">{{ __('lock') }}</span></div>
                            </div>
                        </div>
                        <div class="single_input">
                            <label class="label_title">{{ __('Confirm Password') }}</label>
                            <div class="include_icon">
                                <input class="form--control radius-5" type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('Confirm Password') }}">
                                <div class="icon"><span class="material-symbols-outlined">{{ __('lock') }}</span></div>
                            </div>
                        </div>

                        <div class="popup_contents__footer justify-content-start">
                            <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
