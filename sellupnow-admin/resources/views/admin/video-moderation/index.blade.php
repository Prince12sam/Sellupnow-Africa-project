@extends('layouts.app')

@section('header-title', __('Trending Videos'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Trending Videos') }}</h4>

        <a href="{{ route('admin.videoModeration.create') }}" class="btn btn-primary">
            {{ __('Add Video') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Filter Videos') }}</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.videoModeration.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <select class="form-control" name="approval">
                            <option value="pending" {{ request('approval', $approval ?? 'all') === 'pending' ? 'selected' : '' }}>{{ __('Pending Approval') }}</option>
                            <option value="approved" {{ request('approval', $approval ?? 'all') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                            <option value="all" {{ request('approval', $approval ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="{{ __('Search by title, slug, video url') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.videoModeration.index') }}" class="btn btn-secondary w-100">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Video Submissions') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Listing') }}</th>
                                <th>{{ __('Owner') }}</th>
                                <th>{{ __('Active') }}</th>
                                <th>{{ __('Published') }}</th>
                                <th>{{ __('Video URL') }}</th>
                                <th>{{ __('Video Approved') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($videos as $key => $row)
                                @php $serial = $videos->firstItem() + $key; @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>
                                        <div>#{{ $row->id }} - {{ $row->title }}</div>
                                        <small class="text-muted">{{ $row->slug }}</small>
                                    </td>
                                    <td>{{ $row->user?->name ?? __('N/A') }}</td>
                                    <td>
                                        <span class="badge {{ (int)$row->status === 1 ? 'bg-success' : 'bg-secondary' }}">{{ (int)$row->status === 1 ? __('Active') : __('Inactive') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ (int)$row->is_published === 1 ? 'bg-primary' : 'bg-warning text-dark' }}">{{ (int)$row->is_published === 1 ? __('Published') : __('Unpublished') }}</span>
                                    </td>
                                    <td style="max-width: 360px;">
                                        @if(!empty($row->video_url))
                                            <a href="{{ $row->video_url }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::limit($row->video_url, 60) }}</a>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ (int)$row->video_is_approved === 1 ? 'bg-success' : 'bg-warning text-dark' }}">{{ (int)$row->video_is_approved === 1 ? __('Approved') : __('Pending') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                                            <a href="{{ $row->listing_url }}" target="_blank" class="btn btn-outline-primary btn-sm">{{ __('Open Listing') }}</a>

                                            <a href="{{ route('admin.videoModeration.show', $row->id) }}" class="btn btn-outline-info btn-sm">{{ __('View') }}</a>
                                            <a href="{{ route('admin.videoModeration.edit', $row->id) }}" class="btn btn-outline-secondary btn-sm">{{ __('Edit') }}</a>

                                            <form action="{{ route('admin.videoModeration.approve', $row->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="video_is_approved" value="{{ (int)$row->video_is_approved === 1 ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-sm {{ (int)$row->video_is_approved === 1 ? 'btn-secondary' : 'btn-success' }}">
                                                    {{ (int)$row->video_is_approved === 1 ? __('Unapprove') : __('Approve') }}
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-danger rv-trigger"
                                                data-action="{{ route('admin.videoModeration.removeVideo', $row->id) }}"
                                                data-title="{{ e($row->listing_title ?? $row->slug ?? '#'.$row->id) }}">
                                                {{ __('Remove Video') }}
                                            </button>
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
            {{ $videos->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- Remove Video Confirmation Modal --}}
    <div class="modal fade" id="removeVideoModal" tabindex="-1" aria-labelledby="removeVideoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-body text-center p-4 pb-0">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10" style="width:68px;height:68px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#dc3545" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                        </svg>
                    </div>
                    <h5 class="fw-bold mb-2" id="removeVideoModalLabel">{{ __('Remove Video?') }}</h5>
                    <p class="text-muted mb-1" style="font-size:.9rem">{{ __('You are about to remove the video from:') }}</p>
                    <p class="fw-semibold mb-3" id="rvListingTitle" style="font-size:.9rem"></p>
                    <p class="text-muted" style="font-size:.82rem">{{ __('The listing will not be deleted — only the video will be removed.') }}</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form id="rvConfirmForm" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                            {{ __('Yes, Remove') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    var modal    = document.getElementById('removeVideoModal');
    var form     = document.getElementById('rvConfirmForm');
    var titleEl  = document.getElementById('rvListingTitle');

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.rv-trigger');
        if (!btn) return;
        form.action    = btn.dataset.action;
        titleEl.textContent = btn.dataset.title || '';
        var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
        bsModal.show();
    });
})();
</script>
@endpush
