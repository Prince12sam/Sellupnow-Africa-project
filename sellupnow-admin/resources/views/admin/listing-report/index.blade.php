@extends('layouts.app')

@section('header-title', __('Listing Reports'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Listing Reports') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Filter Reports') }}</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.listingReport.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="{{ __('Search by listing title, customer name or description') }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="status">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>{{ __('Resolved') }}</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" min="1" name="listing_id" class="form-control" value="{{ request('listing_id') }}"
                            placeholder="{{ __('Listing ID') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.listingReport.index') }}" class="btn btn-secondary w-100">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Report List') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Listing') }}</th>
                                <th>{{ __('Reported By') }}</th>
                                <th>{{ __('Reason') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $key => $report)
                                @php $serial = $reports->firstItem() + $key; @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>
                                        <div>#{{ $report->listing_id }}</div>
                                        <small class="text-muted">{{ $report->listing?->title ?? __('Listing not found') }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $report->user?->name ?? __('Guest') }}</div>
                                        @if($report->user?->phone)
                                            <small class="text-muted">{{ $report->user->phone }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $report->reason?->name ?? __('N/A') }}</td>
                                    <td>
                                        @if ($report->status === 'resolved')
                                            <span class="badge bg-success">{{ __('Resolved') }}</span>
                                        @elseif ($report->status === 'rejected')
                                            <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->created_at?->format('d M Y h:i A') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            @hasPermission('admin.listingReport.show')
                                                <a href="{{ route('admin.listingReport.show', $report->id) }}"
                                                    class="btn btn-outline-primary btn-sm circleIcon" title="{{ __('View') }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endhasPermission
                                            @hasPermission('admin.listingReport.delete')
                                                <a href="{{ route('admin.listingReport.delete', $report->id) }}"
                                                    class="btn btn-outline-danger btn-sm deleteConfirmAlert circleIcon" title="{{ __('Delete') }}">
                                                    <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="icon" loading="lazy" />
                                                </a>
                                            @endhasPermission
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
            {{ $reports->withQueryString()->links() }}
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
