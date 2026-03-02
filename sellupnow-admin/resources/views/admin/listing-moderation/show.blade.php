@extends('layouts.app')

@section('header-title', __('Listing Moderation Details'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Listing Moderation Details') }}</h4>
        <a href="{{ route('admin.listingModeration.index') }}" class="btn py-2.5 btn-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card mb-3">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Listing Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div><strong>{{ __('Listing ID') }}:</strong> #{{ $listing->id }}</div>
                        <div><strong>{{ __('Title') }}:</strong> {{ $listing->title }}</div>
                        <div><strong>{{ __('Slug') }}:</strong> {{ $listing->slug }}</div>
                        <div><strong>{{ __('Category') }}:</strong> {{ $listing->category?->name ?? __('N/A') }}</div>
                        <div><strong>{{ __('Sub Category') }}:</strong> {{ $listing->subCategory?->name ?? __('N/A') }}</div>
                        <div><strong>{{ __('Child Category') }}:</strong> {{ $listing->childCategory?->name ?? __('N/A') }}</div>
                        <div><strong>{{ __('Country') }}:</strong> {{ $listing->country?->name ?? __('N/A') }}</div>
                        <div><strong>{{ __('Price') }}:</strong> {{ $listing->price }}</div>
                        <div><strong>{{ __('Phone') }}:</strong> {{ $listing->phone ?? __('N/A') }}</div>
                        <div><strong>{{ __('Address') }}:</strong> {{ $listing->address ?? __('N/A') }}</div>
                        <div><strong>{{ __('Owner') }}:</strong> {{ $listing->user?->name ?? __('N/A') }}</div>
                        <div><strong>{{ __('Favorites') }}:</strong> {{ $listing->favorites_count }}</div>
                        <div><strong>{{ __('Reports') }}:</strong> {{ $listing->reports_count }}</div>
                        <div><strong>{{ __('Created At') }}:</strong> {{ $listing->created_at?->format('d M Y h:i A') }}</div>
                        <div><strong>{{ __('Published At') }}:</strong> {{ $listing->published_at?->format('d M Y h:i A') ?? __('N/A') }}</div>
                    </div>
                    <div class="col-md-4">
                        <img src="{{ $listing->thumbnail }}" alt="listing" class="img-fluid rounded" loading="lazy">
                    </div>
                </div>
                <hr>
                <h6>{{ __('Description') }}</h6>
                <p class="mb-0">{{ $listing->description ?? __('No description') }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Moderation Actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    @hasPermission('admin.listingModeration.status')
                        <form action="{{ route('admin.listingModeration.status', $listing->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="{{ $listing->status ? 0 : 1 }}">
                            <button type="submit" class="btn {{ $listing->status ? 'btn-success' : 'btn-secondary' }}">
                                {{ $listing->status ? __('Set Inactive') : __('Set Active') }}
                            </button>
                        </form>
                    @endhasPermission

                    @hasPermission('admin.listingModeration.featured')
                        <form action="{{ route('admin.listingModeration.featured', $listing->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="is_featured" value="{{ !empty($listing->is_featured) ? 0 : 1 }}">
                            <button type="submit" class="btn {{ !empty($listing->is_featured) ? 'btn-info' : 'btn-outline-secondary' }}">
                                {{ !empty($listing->is_featured) ? __('Unfeature') : __('Feature') }}
                            </button>
                        </form>
                    @endhasPermission

                    @hasPermission('admin.listingModeration.publish')
                        <form action="{{ route('admin.listingModeration.publish', $listing->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="is_published" value="{{ $listing->is_published ? 0 : 1 }}">
                            <button type="submit" class="btn {{ $listing->is_published ? 'btn-primary' : 'btn-warning' }}">
                                {{ $listing->is_published ? __('Unpublish') : __('Publish') }}
                            </button>
                        </form>
                    @endhasPermission

                    @hasPermission('admin.listingModeration.delete')
                        <a href="{{ route('admin.listingModeration.delete', $listing->id) }}" class="btn btn-danger deleteConfirmAlert">
                            {{ __('Delete Listing') }}
                        </a>
                    @endhasPermission
                </div>
            </div>
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
                    confirmButtonText: "{{ __('Yes, delete it!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
@endpush
