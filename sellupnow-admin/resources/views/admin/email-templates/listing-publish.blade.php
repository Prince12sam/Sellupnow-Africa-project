@extends('layouts.app')
@section('header-title', __('Listing Publish Template'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-8 col-lg-9 mx-auto">
        <div class="mb-3"><a href="{{ route('admin.emailTemplate.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back') }}</a></div>
        <form method="POST" action="{{ route('admin.emailTemplate.listingPublish') }}">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Listing Publish Email') }}</h5></div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('Subject') }}</label>
                        <input type="text" name="listing_publish_subject" class="form-control @error('listing_publish_subject') is-invalid @enderror" value="{{ old('listing_publish_subject', $subject) }}" required>
                        @error('listing_publish_subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">{{ __('Message') }}</label>
                        <textarea name="listing_publish_message" rows="8" class="form-control @error('listing_publish_message') is-invalid @enderror" required>{{ old('listing_publish_message', $message) }}</textarea>
                        @error('listing_publish_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
