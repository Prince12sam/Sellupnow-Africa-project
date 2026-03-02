@extends('layouts.app')
@section('header-title', __('User Public Profile Page Settings'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-9 mx-auto">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.loginRegister') }}">{{ __('Login / Register') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingCreate') }}">{{ __('Listing Create') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingDetails') }}">{{ __('Listing Details') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.guestListing') }}">{{ __('Guest Listing') }}</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('admin.pageSettings.userPublicProfile') }}">{{ __('Public Profile') }}</a></li>
        </ul>
        <form method="POST" action="{{ route('admin.pageSettings.userPublicProfile') }}">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('User Public Profile Page — Sidebar Advertisement') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Ad Type') }}</label>
                            <select name="user_public_profile_page_advertisement_type" class="form-select">
                                <option value="">{{ __('None') }}</option>
                                <option value="image" {{ $settings['user_public_profile_page_advertisement_type'] === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                                <option value="embed_code" {{ $settings['user_public_profile_page_advertisement_type'] === 'embed_code' ? 'selected' : '' }}>{{ __('Embed Code') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Ad Size') }}</label>
                            <input type="text" name="user_public_profile_page_advertisement_size" class="form-control"
                                value="{{ old('user_public_profile_page_advertisement_size', $settings['user_public_profile_page_advertisement_size']) }}" placeholder="e.g. 300x250">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Alignment') }}</label>
                            <select name="user_public_profile_page_advertisement_alignment" class="form-select">
                                <option value="left" {{ $settings['user_public_profile_page_advertisement_alignment'] === 'left' ? 'selected' : '' }}>{{ __('Left') }}</option>
                                <option value="center" {{ $settings['user_public_profile_page_advertisement_alignment'] === 'center' ? 'selected' : '' }}>{{ __('Center') }}</option>
                                <option value="right" {{ $settings['user_public_profile_page_advertisement_alignment'] === 'right' ? 'selected' : '' }}>{{ __('Right') }}</option>
                            </select>
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
