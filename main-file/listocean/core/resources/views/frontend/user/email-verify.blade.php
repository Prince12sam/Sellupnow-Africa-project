@extends('frontend.layout.master')
@section('site_title')
    {{ __('Email Verify') }}
@endsection
@section('content')
    <div class="loginArea section-padding2">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 login-Wrapper">
                   <div class="text-center mb-3">
                       <h3 class="tittle">{{ __('Verify Your Account') }}</h3>
                       <div class="alert alert-info alert-bs-dismissible fade show mt-5 mb-1 mx-auto d-inline-block" role="alert">
                           {{ __('Please check email inbox/spam for verification code') }}
                       </div>
                   </div>
                    <x-validation.frontend-error />
                    <form action="{{ route('email.verify') }}" method="post">
                        @csrf
                        <div class="row">
                          <div class="col-12">
                              <label class="infoTitle">{{ __('First Name') }}</label>
                              <div class="input-form input-form2">
                                  <input type="text" name="email_verify_token" placeholder="{{ __('Enter code') }}">
                              </div>
                          </div>
                       </div>
                        <div class="col-12">
                            <div class="btn-wrapper text-center mt-50">
                                <button type="submit" class="cmn-btn4 w-100 mb-60 verify-account">{{ __('Verify Account') }}</button>
                            </div>
                        </div>
                    </form>
                    <!--Reset code -->
                    <div class="resend-verify-code-wrap mt-3 text-center">
                        <a  class="text-center" href="{{ route('resend.verify.code') }}"><strong>{{ __('Resend Code') }}</strong></a>
                    </div>
                </div>
           </div>
        </div>
    </div>
@endsection
