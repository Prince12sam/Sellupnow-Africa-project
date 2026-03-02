@extends('layouts.app')
@section('header-title', __('Login / Register Page Settings'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-9 mx-auto">
        {{-- Tab nav --}}
        <ul class="nav nav-tabs mb-3" id="pageSettingsTabs">
            <li class="nav-item"><a class="nav-link active" href="{{ route('admin.pageSettings.loginRegister') }}">{{ __('Login / Register') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingCreate') }}">{{ __('Listing Create') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingDetails') }}">{{ __('Listing Details') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.guestListing') }}">{{ __('Guest Listing') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.userPublicProfile') }}">{{ __('Public Profile') }}</a></li>
        </ul>

        <form method="POST" action="{{ route('admin.pageSettings.loginRegister') }}">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Login / Register Page') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Login Form Title') }}</label>
                            <input type="text" name="login_form_title" class="form-control" value="{{ old('login_form_title', $settings['login_form_title']) }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Register Page Title') }}</label>
                            <input type="text" name="register_page_title" class="form-control" value="{{ old('register_page_title', $settings['register_page_title']) }}">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label fw-semibold">{{ __('Register Page Description') }}</label>
                            <textarea name="register_page_description" rows="3" class="form-control">{{ old('register_page_description', $settings['register_page_description']) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Terms & Conditions Page Slug') }}</label>
                            <input type="text" name="select_terms_condition_page" class="form-control" value="{{ old('select_terms_condition_page', $settings['select_terms_condition_page']) }}" placeholder="terms-and-conditions">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('reCAPTCHA v2 Site Key') }}</label>
                            <input type="text" name="recaptcha_2_site_key" class="form-control" value="{{ old('recaptcha_2_site_key', $settings['recaptcha_2_site_key']) }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-semibold d-block">{{ __('Social Login') }}</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="register_page_social_login_show_hide" value="on"
                                    {{ ($settings['register_page_social_login_show_hide'] === 'on') ? 'checked' : '' }}>
                                <label class="form-check-label">{{ __('Show social login buttons') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
