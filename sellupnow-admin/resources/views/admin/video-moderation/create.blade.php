@extends('layouts.app')

@section('header-title', __('Add Video'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Add Video to Listing') }}</h4>
        <a href="{{ route('admin.videoModeration.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Attach a Video to a Listing') }}</h5>
                <small class="text-muted">{{ __('Upload a video file OR paste an external URL — only one is required.') }}</small>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('admin.videoModeration.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('Listing ID') }} <span class="text-danger">*</span></label>
                        <input type="number" name="listing_id" value="{{ old('listing_id') }}" class="form-control @error('listing_id') is-invalid @enderror" required>
                        @error('listing_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ── Upload method selector ──────────────────────────────── --}}
                    <div class="col-12">
                        <div class="d-flex gap-4 mb-2" id="video-source-tabs">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="_video_source" id="srcFile" value="file" checked>
                                <label class="form-check-label fw-semibold" for="srcFile">{{ __('Upload Video File') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="_video_source" id="srcUrl" value="url">
                                <label class="form-check-label fw-semibold" for="srcUrl">{{ __('Paste External URL') }}</label>
                            </div>
                        </div>

                        {{-- File upload panel --}}
                        <div id="panelFile">
                            <label class="form-label">{{ __('Video File') }} <span class="text-muted small">(mp4, webm, ogg, mov — max 200 MB)</span></label>
                            <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
                                   class="form-control @error('video_file') is-invalid @enderror">
                            @error('video_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- URL panel --}}
                        <div id="panelUrl" style="display:none">
                            <label class="form-label">{{ __('Video URL') }} <span class="text-muted small">(YouTube, Vimeo, direct link…)</span></label>
                            <input type="text" name="video_url" value="{{ old('video_url') }}"
                                   class="form-control @error('video_url') is-invalid @enderror"
                                   placeholder="https://…">
                            @error('video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <small class="text-muted d-block mt-1">{{ __('The video will be marked as Pending approval after saving.') }}</small>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[name="_video_source"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.getElementById('panelFile').style.display = this.value === 'file' ? '' : 'none';
            document.getElementById('panelUrl').style.display  = this.value === 'url'  ? '' : 'none';
        });
    });
</script>
@endpush
