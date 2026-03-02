@extends('layouts.app')
@section('header-title', __('Guest — Approve Listing Template'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-8 col-lg-9 mx-auto">
        <div class="mb-3"><a href="{{ route('admin.emailTemplate.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back') }}</a></div>
        <form method="POST" action="{{ route('admin.emailTemplate.guestApproveListing') }}">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Guest — Approve Listing Email') }}</h5></div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('Subject') }}</label>
                        <input type="text" name="guest_listing_approve_subject" class="form-control @error('guest_listing_approve_subject') is-invalid @enderror" value="{{ old('guest_listing_approve_subject', $subject) }}" required>
                        @error('guest_listing_approve_subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">{{ __('Message') }}</label>
                        <textarea name="guest_listing_approve_message" rows="8" class="form-control @error('guest_listing_approve_message') is-invalid @enderror" required>{{ old('guest_listing_approve_message', $message) }}</textarea>
                        @error('guest_listing_approve_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
