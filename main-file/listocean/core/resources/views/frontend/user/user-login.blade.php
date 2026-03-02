@extends('frontend.layout.master')
@section('site_title', __('User Login'))
@section('content')
    <div class="loginArea section-padding2">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-lg-5 p-0 order-lg-1 order-1 loginLeft-img">
                    <div class="loginLeft-img">
                        <div class="login-cap">
                            <h3 class="tittle">{{ get_static_option('login_form_title') ?? __('Buy & sell anything') }}</h3>
                            <p class="pera">{{ get_static_option('register_page_description') ?? __('Buy or Sell your any items.') }}</p>
                        </div>
                        <div class="login-img">
                            {!! render_image_markup_by_attachment_id(get_static_option('register_page_image')) !!}
                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-7 order-lg-1 order-0 login-Wrapper">
                    <x-validation.frontend-error/>
                    <div class="error-message"></div>
                    <div class="row">
                        <form method="post" action="{{ route('user.login') }}">
                            @csrf
                            <div class="col-md-12">
                                <label class="infoTitle">{{ __('Email Or User Name') }}</label>
                                <div class="input-form">
                                    <input type="text" name="username" id="username" placeholder="{{ __('Email Or User Name') }}">
                                    <div class="icon"><i class="las la-envelope icon"></i></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="infoTitle"> {{ __('Password') }} </label>
                                <div class="input-form">
                                    <input type="password" name="password" id="password" placeholder="{{ __('Type Password') }}">
                                    <div class="icon"><i class="las la-lock icon"></i></div>
                                    <div class="icon toggle-password">
                                         <i class="las la-eye-slash"></i>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-sm-12">
                            <div class="passRemember mt-20">
                                <label class="checkWrap2">{{ __('Remember Me') }}
                                    <input class="effectBorder" name="remember"  type="checkbox" id="check15">
                                    <span class="checkmark"></span>
                                </label>
                                <!-- forgetPassword -->
                                <div class="forgetPassword mb-25">
                                    <a href="{{ route('user.forgot.password') }}" class="forgetPass">{{ __('Forgot Password?') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="btn-wrapper text-center">
                                <button id="signin_form" class="cmn-btn4 w-100">{{ get_static_option('login_page_button_title') ?? __('Login') }}</button>
                                @if(moduleExists('SMSGateway') && get_static_option('otp_login_status') || !empty(get_static_option('register_page_social_login_show_hide')) && get_static_option('enable_google_login') || get_static_option('enable_facebook_login'))
                                       <p class="font-weight-bold text-center mt-2 mb-2">{{__('or')}}</p>
                                @endif
                                @if(moduleExists('SMSGateway') && get_static_option('otp_login_status'))
                                    <a href="{{route('user.login.otp')}}" class="cmn-btn-outline4 w-100 mb-20">{{__('Login In with OTP')}}</a>
                                @endif
                                <!--social login -->
                                @if(!empty(get_static_option('register_page_social_login_show_hide')))
                                    @if(get_static_option('enable_google_login'))
                                        <a href="{{ route('login.google.redirect') }}" class="cmn-btn-outline4  mb-20 w-100">
                                            <img src="{{ asset('assets/frontend/img/icon/googleIocn.svg') }}" alt="images" class="icon"> {{ __('Register With Google') }}
                                        </a>
                                    @endif
                                    @if(get_static_option('enable_facebook_login'))
                                        <a href="{{ route('login.facebook.redirect') }}" class="cmn-btn-outline4 mb-20  w-100">
                                            <img src="{{ asset('assets/frontend/img/icon/fbIcon.svg') }}" alt="images" class="icon">{{ __('Register With Facebook') }}
                                        </a>
                                    @endif
                                @endif

                                <p class="sinUp"><span>{{ __('Don’t have an account?') }}</span><a href="{{ route('user.register') }}" class="singApp">{{ __('Sign Up') }}</a></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $(document).on('click','#signin_form',function (e){
                    e.preventDefault();
                    let el = $(this);
                    let erContainer = $(".error-message");
                    erContainer.html('');
                    el.text('{{__('Please Wait..')}}');
                    $.ajax({
                        url: "{{route('user.login')}}",
                        type: "POST",
                        data: {
                            username : $('#username').val(),
                            password : $('#password').val(),
                            remember : $('#remember').val(),
                        },
                        error:function(data){
                            var errors = data.responseJSON;
                            erContainer.html('<div class="alert alert-danger"></div>');
                            $.each(errors.errors, function(index,value){
                                erContainer.find('.alert.alert-danger').append('<p>'+value+'</p>');
                            });
                            el.text('{{__('Login')}}');
                        },
                        success:function (data){
                            $('.alert.alert-danger').remove();
                            if (data.status == 'user-login'){
                                el.text('{{__('Redirecting')}}..');
                                erContainer.html('<div class="alert alert-'+data.type+'">'+data.msg+'</div>');
                                let redirectPath = "{{route('user.dashboard')}}";
                                @if(!empty(request()->get('return')))
                                    redirectPath = "{{url('/'.request()->get('return'))}}";
                                @endif
                                if (redirectPath.endsWith('/dashboard') || redirectPath.endsWith('/admin/dashboard')) {
                                    redirectPath = "{{route('user.dashboard')}}";
                                }
                                    window.location = redirectPath;
                            }else{
                                erContainer.html('<div class="alert alert-'+data.type+'">'+data.msg+'</div>');
                                el.text('{{__('Login')}}');
                            }
                        }
                    });
                });
            });
        }(jQuery));
    </script>
@endsection



