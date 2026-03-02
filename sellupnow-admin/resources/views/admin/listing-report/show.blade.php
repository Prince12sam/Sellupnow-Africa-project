@extends('layouts.app')

@section('header-title', __('Listing Report Details'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Listing Report Details') }}</h4>
        <a href="{{ route('admin.listingReport.index') }}" class="btn py-2.5 btn-secondary">
            <i class="fa fa-arrow-left"></i>
            {{ __('Back to Reports') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card mb-3">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Report Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>{{ __('Report ID') }}:</strong> #{{ $listingReport->id }}
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Created At') }}:</strong> {{ $listingReport->created_at?->format('d M Y h:i A') }}
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Current Status') }}:</strong>
                        @if ($listingReport->status === 'resolved')
                            <span class="badge bg-success">{{ __('Resolved') }}</span>
                        @elseif ($listingReport->status === 'rejected')
                            <span class="badge bg-danger">{{ __('Rejected') }}</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Resolved At') }}:</strong>
                        {{ $listingReport->resolved_at?->format('d M Y h:i A') ?? __('N/A') }}
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Reason') }}:</strong> {{ $listingReport->reason?->name ?? __('N/A') }}
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Reported By') }}:</strong>
                        {{ $listingReport->user?->name ?? __('Guest') }}
                        @if($listingReport->user?->phone)
                            ({{ $listingReport->user->phone }})
                        @endif
                    </div>
                </div>

                <hr>

                <div>
                    <h6>{{ __('Description') }}</h6>
                    <p class="mb-0">{{ $listingReport->description ?: __('No description provided.') }}</p>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Listing Information') }}</h5>
            </div>
            <div class="card-body">
                @if ($listingReport->listing)
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div><strong>{{ __('Listing ID') }}:</strong> #{{ $listingReport->listing->id }}</div>
                            <div><strong>{{ __('Title') }}:</strong> {{ $listingReport->listing->title }}</div>
                            <div><strong>{{ __('Price') }}:</strong> {{ $listingReport->listing->price }}</div>
                            <div><strong>{{ __('Status') }}:</strong>
                                @if ($listingReport->listing->status)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                            <div><strong>{{ __('Published') }}:</strong>
                                @if ($listingReport->listing->is_published)
                                    <span class="badge bg-success">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ __('No') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <img src="{{ $listingReport->listing->thumbnail }}" alt="listing" class="img-fluid rounded" loading="lazy">
                        </div>
                    </div>
                @else
                    <p class="mb-0">{{ __('Listing not found or removed.') }}</p>
                @endif
            </div>
        </div>

        @hasPermission('admin.listingReport.status')
            <div class="card">
                <div class="card-header py-3">
                    <h5 class="card-title m-0">{{ __('Update Status') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.listingReport.status', $listingReport->id) }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $listingReport->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="resolved" {{ $listingReport->status === 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                                <option value="rejected" {{ $listingReport->status === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endhasPermission
    </div>
@endsection
