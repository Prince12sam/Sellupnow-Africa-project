@extends('layouts.app')

@section('header-title', __('Video Details'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <div>
            <h4 class="m-0">{{ __('Video Details') }}</h4>
            <small class="text-muted">#{{ $row->id }} — {{ $row->title }}</small>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.videoModeration.edit', $row->id) }}" class="btn btn-outline-secondary">{{ __('Edit') }}</a>
            <a href="{{ $row->listing_url }}" target="_blank" class="btn btn-outline-primary">{{ __('Open Listing') }}</a>
            <a href="{{ route('admin.videoModeration.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div><strong>{{ __('Owner') }}:</strong> {{ $row->user?->name ?? __('N/A') }}</div>
                        <div><strong>{{ __('Slug') }}:</strong> {{ $row->slug }}</div>
                        <div><strong>{{ __('Active') }}:</strong> {{ (int)$row->status === 1 ? __('Yes') : __('No') }}</div>
                        <div><strong>{{ __('Published') }}:</strong> {{ (int)$row->is_published === 1 ? __('Yes') : __('No') }}</div>
                        <div><strong>{{ __('Video Approved') }}:</strong> {{ (int)$row->video_is_approved === 1 ? __('Approved') : __('Pending') }}</div>
                    </div>

                    <div class="col-md-6">
                        <div><strong>{{ __('Video URL') }}:</strong></div>
                        @if(!empty($row->video_url))
                            <div class="mb-2">
                                <a href="{{ $row->video_url }}" target="_blank" rel="noopener">{{ $row->video_url }}</a>
                            </div>
                        @else
                            <div class="text-muted">--</div>
                        @endif

                        <div class="d-flex gap-2 flex-wrap">
                            <form action="{{ route('admin.videoModeration.approve', $row->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="video_is_approved" value="{{ (int)$row->video_is_approved === 1 ? 0 : 1 }}">
                                <button type="submit" class="btn {{ (int)$row->video_is_approved === 1 ? 'btn-secondary' : 'btn-success' }}">
                                    {{ (int)$row->video_is_approved === 1 ? __('Unapprove') : __('Approve') }}
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#removeVideoModal">
                                {{ __('Remove Video') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                    <p class="fw-semibold mb-3" style="font-size:.9rem">{{ e($row->listing_title ?? $row->slug ?? '#'.$row->id) }}</p>
                    <p class="text-muted" style="font-size:.82rem">{{ __('The listing will not be deleted — only the video will be removed.') }}</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form action="{{ route('admin.videoModeration.removeVideo', $row->id) }}" method="POST">
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
