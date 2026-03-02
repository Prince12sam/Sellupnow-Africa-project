@extends('layouts.login-screens')
@section('content')
    <section class="loginForm">
        <div class="loginForm__left__inner desktop-center">
            <div class="loginForm__header">
                <h2 class="loginForm__header__title">{{ __('Forget Password') }}</h2>
                <p class="loginForm__header__para mt-3">{{ __('Hello there, here you can rest you password.') }} </p>
            </div>
            <x-validation.error/>
            <div class="loginForm__wrapper mt-4">
                <form action="{{ route('admin.forget.password') }}" class="custom_form" method="POST">
                    @csrf
                    <div class="single_input">
                        <label class="label_title">{{ __('Username or Email') }}</label>
                        <div class="include_icon">
                            <input class="form--control radius-5" type="text" id="username" name="username" placeholder="{{ __('Username or Email') }}">
                            <div class="icon"><span class="material-symbols-outlined">{{ __('person') }}</span></div>
                        </div>
                    </div>
                    <div class="btn_wrapper single_input">
                        <button type="submit" id="form_submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Send Reset Password Mail')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
