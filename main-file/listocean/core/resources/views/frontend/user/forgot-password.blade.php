@extends('frontend.layout.master')
@section('site_title')
    {{ __('Forget Password') }}
@endsection
@section('content')
    <div class="loginArea section-padding2">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 login-Wrapper">
                    <h3 class="tittle mb-3"> {{__('Forgot Password!') }}</h3>
                    <x-validation.frontend-error/>
                    <form  method="post" action="{{ route('user.forgot.password') }}">
                        @csrf
                        <div class="col-lg-12">
                            <label class="infoTitle"> {{ __('Email') }} </label>
                            <div class="input-form input-form2">
                                <input type="text" name="email" placeholder="{{ __('Enter email') }}">
                            </div>
                        </div>
                        <div class="btn-wrapper mb-10">
                            <button type="submit" class="cmn-btn4 w-100">{{ __('Submit Now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
     </div>
@endsection
