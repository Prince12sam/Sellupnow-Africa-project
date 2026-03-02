@extends('layouts.app')

@section('header-title', __('Edit Featured Ad Package'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Edit Featured Ad Package') }}</h4>
        <a href="{{ route('admin.featuredAdPackage.index') }}" class="btn btn-secondary">
            &larr; {{ __('Back') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.featuredAdPackage.update', $package->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('admin.listocean-featured-ad-packages._form', ['pkg' => $package])
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
