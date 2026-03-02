@extends('layouts.app')
@section('header-title', __('Listing Create Page Settings'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-9 mx-auto">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.loginRegister') }}">{{ __('Login / Register') }}</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('admin.pageSettings.listingCreate') }}">{{ __('Listing Create') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingDetails') }}">{{ __('Listing Details') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.guestListing') }}">{{ __('Guest Listing') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.userPublicProfile') }}">{{ __('Public Profile') }}</a></li>
        </ul>
        <form method="POST" action="{{ route('admin.pageSettings.listingCreate') }}">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Listing Create Page') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('Listing Create Setting') }}</label>
                            <select name="listing_create_settings" class="form-select">
                                <option value="login_required" {{ $settings['listing_create_settings'] === 'login_required' ? 'selected' : '' }}>{{ __('Login Required') }}</option>
                                <option value="guest_allowed" {{ $settings['listing_create_settings'] === 'guest_allowed' ? 'selected' : '' }}>{{ __('Guest Allowed') }}</option>
                            </select>
                            <small class="text-muted">{{ __('Controls whether a guest can submit a listing without registering.') }}</small>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __('New Listing Status') }}</label>
                            <select name="listing_create_status_settings" class="form-select">
                                <option value="publish" {{ $settings['listing_create_status_settings'] === 'publish' ? 'selected' : '' }}>{{ __('Auto Publish') }}</option>
                                <option value="pending" {{ $settings['listing_create_status_settings'] === 'pending' ? 'selected' : '' }}>{{ __('Pending Approval') }}</option>
                            </select>
                            <small class="text-muted">{{ __('Whether new listings go live immediately or wait for admin approval.') }}</small>
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
