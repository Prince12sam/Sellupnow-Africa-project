@extends('layouts.app')
@section('header-title', __('Listing Details Page Settings'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-9 mx-auto">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.loginRegister') }}">{{ __('Login / Register') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.listingCreate') }}">{{ __('Listing Create') }}</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('admin.pageSettings.listingDetails') }}">{{ __('Listing Details') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.guestListing') }}">{{ __('Guest Listing') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pageSettings.userPublicProfile') }}">{{ __('Public Profile') }}</a></li>
        </ul>
        <form method="POST" action="{{ route('admin.pageSettings.listingDetails') }}">
            @csrf
            <div class="card mb-4">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Listing Details Page — Labels') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        @foreach([
                            ['listing_default_phone_number_title', 'Default Phone Number Title'],
                            ['listing_phone_number_show_hide_button_title', 'Show/Hide Phone Button Title'],
                            ['listing_report_button_title', 'Report Button Title'],
                            ['listing_share_button_title', 'Share Button Title'],
                            ['listing_show_phone_number_title', 'Show Phone Number Title'],
                            ['listing_safety_tips_title', 'Safety Tips Title'],
                            ['listing_location_title', 'Location Section Title'],
                            ['listing_description_title', 'Description Section Title'],
                            ['listing_tag_title', 'Tags Section Title'],
                            ['listing_relevant_title', 'Related Listings Title'],
                        ] as [$key, $label])
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">{{ __($label) }}</label>
                            <input type="text" name="{{ $key }}" class="form-control" value="{{ old($key, $settings[$key]) }}">
                        </div>
                        @endforeach
                        <div class="col-12 mb-2">
                            <label class="form-label fw-semibold">{{ __('Safety Tips Text') }}</label>
                            <textarea name="safety_tips_info" rows="4" class="form-control">{{ old('safety_tips_info', $settings['safety_tips_info']) }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Safety Tips Popup Color') }}</label>
                            <input type="text" name="safety_tips_color" class="form-control" value="{{ old('safety_tips_color', $settings['safety_tips_color'] ?? '') }}" placeholder="#fff8e1 or rgba(255,247,237,1)">
                            <small class="form-text text-muted">Hex or rgba background color for the safety popup.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Left Sidebar Advertisement') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Ad Type') }}</label>
                            <select name="left_listing_details_page_advertisement_type" class="form-select">
                                <option value="">{{ __('None') }}</option>
                                <option value="image" {{ $settings['left_listing_details_page_advertisement_type'] === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                                <option value="embed_code" {{ $settings['left_listing_details_page_advertisement_type'] === 'embed_code' ? 'selected' : '' }}>{{ __('Embed Code') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Ad Size') }}</label>
                            <input type="text" name="left_listing_details_page_advertisement_size" class="form-control" value="{{ old('left_listing_details_page_advertisement_size', $settings['left_listing_details_page_advertisement_size']) }}" placeholder="e.g. 300x250">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Alignment') }}</label>
                            <select name="left_listing_details_page_advertisement_alignment" class="form-select">
                                <option value="left" {{ $settings['left_listing_details_page_advertisement_alignment'] === 'left' ? 'selected' : '' }}>{{ __('Left') }}</option>
                                <option value="center" {{ $settings['left_listing_details_page_advertisement_alignment'] === 'center' ? 'selected' : '' }}>{{ __('Center') }}</option>
                                <option value="right" {{ $settings['left_listing_details_page_advertisement_alignment'] === 'right' ? 'selected' : '' }}>{{ __('Right') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Right Sidebar Advertisement') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Ad Type') }}</label>
                            <select name="right_listing_details_page_advertisement_type" class="form-select">
                                <option value="">{{ __('None') }}</option>
                                <option value="image" {{ $settings['right_listing_details_page_advertisement_type'] === 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                                <option value="embed_code" {{ $settings['right_listing_details_page_advertisement_type'] === 'embed_code' ? 'selected' : '' }}>{{ __('Embed Code') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Ad Size') }}</label>
                            <input type="text" name="right_listing_details_page_advertisement_size" class="form-control" value="{{ old('right_listing_details_page_advertisement_size', $settings['right_listing_details_page_advertisement_size']) }}" placeholder="e.g. 300x250">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Alignment') }}</label>
                            <select name="right_listing_details_page_advertisement_alignment" class="form-select">
                                <option value="left" {{ $settings['right_listing_details_page_advertisement_alignment'] === 'left' ? 'selected' : '' }}>{{ __('Left') }}</option>
                                <option value="center" {{ $settings['right_listing_details_page_advertisement_alignment'] === 'center' ? 'selected' : '' }}>{{ __('Center') }}</option>
                                <option value="right" {{ $settings['right_listing_details_page_advertisement_alignment'] === 'right' ? 'selected' : '' }}>{{ __('Right') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-2">
                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
