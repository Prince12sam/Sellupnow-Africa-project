@extends('frontend.layout.master')
@section('site_title')
    {{ __('Edit Video') }}
@endsection
@section('content')
    <div class="profile-setting section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>

                                <div class="relevant-ads box-shadow1 p-24">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <h4 class="mb-0">{{ __('Edit Video') }}</h4>
                                        <a href="{{ route('user.my.videos') }}" class="btn btn-outline-secondary btn-sm">
                                            ← {{ __('Back to My Videos') }}
                                        </a>
                                    </div>

                                    @if($errors->any())
                                        <div class="alert alert-danger mb-3">
                                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                                        </div>
                                    @endif

                                    <form method="POST"
                                          action="{{ route('user.my.videos.update', $video->id) }}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        {{-- Current video preview --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">{{ __('Current Video') }}</label>
                                            <div style="max-width:320px;">
                                                <video src="{{ $video->video_url }}"
                                                       @if($video->thumbnail_url) poster="{{ $video->thumbnail_url }}" @endif
                                                       controls muted playsinline preload="metadata"
                                                       style="width:100%;border-radius:8px;background:#000;"></video>
                                            </div>
                                        </div>

                                        {{-- Replace video (optional) --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">{{ __('Replace Video') }}
                                                <span class="text-muted fw-normal" style="font-size:13px;">— {{ __('optional') }}</span>
                                            </label>
                                            <input type="file" name="video_file" id="video_file"
                                                   class="form-control @error('video_file') is-invalid @enderror"
                                                   accept="video/mp4,video/webm,video/quicktime">
                                            <small class="text-muted">{{ __('MP4, WebM or MOV — max 200 MB. Replacing the video will reset its approval status.') }}</small>
                                            @error('video_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Thumbnail section --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">{{ __('Thumbnail') }}</label>

                                            <canvas id="thumb-canvas" style="display:none;"></canvas>
                                            <input type="hidden" name="thumbnail_base64" id="thumbnail_base64">

                                            <div class="d-flex align-items-start gap-3 flex-wrap">
                                                <div style="flex:0 0 auto;">
                                                    <img id="thumb-preview"
                                                         src="{{ $video->thumbnail_url ?: '' }}"
                                                         alt="thumbnail"
                                                         style="width:160px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;background:#000;
                                                                {{ $video->thumbnail_url ? '' : 'display:none;' }}"
                                                         >
                                                    <div id="thumb-placeholder"
                                                         style="width:160px;height:90px;border-radius:6px;border:1px dashed #cbd5e1;background:#f8fafc;
                                                                display:{{ $video->thumbnail_url ? 'none' : 'flex' }};
                                                                align-items:center;justify-content:center;color:#94a3b8;font-size:12px;">
                                                        {{ __('No thumbnail') }}
                                                    </div>
                                                </div>
                                                <div style="flex:1;min-width:200px;" id="seek-wrap" style="display:none;">
                                                    <label class="form-label mb-1" style="font-size:12px;">{{ __('Drag to pick a different frame') }}</label>
                                                    <input type="range" id="thumb-seek" class="form-range" min="0" max="100" value="5" step="1">
                                                    <small class="text-muted" id="thumb-time-label" style="font-size:11px;">{{ __('Frame at: 0s') }}</small>
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="recapture-btn">
                                                            <i class="las la-camera me-1"></i>{{ __('Re-capture from current video') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <video id="thumb-video" crossorigin="anonymous"
                                                   style="display:none;width:1px;height:1px;" muted playsinline></video>
                                        </div>

                                        {{-- Caption --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">{{ __('Caption') }}</label>
                                            <textarea name="caption" rows="2"
                                                      class="form-control @error('caption') is-invalid @enderror"
                                                      maxlength="300"
                                                      placeholder="{{ __('Short description shown under the video...') }}">{{ old('caption', $video->caption) }}</textarea>
                                            @error('caption')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Tag a listing --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">{{ __('Tag a Listing') }}</label>
                                            <select name="listing_id" class="form-select @error('listing_id') is-invalid @enderror">
                                                <option value="">{{ __('— None (no listing linked) —') }}</option>
                                                @foreach($listings as $listing)
                                                    <option value="{{ $listing->id }}"
                                                        {{ old('listing_id', $video->listing_id) == $listing->id ? 'selected' : '' }}>
                                                        #{{ $listing->id }} — {{ $listing->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('listing_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- CTA --}}
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">{{ __('Button Text') }}</label>
                                                <input type="text" name="cta_text"
                                                       value="{{ old('cta_text', $video->cta_text) }}"
                                                       class="form-control @error('cta_text') is-invalid @enderror"
                                                       maxlength="60"
                                                       placeholder="{{ __('e.g. Shop Now') }}">
                                                @error('cta_text')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold">{{ __('Button URL') }}</label>
                                                <input type="url" name="cta_url"
                                                       value="{{ old('cta_url', $video->cta_url) }}"
                                                       class="form-control @error('cta_url') is-invalid @enderror"
                                                       maxlength="2000"
                                                       placeholder="https://...">
                                                @error('cta_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-flex gap-3">
                                            <button type="submit" class="red-btn" id="saveBtn">
                                                <i class="las la-save me-1"></i> {{ __('Save Changes') }}
                                            </button>
                                            <a href="{{ route('user.my.videos') }}" class="btn btn-outline-secondary">
                                                {{ __('Cancel') }}
                                            </a>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
(function () {
    const videoInput   = document.getElementById('video_file');
    const thumbVideo   = document.getElementById('thumb-video');
    const canvas       = document.getElementById('thumb-canvas');
    const preview      = document.getElementById('thumb-preview');
    const placeholder  = document.getElementById('thumb-placeholder');
    const seekWrap     = document.getElementById('seek-wrap');
    const seekSlider   = document.getElementById('thumb-seek');
    const timeLabel    = document.getElementById('thumb-time-label');
    const hiddenInput  = document.getElementById('thumbnail_base64');
    const recaptureBtn = document.getElementById('recapture-btn');
    const saveBtn      = document.getElementById('saveBtn');
    let duration       = 0;
    let currentVideoSrc = '{{ $video->video_url }}';

    function captureFrame(seconds) {
        canvas.width  = thumbVideo.videoWidth  || 640;
        canvas.height = thumbVideo.videoHeight || 360;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(thumbVideo, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.82);
        hiddenInput.value = dataUrl;
        preview.src       = dataUrl;
        preview.style.display  = '';
        placeholder.style.display = 'none';
        timeLabel.textContent  = '{{ __("Frame at:") }} ' + seconds.toFixed(1) + 's';
    }

    function loadVideoForThumb(src) {
        thumbVideo.src = src;
        thumbVideo.addEventListener('loadedmetadata', function onMeta() {
            thumbVideo.removeEventListener('loadedmetadata', onMeta);
            duration = thumbVideo.duration || 10;
            seekWrap.style.display = '';
            seekSlider.addEventListener('input', function () {
                thumbVideo.currentTime = (this.value / 100) * duration;
            });
            thumbVideo.addEventListener('seeked', function () {
                captureFrame(thumbVideo.currentTime);
            });
        });
    }

    // Load existing video into thumb extractor for re-capture
    if (currentVideoSrc) {
        loadVideoForThumb(currentVideoSrc);
    }

    // When user selects a new video file
    videoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const mb = (file.size / 1024 / 1024).toFixed(1);
        saveBtn.innerHTML = '<i class="las la-save me-1"></i> {{ __("Save Changes") }} (' + mb + ' MB)';
        const blobUrl = URL.createObjectURL(file);
        currentVideoSrc = blobUrl;
        loadVideoForThumb(blobUrl);
        // seek to 5% for auto-capture
        thumbVideo.addEventListener('loadedmetadata', function onMeta2() {
            thumbVideo.removeEventListener('loadedmetadata', onMeta2);
            const seekTo = Math.min(duration * 0.05, Math.min(duration - 0.1, 1));
            thumbVideo.currentTime = seekTo;
        });
    });

    // Re-capture from current video
    if (recaptureBtn) {
        recaptureBtn.addEventListener('click', function () {
            if (!currentVideoSrc) return;
            const t = ((seekSlider.value / 100) * duration) || 1;
            thumbVideo.currentTime = t;
        });
    }

    document.querySelector('form').addEventListener('submit', function () {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}';
    });
})();
</script>
@endsection
