@extends('layouts.app')

@section('header-title', __('Edit Marketplace Banner'))
@section('header-subtitle', __('Update marketplace banner under app badges'))

@section('content')
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.marketplaceBanner.update', $banner->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">{{ __('Title') }}</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Redirect URL') }}</label>
                        <input type="url" name="redirect_url" class="form-control" value="{{ old('redirect_url', $banner->redirect_url) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Image') }}</label>
                        <input type="file" name="image" class="form-control">
                        @if($banner->image_path)
                            <div class="mt-2"><img src="{{ asset('storage/' . $banner->image_path) }}" height="76" alt="current" /></div>
                        @endif
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="status" value="1" class="form-check-input" id="status" {{ $banner->status ? 'checked' : '' }}>
                        <label for="status" class="form-check-label">{{ __('Active') }}</label>
                    </div>

                    <button class="btn btn-primary">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
