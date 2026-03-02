@extends('layouts.app')

@section('header-title', __('Marketplace Banner'))
@section('header-subtitle', __('Manage the marketplace banner under app badges'))

@section('content')
    <div class="container-fluid mt-3">
        <div class="my-3 card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    @if(!empty($banner) && $banner->image_path)
                        <img src="{{ asset('storage/' . $banner->image_path) }}" height="76" alt="Marketplace banner" />
                        <div>
                            <div class="fw-semibold">{{ $banner->title ?: __('Marketplace Banner') }}</div>
                            <div class="text-muted small">{{ $banner->status ? __('Active') : __('Inactive') }}</div>
                        </div>
                    @else
                        <div>
                            <div class="fw-semibold">{{ __('Marketplace — under app badges') }}</div>
                            <div class="text-muted small">{{ __('No banner configured') }}</div>
                        </div>
                    @endif
                </div>

                <div>
                    @hasPermission('admin.banner.create')
                        @if(empty($banner))
                            <a href="{{ route('admin.marketplaceBanner.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
                        @endif
                    @endhasPermission

                    @hasPermission('admin.banner.edit')
                        @if(!empty($banner))
                            <a href="{{ route('admin.marketplaceBanner.edit', $banner->id) }}" class="btn btn-outline-info">{{ __('Edit') }}</a>
                        @endif
                    @endhasPermission

                    @hasPermission('admin.banner.destroy')
                        @if(!empty($banner))
                            <a href="{{ route('admin.marketplaceBanner.destroy', $banner->id) }}" class="btn btn-outline-danger deleteConfirm">{{ __('Delete') }}</a>
                        @endif
                    @endhasPermission
                </div>
            </div>
        </div>
    </div>
@endsection
