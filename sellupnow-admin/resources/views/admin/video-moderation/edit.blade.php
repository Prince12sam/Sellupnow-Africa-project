@extends('layouts.app')

@section('header-title', __('Edit Video'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Edit Video') }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.videoModeration.show', $row->id) }}" class="btn btn-outline-info">{{ __('View') }}</a>
            <a href="{{ route('admin.videoModeration.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">#{{ $row->id }} — {{ $row->title }}</h5>
                <small class="text-muted">{{ $row->slug }}</small>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('admin.videoModeration.update', $row->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    {{-- ── Current video preview ──────────────────────────────── --}}
                    @if(!empty($row->video_url))
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('Current Video') }}</label>
                            @php
                                $ext = strtolower(pathinfo(parse_url($row->video_url, PHP_URL_PATH), PATHINFO_EXTENSION));
                                $isHosted = in_array($ext, ['mp4','webm','ogg','mov']);
                            @endphp
                            @if($isHosted)
                                <div>
                                    <video src="{{ $row->video_url }}" controls style="max-width:480px;max-height:270px;border-radius:6px"></video>
                                </div>
                            @else
                                <div>
                                    <a href="{{ $row->video_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                        {{ __('Open video link') }} &nearr;
                                    </a>
                                    <small class="text-muted ms-2">{{ $row->video_url }}</small>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ── Replace video (file OR URL) ──────────────────────── --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('Replace Video') }}</label>
                        <div class="d-flex gap-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="_video_replace" id="editSrcFile" value="file" checked>
                                <label class="form-check-label" for="editSrcFile">{{ __('Upload new file') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="_video_replace" id="editSrcUrl" value="url">
                                <label class="form-check-label" for="editSrcUrl">{{ __('Change URL') }}</label>
                            </div>
                        </div>

                        <div id="editPanelFile">
                            <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
                                   class="form-control @error('video_file') is-invalid @enderror">
                            <small class="text-muted">mp4, webm, ogg, mov — max 200 MB</small>
                            @error('video_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div id="editPanelUrl" style="display:none">
                            <input type="text" name="video_url" value="{{ old('video_url', $row->video_url) }}"
                                   class="form-control @error('video_url') is-invalid @enderror"
                                   placeholder="https://…">
                            <small class="text-muted">{{ __('Clear to remove the video entirely.') }}</small>
                            @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('Approved') }}</label>
                        <select class="form-control" name="video_is_approved">
                            <option value="0" {{ (int)old('video_is_approved', $row->video_is_approved) === 0 ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="1" {{ (int)old('video_is_approved', $row->video_is_approved) === 1 ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        </select>
                        @error('video_is_approved')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Banner/Ad on this video (Trending videos)') }}</label>
                        <select class="form-control" name="bottom_advertisement_id">
                            <option value="0">{{ __('None') }}</option>
                            @foreach(($ads ?? []) as $ad)
                                @php
                                    $label = trim((string)($ad->title ?? ''));
                                    if ($label === '') $label = 'Ad #' . ($ad->id ?? '');
                                    $label .= ' — ' . (($ad->type ?? '')); 
                                @endphp
                                <option value="{{ (int)($ad->id ?? 0) }}" {{ (int)old('bottom_advertisement_id', $currentAdId ?? 0) === (int)($ad->id ?? 0) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ __('This lets admin place a specific promo/banner ad on this specific reel video at /trending-videos.') }}</small>
                        @error('bottom_advertisement_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#removeVideoModal">
                            {{ __('Remove Video') }}
                        </button>
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
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[name="_video_replace"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.getElementById('editPanelFile').style.display = this.value === 'file' ? '' : 'none';
            document.getElementById('editPanelUrl').style.display  = this.value === 'url'  ? '' : 'none';
        });
    });
</script>
@endpush
