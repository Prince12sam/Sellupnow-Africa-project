@extends('layouts.app')

@section('header-title', __('User Reviews'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('User Reviews') }}</h4>
        <small class="text-muted">{{ __('User-to-user reviews from the Listocean platform') }}</small>
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

        {{-- Status filter --}}
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('admin.listocean-review.index') }}"
               class="btn btn-sm {{ !$statusFilter ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ __('All') }}
            </a>
            <a href="{{ route('admin.listocean-review.index', ['status' => 'approved']) }}"
               class="btn btn-sm {{ $statusFilter === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                {{ __('Approved') }}
            </a>
            <a href="{{ route('admin.listocean-review.index', ['status' => 'pending']) }}"
               class="btn btn-sm {{ $statusFilter === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                {{ __('Pending') }}
            </a>
            <a href="{{ route('admin.listocean-review.index', ['status' => 'rejected']) }}"
               class="btn btn-sm {{ $statusFilter === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                {{ __('Rejected') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">#</th>
                                <th>{{ __('Reviewer') }}</th>
                                <th>{{ __('Reviewed User') }}</th>
                                <th>{{ __('Rating') }}</th>
                                <th style="min-width: 250px">{{ __('Message') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th class="text-end pe-3">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    <td class="ps-3">{{ $review->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $review->reviewer_name }}</div>
                                        <small class="text-muted">@{{ $review->reviewer_username }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $review->reviewed_name }}</div>
                                        <small class="text-muted">@{{ $review->reviewed_username }}</small>
                                    </td>
                                    <td>
                                        @php $rating = (float) $review->rating; @endphp
                                        <span class="text-warning fw-bold">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($rating))
                                                    <i class="fa fa-star"></i>
                                                @elseif($i - $rating < 1)
                                                    <i class="fa fa-star-half-o"></i>
                                                @else
                                                    <i class="fa fa-star-o"></i>
                                                @endif
                                            @endfor
                                        </span>
                                        <span class="ms-1 text-muted small">{{ number_format($rating, 1) }}</span>
                                    </td>
                                    <td>
                                        <span>{{ \Illuminate\Support\Str::limit($review->message, 120) }}</span>
                                    </td>
                                    <td>
                                        @if($review->status === 'approved')
                                            <span class="badge bg-success">{{ __('Approved') }}</span>
                                        @elseif($review->status === 'rejected')
                                            <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex gap-2 justify-content-end">
                                            {{-- Approve/Pending toggle --}}
                                            <a href="{{ route('admin.listocean-review.toggle', $review->id) }}"
                                               class="btn btn-sm {{ $review->status === 'approved' ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                               title="{{ $review->status === 'approved' ? __('Set Pending') : __('Approve') }}">
                                                <i class="fa {{ $review->status === 'approved' ? 'fa-pause' : 'fa-check' }}"></i>
                                                {{ $review->status === 'approved' ? __('Pending') : __('Approve') }}
                                            </a>
                                            {{-- Delete --}}
                                            <a href="{{ route('admin.listocean-review.destroy', $review->id) }}"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('{{ __('Delete this review? This cannot be undone.') }}')"
                                               title="{{ __('Delete') }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        {{ __('No reviews found') }}
                                        @if($statusFilter)
                                            {{ __('with status') }} "{{ $statusFilter }}"
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $reviews->links() }}
        </div>

    </div>
@endsection
