@extends('layouts.app')
@section('header-title', __('Add Notice'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-8 col-lg-9 mx-auto">
        <div class="mb-3">
            <a href="{{ route('admin.siteNotice.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to Notices') }}
            </a>
        </div>
        <form method="POST" action="{{ route('admin.siteNotice.store') }}">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Add New Notice') }}</h5></div>
                <div class="card-body">
                    @include('admin.site-notices._form')
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('Create Notice') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
