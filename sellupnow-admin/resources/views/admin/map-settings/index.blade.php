@extends('layouts.app')

@section('header-title', __('Map Settings'))

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-xl-8 col-lg-9 mx-auto">
            <form action="{{ route('admin.mapSettings.update') }}" method="POST">
                @csrf
                @method('put')
                <div class="card">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0">{{ __('Google Map Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label class="form-label fw-semibold">{{ __('Enable Google Map') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="google_map_settings_on_off"
                                        value="on" {{ ($settings['google_map_settings_on_off'] === 'on') ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ __('On / Off') }}</label>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <label class="form-label fw-semibold">{{ __('Google Map API Key') }}</label>
                                <input type="text" class="form-control @error('google_map_api_key') is-invalid @enderror"
                                    name="google_map_api_key"
                                    value="{{ old('google_map_api_key', $settings['google_map_api_key']) }}"
                                    placeholder="AIzaSy...">
                                @error('google_map_api_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">{{ __('Obtain this from Google Cloud Console → APIs & Services → Credentials.') }}</small>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">{{ __('Search Placeholder Text') }}</label>
                                <input type="text" class="form-control"
                                    name="google_map_search_placeholder_title"
                                    value="{{ old('google_map_search_placeholder_title', $settings['google_map_search_placeholder_title']) }}"
                                    placeholder="{{ __('Search by location') }}">
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">{{ __('Search Button Text') }}</label>
                                <input type="text" class="form-control"
                                    name="google_map_search_button_title"
                                    value="{{ old('google_map_search_button_title', $settings['google_map_search_button_title']) }}"
                                    placeholder="{{ __('Search') }}">
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
</div>
@endsection
