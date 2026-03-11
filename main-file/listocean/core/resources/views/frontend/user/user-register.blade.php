@extends('frontend.layout.master')
@section('site_title')
 {{ __('User Register') }}
@endsection
@section('style')
    <style>
        .loginArea .login-Wrapper .input-form.input-form2 input {
            padding: 8px 0 6px 56px;
        }
        span#phone_availability {
            font-size: 13px;
        }
    </style>
@endsection
@section('content')
    <div class="loginArea section-padding2">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-lg-5 p-0  order-lg-1 order-1 loginLeft-img">
                    <div class="loginLeft-img">
                        <div class="login-cap">
                            <h3 class="tittle">{{ get_static_option('register_page_title') ?? __('Register') }}</h3>
                            <p class="pera">{{ get_static_option('register_page_description') ?? __('Buy or Sell any items.') }}</p>
                        </div>
                        <div class="login-img">
                            {!! render_image_markup_by_attachment_id(get_static_option('register_page_image')) !!}
                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-7 order-lg-1 order-0 login-Wrapper">

                    @if(!empty(get_static_option('site_google_captcha_enable')))
                        @if(get_static_option('captcha_provider') == 'cloudflare')
                            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                        @else
                            <script src='https://www.google.com/recaptcha/api.js'></script>
                        @endif
                    @endif

                    <x-validation.frontend-error/>
                    <form action="{{ route('user.register') }}" method="post">
                        @csrf
                    <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <label class="infoTitle">{{ __('Your Name') }}</label>
                                <div class="input-form input-form2">
                                    <input type="text" class="ps-3" name="name" value="{{old('name')}}" id="name" placeholder="{{ __('Your Name') }}">
                                    <div class="icon"><i class="las la-user icon"></i></div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <label class="infoTitle">{{ __('Email') }}</label>
                                <div class="input-form input-form2">
                                    <input type="email" name="email" id="email" value="{{old('email')}}" placeholder="{{__('Your Email')}}">
                                    <div class="icon"><i class="lar la-envelope icon"></i></div>
                                </div>
                                <span id="email_availability"></span>
                            </div>

                            <div class="col-lg-12 col-md-12 mt-2">
                                <label class="infoTitle">{{ __('Password') }}</label>
                                <div class="input-form">
                                    <input type="password" name="password" id="password" placeholder="{{ __('Password') }}">
                                    <div class="icon"><i class="las la-lock icon"></i></div>
                                    <div class="icon toggle-password">
                                       <i class="las la-eye"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="col-lg-12 col-md-12">
                                <label class="checkWrap2 terms-conditions"> {{ __('I agree with the') }}
                                    <a href="{{ url('/'.get_static_option('select_terms_condition_page')) }}" target="_blank" class="text-primary"> {{ __('Terms and Conditions') }} </a>
                                    <input class="effectBorder check-input" type="checkbox" name="terms_conditions" id="terms_conditions" value="1">
                                    <span class="checkmark"></span>
                                </label>
                            </div>

                        @if(!empty(get_static_option('site_google_captcha_enable')))
                            <div class="col-md-12 my-3">
                                @if(get_static_option('captcha_provider') == 'cloudflare')
                                    <div class="cf-turnstile" data-sitekey="{{ get_static_option('cloudflare_turnstile_site_key') }}" data-appearance="always" data-theme="light" data-size="normal"></div>
                                    @if ($errors->has('cf-turnstile-response'))
                                        <span class="text-danger">{{ $errors->first('cf-turnstile-response') }}</span>
                                    @endif
                                @else
                                    <div class="g-recaptcha" data-sitekey="{{ get_static_option('recaptcha_2_site_key')}}"></div>
                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                                    @endif
                                @endif
                            </div>
                        @endif

                            <div class="col-sm-12 mt-2">
                                <div class="btn-wrapper text-center">
                                    <button type="submit" class="cmn-btn4 w-100 user-register-form sign_up_now_button">{{ __('Register') }}
                                        <span id="user_register_load_spinner"></span>
                                    </button>
                                    <!--social login -->
                                    @if(!empty(get_static_option('register_page_social_login_show_hide')))
                                        @if(get_static_option('enable_google_login') || get_static_option('enable_facebook_login'))
                                            <div class="bar-wrap">
                                                <span class="bar"></span>
                                                <p class="or">{{ __('or') }}</p>
                                                <span class="bar"></span>
                                            </div>
                                        @endif

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

                                    <p class="sinUp">
                                        <span>{{ __('Already have an account?') }} </span>
                                        <a href="{{ route('user.login') }}" class="singApp">{{ __('Login') }}</a>
                                    </p>

                                </div>
                            </div>
                         </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End-of login Area -->
@endsection
@section('scripts')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $(document).on('keyup', '#email', function() {
                    let email = $(this).val();
                    let emailRegex = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
                    if (emailRegex.test(email) && email != '') {
                        $.ajax({
                            url: "{{ route('user.email.availability') }}",
                            type: 'post',
                            data: {
                                email: email
                            },
                            success: function(res) {
                                if (res.status == 'available') {
                                    $("#email_availability").html(
                                        "<span style='color: green;'>" + res.msg +
                                        "</span>");
                                } else {
                                    $("#email_availability").html(
                                        "<span style='color: red;'>" + res.msg +
                                        "</span>");
                                }
                            }
                        });
                    } else {
                        $("#email_availability").html(
                            "<span style='color: red;'>{{ __('Enter valid email') }}</span>");
                    }
                });

                //confirm signup
                $(document).on('click', '.sign_up_now_button', function() {

                    let name     = $('#name').val();
                    let email    = $('#email').val();
                    let password = $('#password').val();

                    let email_validation_text = $('#email_availability span').text();

                    if(name == '' || email == '' || password == ''){
                        toastr_warning_js("{{ __('Please fill all fields') }}")
                        return false
                    }else if(email_validation_text == 'Sorry! Email has already taken' || email_validation_text == 'Enter valid email'){
                        toastr_warning_js("{{ __('Please enter a valid email') }}")
                        return false
                    }else if(password.length < 6){
                        toastr_warning_js("{{ __('Password must be 6 character at least') }}")
                        return false
                    }


                    // terms and condition check
                    if (!$('.terms-conditions .check-input').is(":checked")) {
                        toastr_warning_js("{{ __('Please agree with terms and conditions') }}")
                        return false;
                    }

                    @if(!empty(get_static_option('site_google_captcha_enable')))
                    @if(get_static_option('captcha_provider') == 'cloudflare')
                    // Turnstile captcha check
                    var turnstileResponse = $('[name="cf-turnstile-response"]').val();
                    if (!turnstileResponse) {
                        toastr_warning_js("{{ __('Please complete the CAPTCHA verification') }}")
                        return false;
                    }
                    @endif
                    @endif

                    $(this).attr("disabled", "disabled");
                    $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{__("Registering")}}');
                  // Submit the form
                    $(this).closest('form').trigger('submit');

                });

            });
        }(jQuery));
    </script>
@endsection
