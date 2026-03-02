@extends('layouts.app')

@section('header-title', __('All Listings'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('All Listings') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Filter Listings') }}</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.listingModeration.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <select class="form-control" name="queue">
                            <option value="all" {{ (request('queue', $queue ?? 'all') === 'all') ? 'selected' : '' }}>{{ __('All Listings') }}</option>
                            <option value="new" {{ (request('queue', $queue ?? 'all') === 'new') ? 'selected' : '' }}>{{ __('New Listing Request') }}</option>
                            <option value="update" {{ (request('queue', $queue ?? 'all') === 'update') ? 'selected' : '' }}>{{ __('Update Listing Request') }}</option>
                            <option value="removed" {{ (request('queue', $queue ?? 'all') === 'removed') ? 'selected' : '' }}>{{ __('Removed Listings') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="{{ __('Search by title, slug, description') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="status">
                            <option value="">{{ __('All Active Status') }}</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="is_published">
                            <option value="">{{ __('All Publish Status') }}</option>
                            <option value="1" {{ request('is_published') === '1' ? 'selected' : '' }}>{{ __('Published') }}</option>
                            <option value="0" {{ request('is_published') === '0' ? 'selected' : '' }}>{{ __('Unpublished') }}</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.listingModeration.index') }}" class="btn btn-secondary w-100">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Listing List') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Listing') }}</th>
                                <th>{{ __('Owner') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Reports') }}</th>
                                <th>{{ __('Active') }}</th>
                                <th>{{ __('Published') }}</th>
                                <th>{{ __('Featured') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listings as $key => $listing)
                                @php $serial = $listings->firstItem() + $key; @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>
                                        <div>#{{ $listing->id }} - {{ $listing->title }}</div>
                                        <small class="text-muted">{{ $listing->slug }}</small>
                                    </td>
                                    <td>{{ $listing->user?->name ?? __('N/A') }}</td>
                                    <td>{{ $listing->category?->name ?? __('N/A') }}</td>
                                    <td>{{ $listing->price }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $listing->reports_count }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($listing->deleted_at))
                                            <span class="badge bg-danger">{{ __('Removed') }}</span>
                                        @else
                                            @hasPermission('admin.listingModeration.status')
                                                <form action="{{ route('admin.listingModeration.status', $listing->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="{{ $listing->status ? 0 : 1 }}">
                                                    <button type="submit" class="btn btn-sm {{ $listing->status ? 'btn-success' : 'btn-secondary' }}">
                                                        {{ $listing->status ? __('Active') : __('Inactive') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge {{ $listing->status ? 'bg-success' : 'bg-secondary' }}">{{ $listing->status ? __('Active') : __('Inactive') }}</span>
                                            @endhasPermission
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($listing->deleted_at))
                                            <span class="text-muted">--</span>
                                        @else
                                            @hasPermission('admin.listingModeration.publish')
                                                <form action="{{ route('admin.listingModeration.publish', $listing->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="is_published" value="{{ $listing->is_published ? 0 : 1 }}">
                                                    <button type="submit" class="btn btn-sm {{ $listing->is_published ? 'btn-primary' : 'btn-warning' }}">
                                                        {{ $listing->is_published ? __('Published') : __('Unpublished') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge {{ $listing->is_published ? 'bg-primary' : 'bg-warning text-dark' }}">{{ $listing->is_published ? __('Published') : __('Unpublished') }}</span>
                                            @endhasPermission
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($listing->deleted_at))
                                            <span class="text-muted">--</span>
                                        @else
                                            @hasPermission('admin.listingModeration.featured')
                                                <form action="{{ route('admin.listingModeration.featured', $listing->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="is_featured" value="{{ !empty($listing->is_featured) ? 0 : 1 }}">
                                                    <button type="submit" class="btn btn-sm {{ !empty($listing->is_featured) ? 'btn-info' : 'btn-outline-secondary' }}">
                                                        {{ !empty($listing->is_featured) ? __('Featured') : __('Not Featured') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge {{ !empty($listing->is_featured) ? 'bg-info text-dark' : 'bg-secondary' }}">
                                                    {{ !empty($listing->is_featured) ? __('Featured') : __('No') }}
                                                </span>
                                            @endhasPermission
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            @hasPermission('admin.listingModeration.show')
                                                <a href="{{ route('admin.listingModeration.show', $listing->id) }}"
                                                   class="btn btn-outline-primary btn-sm circleIcon" title="{{ __('View') }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endhasPermission
                                            @if(empty($listing->deleted_at))
                                                @hasPermission('admin.listingModeration.delete')
                                                    <a href="{{ route('admin.listingModeration.delete', $listing->id) }}"
                                                       class="btn btn-outline-danger btn-sm deleteConfirmAlert circleIcon" title="{{ __('Remove') }}">
                                                        <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="icon" loading="lazy" />
                                                    </a>
                                                @endhasPermission
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $listings->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.deleteConfirmAlert').forEach((element) => {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __('You will not be able to revert this!') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, remove it!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
@endpush
