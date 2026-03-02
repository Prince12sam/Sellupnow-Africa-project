@extends('layouts.app')
@section('header-title', __('Guest Listing Page Settings'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-9 mx-auto">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.loginRegister') }}">{{ __('Login / Register') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingCreate') }}">{{ __('Listing Create') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingDetails') }}">{{ __('Listing Details') }}</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('admin.pageSettings.guestListing') }}">{{ __('Guest Listing') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.userPublicProfile') }}">{{ __('Public Profile') }}</a></li>
        </ul>
        <form method="POST" action="{{ route('admin.pageSettings.guestListing') }}">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Guest Listing Page') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Guest Listing') }}</label>
                            <select name="guest_listing_allowed_disallowed" class="form-select">
                                <option value="allowed" {{ $settings['guest_listing_allowed_disallowed'] === 'allowed' ? 'selected' : '' }}>{{ __('Allowed') }}</option>
                                <option value="disallowed" {{ $settings['guest_listing_allowed_disallowed'] === 'disallowed' ? 'selected' : '' }}>{{ __('Disallowed') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Max Gallery Images') }}</label>
                            <input type="number" name="guest_listing_gallery_image_upload_limit" class="form-control"
                                value="{{ old('guest_listing_gallery_image_upload_limit', $settings['guest_listing_gallery_image_upload_limit']) }}" min="1" max="50">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Listing Expiry (days)') }}</label>
                            <input type="number" name="guest_listing_expire_limit" class="form-control"
                                value="{{ old('guest_listing_expire_limit', $settings['guest_listing_expire_limit']) }}" min="1">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Info Section Title') }}</label>
                            <input type="text" name="guest_add_listing_info_section_title" class="form-control"
                                value="{{ old('guest_add_listing_info_section_title', $settings['guest_add_listing_info_section_title']) }}">
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label fw-semibold">{{ __('Registration Agreement Title') }}</label>
                            <input type="text" name="guest_registration_agreement_title" class="form-control"
                                value="{{ old('guest_registration_agreement_title', $settings['guest_registration_agreement_title']) }}">
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
