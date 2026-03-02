@extends('frontend.layout.master')
@section('site_title')
    {{ __('Upload Video') }}
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
                                        <h4 class="mb-0">{{ __('Upload a Video') }}</h4>
                                        <a href="{{ route('user.my.videos') }}" class="btn btn-outline-secondary btn-sm">
                                            ← {{ __('Back to My Videos') }}
                                        </a>
                                    </div>

                                    @if($videoQuota > 0)
                                        <p class="text-muted mb-4" style="font-size:13px;">
                                            {{ $videoUsed }} / {{ $videoQuota }} {{ __('videos used on your plan') }}
                                        </p>
                                    @endif

                                    <form method="POST" action="{{ route('user.my.videos.store') }}" enctype="multipart/form-data">
                                        @csrf

                                        {{-- Video file --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">
                                                {{ __('Video File') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="video_file" id="video_file"
                                                   class="form-control @error('video_file') is-invalid @enderror"
                                                   accept="video/mp4,video/webm,video/quicktime" required>
                                            <small class="text-muted">{{ __('MP4, WebM or MOV — max 200 MB') }}</small>
                                            @error('video_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Auto-captured thumbnail preview --}}
                                        <div class="mb-4" id="thumb-section" style="display:none;">
                                            <label class="form-label fw-semibold">{{ __('Thumbnail') }}</label>

                                            {{-- hidden canvas used for capture (never visible) --}}
                                            <canvas id="thumb-canvas" style="display:none;"></canvas>
                                            {{-- base64 result is stored here and submitted with the form --}}
                                            <input type="hidden" name="thumbnail_base64" id="thumbnail_base64">

                                            <div class="d-flex align-items-start gap-3 flex-wrap">
                                                {{-- thumbnail preview --}}
                                                <div style="flex:0 0 auto;">
                                                    <img id="thumb-preview"
                                                         src="" alt="thumbnail"
                                                         style="width:160px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;background:#000;">
                                                </div>
                                                {{-- seek slider --}}
                                                <div style="flex:1;min-width:200px;">
                                                    <label class="form-label mb-1" style="font-size:12px;">{{ __('Drag to pick a different frame') }}</label>
                                                    <input type="range" id="thumb-seek" class="form-range" min="0" max="100" value="5" step="1">
                                                    <small class="text-muted" id="thumb-time-label" style="font-size:11px;">{{ __('Frame at: 0s') }}</small>
                                                </div>
                                            </div>

                                            {{-- hidden video element used for seeking/capturing --}}
                                            <video id="thumb-video" crossorigin="anonymous"
                                                   style="display:none;width:1px;height:1px;" muted playsinline></video>
                                        </div>

                                        {{-- Caption --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">{{ __('Caption') }}</label>
                                            <textarea name="caption" rows="2"
                                                      class="form-control @error('caption') is-invalid @enderror"
                                                      maxlength="300"
                                                      placeholder="{{ __('Short description shown under the video...') }}">{{ old('caption') }}</textarea>
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
                                                    <option value="{{ $listing->id }}" {{ old('listing_id') == $listing->id ? 'selected' : '' }}>
                                                        #{{ $listing->id }} — {{ $listing->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">{{ __('Link this video to one of your active listings so buyers can find it.') }}</small>
                                            @error('listing_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- CTA (optional) --}}
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">{{ __('Button Text') }}</label>
                                                <input type="text" name="cta_text" value="{{ old('cta_text') }}"
                                                       class="form-control @error('cta_text') is-invalid @enderror"
                                                       maxlength="60"
                                                       placeholder="{{ __('e.g. Shop Now') }}">
                                                @error('cta_text')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold">{{ __('Button URL') }}</label>
                                                <input type="url" name="cta_url" value="{{ old('cta_url') }}"
                                                       class="form-control @error('cta_url') is-invalid @enderror"
                                                       maxlength="2000"
                                                       placeholder="https://...">
                                                @error('cta_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Notice --}}
                                        <div class="alert alert-info mb-4" style="font-size:13px;">
                                            <i class="las la-info-circle me-1"></i>
                                            {{ __('Your video will be reviewed by our team before it appears publicly. You will be notified once approved or if changes are needed.') }}
                                        </div>

                                        <div class="d-flex gap-3">
                                            <button type="submit" class="red-btn" id="uploadBtn">
                                                <i class="las la-cloud-upload-alt me-1"></i> {{ __('Submit for Review') }}
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
    const video        = document.getElementById('thumb-video');
    const canvas       = document.getElementById('thumb-canvas');
    const preview      = document.getElementById('thumb-preview');
    const seekSlider   = document.getElementById('thumb-seek');
    const timeLabel    = document.getElementById('thumb-time-label');
    const thumbSection = document.getElementById('thumb-section');
    const hiddenInput  = document.getElementById('thumbnail_base64');
    const uploadBtn    = document.getElementById('uploadBtn');
    let duration       = 0;

    function captureFrame(seconds) {
        canvas.width  = video.videoWidth  || 640;
        canvas.height = video.videoHeight || 360;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.82);
        hiddenInput.value = dataUrl;
        preview.src       = dataUrl;
        timeLabel.textContent = '{{ __("Frame at:") }} ' + seconds.toFixed(1) + 's';
    }

    videoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // update button label with size
        const mb = (file.size / 1024 / 1024).toFixed(1);
        uploadBtn.querySelector('i') && (uploadBtn.innerHTML =
            '<i class="las la-cloud-upload-alt me-1"></i> {{ __("Submit for Review") }} (' + mb + ' MB)');

        // load video for thumbnail extraction
        const blobUrl = URL.createObjectURL(file);
        video.src = blobUrl;
        video.addEventListener('loadedmetadata', function onMeta() {
            video.removeEventListener('loadedmetadata', onMeta);
            duration = video.duration || 10;
            // seek to 5 % (about 1s on a 20s clip)
            const seekTo = Math.min(duration * 0.05, Math.min(duration - 0.1, 1));
            video.currentTime = seekTo;
        });
        video.addEventListener('seeked', function onSeeked() {
            video.removeEventListener('seeked', onSeeked);
            captureFrame(video.currentTime);
            thumbSection.style.display = 'block';
            // re-wire seek slider
            seekSlider.addEventListener('input', function () {
                const t = (this.value / 100) * duration;
                video.currentTime = t;
            });
            video.addEventListener('seeked', function () {
                captureFrame(video.currentTime);
            });
        });
    });

    // Prevent double-submit; keep thumbnail_base64 intact
    document.querySelector('form').addEventListener('submit', function () {
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Uploading...") }}';
    });
})();
</script>
@endsection
