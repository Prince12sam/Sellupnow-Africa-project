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
                <form action="{{ route('admin.reset.password.change') }}" class="custom_form" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{$token}}">

                    <div class="single_input">
                        <label class="label_title">{{ __('Username') }}</label>
                        <div class="include_icon">
                            <input class="form--control radius-5" type="text" id="username" readonly value="{{$username}}" name="username">
                            <div class="icon">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                        </div>
                    </div>

                    <div class="single_input">
                        <label class="label_title">{{ __('Password') }}</label>
                        <div class="include_icon">
                            <input class="form--control radius-5" type="password" id="password" name="password">
                            <div class="icon">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                        </div>
                    </div>

                    <div class="single_input">
                        <label class="label_title">{{ __('Confirm Password') }}</label>
                        <div class="include_icon">
                            <input class="form--control radius-5" type="password" id="password_confirmation" name="password_confirmation">
                            <div class="icon">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                        </div>
                    </div>

                    <div class="btn_wrapper single_input">
                        <button type="submit" id="form_submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Reset Password')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
