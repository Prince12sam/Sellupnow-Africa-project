@extends('layouts.app')

@section('header-title', __('Promo Video Ads'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Promo Video Ads') }}</h4>
        <a href="{{ route('admin.promoVideoAds.create') }}" class="btn btn-primary">
            + {{ __('Create Promo Video') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.promoVideoAds.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <select class="form-control" name="status">
                            @php $s = request('status', $status ?? 'pending'); @endphp
                            <option value="pending" {{ $s==='pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="approved" {{ $s==='approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                            <option value="rejected" {{ $s==='rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                            <option value="all" {{ $s==='all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search caption, url, id...') }}">
                    </div>
                    <div class="col-md-3 d-grid">
                        <button class="btn btn-primary" type="submit">{{ __('Filter') }}</button>
                    </div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Caption') }}</th>
                            <th>{{ __('Video') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($videos as $row)
                            @php
                                $statusText = $row->is_approved ? __('Approved') : ($row->is_rejected ? __('Rejected') : __('Pending'));
                            @endphp
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>
                                    @if(!empty($row->user_id))
                                        <span class="d-block">{{ $row->user->name ?? __('User #').$row->user_id }}</span>
                                        <span class="badge bg-info text-dark">{{ __('User Submission') }}</span>
                                    @else
                                        <span class="text-muted fst-italic">{{ __('Admin') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $row->is_approved ? 'bg-success' : ($row->is_rejected ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ $statusText }}
                                    </span>
                                    @if($row->is_rejected && !empty($row->reject_reason))
                                        <div class="small text-muted mt-1" style="max-width:200px;">{{ $row->reject_reason }}</div>
                                    @endif
                                </td>
                                <td style="max-width:420px;">
                                    {{ \Illuminate\Support\Str::limit((string)($row->caption ?? ''), 90) }}
                                </td>
                                <td>
                                    @if(!empty($row->video_url))
                                        <a href="{{ $row->video_url }}" target="_blank" rel="noopener">{{ __('Open') }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end flex-wrap">
                                        @if(!$row->is_approved)
                                            {{-- Quick Approve --}}
                                            <form method="POST" action="{{ route('admin.promoVideoAds.moderate', $row->id) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-success btn-sm">{{ __('Approve') }}</button>
                                            </form>
                                        @endif
                                        @if(!$row->is_rejected)
                                            {{-- Quick Reject (opens modal) --}}
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal"
                                                    data-id="{{ $row->id }}"
                                                    data-url="{{ route('admin.promoVideoAds.moderate', $row->id) }}">
                                                {{ __('Reject') }}
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.promoVideoAds.edit', $row->id) }}" class="btn btn-outline-secondary btn-sm">{{ __('Edit') }}</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('No promo videos found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $videos->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Reason Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="rejectForm">
                @csrf
                <input type="hidden" name="action" value="reject">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Video') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">{{ __('Reason (shown to user)') }}</label>
                        <textarea name="reject_reason" class="form-control" rows="3" placeholder="{{ __('Content not suitable for the platform.') }}"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Confirm Reject') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
document.getElementById('rejectModal').addEventListener('show.bs.modal', function (event) {
    const btn  = event.relatedTarget;
    const url  = btn.getAttribute('data-url');
    document.getElementById('rejectForm').setAttribute('action', url);
    // clear previous reason
    this.querySelector('textarea[name="reject_reason"]').value = '';
});
</script>
@endpush

@endsection
