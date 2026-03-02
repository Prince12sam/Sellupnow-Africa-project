@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <div>
            <h4 class="mb-0">{{ __('Create Membership Plan') }}</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.membershipPlan.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-medium mb-1">{{ __('Please fix the errors below:') }}</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.membershipPlan.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Price') }}</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', '0') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Currency (ISO)') }}</label>
                            <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" placeholder="NGN">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Duration (days)') }}</label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', '30') }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Listing Quota') }} <small class="text-muted">(0 = unlimited)</small></label>
                            <input type="number" name="listing_quota" class="form-control" value="{{ old('listing_quota', '0') }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Auto-Featured Listings') }}</label>
                            <input type="number" name="auto_feature_count" class="form-control" value="{{ old('auto_feature_count', '0') }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                {{ __('Video Quota') }}
                                <small class="text-muted d-block">0 = none &nbsp;|&nbsp; -1 = unlimited &nbsp;|&nbsp; N = max N listings</small>
                            </label>
                            <input type="number" name="video_quota" class="form-control" value="{{ old('video_quota', '0') }}" min="-1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                {{ __('Banner Ad Quota') }}
                                <small class="text-muted d-block">0 = none &nbsp;|&nbsp; -1 = unlimited &nbsp;|&nbsp; N = max N requests</small>
                            </label>
                            <input type="number" name="banner_ad_quota" class="form-control" value="{{ old('banner_ad_quota', '0') }}" min="-1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Badge Label') }} <small class="text-muted">(e.g. Most Popular)</small></label>
                            <input type="text" name="badge_label" class="form-control" value="{{ old('badge_label') }}" placeholder="Most Popular">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">{{ __('Badge Color') }}</label>
                            <input type="color" name="badge_color" class="form-control form-control-color" value="{{ old('badge_color', '#f97316') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', '0') }}" min="0">
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">{{ __('Features') }}</label>
                        <div class="row">
                            @foreach(config('membership.features') as $key => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]" id="feature_{{ $key }}" value="{{ $key }}" {{ in_array($key, old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_{{ $key }}">{{ __($label) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('Create Plan') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
