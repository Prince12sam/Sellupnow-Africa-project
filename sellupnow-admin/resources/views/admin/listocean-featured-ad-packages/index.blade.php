@extends('layouts.app')

@section('header-title', __('Featured Ad Packages'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Featured Ad Packages') }}</h4>
        <a href="{{ route('admin.featuredAdPackage.create') }}" class="btn btn-primary">
            {{ __('+ New Package') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(!$hasTable)
            <div class="alert alert-warning">
                {{ __('The featured_ad_packages table does not exist in the ListOcean database yet. Run the migration first.') }}
            </div>
        @else
            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">{{ __('Packages') }}</h5>
                    <small class="text-muted">{{ $packages->count() }} {{ __('total') }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('Ad Limit') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end pe-3">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $pkg)
                                    <tr>
                                        <td class="ps-3">{{ $pkg->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $pkg->name }}</div>
                                            @if(!empty($pkg->description))
                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($pkg->description, 60) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ number_format((float)$pkg->price, 2) }} {{ $pkg->currency ?? '' }}</td>
                                        <td>{{ $pkg->duration_days }} {{ __('days') }}</td>
                                        <td>{{ number_format((int)$pkg->advertisement_limit) }}</td>
                                        <td>
                                            @if($pkg->is_active)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.featuredAdPackage.edit', $pkg->id) }}"
                                               class="btn btn-outline-primary btn-sm me-1">
                                                {{ __('Edit') }}
                                            </a>
                                            <a href="{{ route('admin.featuredAdPackage.destroy', $pkg->id) }}"
                                               class="btn btn-outline-danger btn-sm js-confirm-delete">
                                                {{ __('Delete') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            {{ __('No packages yet. Create one to let users promote their listings.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-confirm-delete').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.href;
        Swal.fire({
            title: '{{ __('Delete package?') }}',
            text: '{{ __('This cannot be undone.') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __('Yes, delete') }}',
            cancelButtonText: '{{ __('Cancel') }}',
            confirmButtonColor: '#d33',
        }).then(function(result) {
            if (result.isConfirmed) window.location.href = url;
        });
    });
});
</script>
@endpush
