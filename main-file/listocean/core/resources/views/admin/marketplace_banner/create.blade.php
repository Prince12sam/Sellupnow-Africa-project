@extends('layouts.app')

@section('header-title', __('Create Marketplace Banner'))
@section('header-subtitle', __('Upload a banner that appears under app badges on marketplace'))

@section('content')
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.marketplaceBanner.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Title') }}</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Redirect URL') }}</label>
                        <input type="url" name="redirect_url" class="form-control" value="{{ old('redirect_url') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Image') }}</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="status" value="1" class="form-check-input" id="status">
                        <label for="status" class="form-check-label">{{ __('Active') }}</label>
                    </div>

                    <button class="btn btn-primary">{{ __('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
