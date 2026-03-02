@extends('layouts.app')

@section('header-title', __('Create Promo Video Ad'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Create Promo Video Ad') }}</h4>
        <a href="{{ route('admin.promoVideoAds.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('New Sponsored Promo Video') }}</h5>
                <small class="text-muted">{{ __('Admin-created videos are auto-approved and marked as sponsored.') }}</small>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('admin.promoVideoAds.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    {{-- ── Video file (required) ──────────────────────────────── --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">{{ __('Video File') }} <span class="text-danger">*</span></label>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
                               class="form-control @error('video_file') is-invalid @enderror" required>
                        <div class="form-text">mp4, webm, ogg, mov — max 200 MB</div>
                        @error('video_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ── Thumbnail (optional) ──────────────────────────────── --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('Thumbnail Image') }} <span class="text-muted small">(optional)</span></label>
                        <input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp"
                               class="form-control @error('thumbnail_file') is-invalid @enderror">
                        <div class="form-text">jpg, png, webp — max 10 MB</div>
                        @error('thumbnail_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ── Caption ──────────────────────────────────────────── --}}
                    <div class="col-12">
                        <label class="form-label">{{ __('Caption') }}</label>
                        <textarea class="form-control @error('caption') is-invalid @enderror" name="caption" rows="3" maxlength="300">{{ old('caption') }}</textarea>
                        @error('caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ── CTA ──────────────────────────────────────────────── --}}
                    <div class="col-md-4">
                        <label class="form-label">{{ __('CTA Button Text') }}</label>
                        <input type="text" name="cta_text" value="{{ old('cta_text') }}" maxlength="60"
                               class="form-control @error('cta_text') is-invalid @enderror"
                               placeholder="{{ __('e.g. Shop Now') }}">
                        @error('cta_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">{{ __('CTA URL') }}</label>
                        <input type="text" name="cta_url" value="{{ old('cta_url') }}" maxlength="2000"
                               class="form-control @error('cta_url') is-invalid @enderror"
                               placeholder="https://…">
                        @error('cta_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ── Schedule ─────────────────────────────────────────── --}}
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Start At') }} <span class="text-muted small">(optional — leave blank to go live immediately)</span></label>
                        <div class="input-group">
                            <input type="text" id="start_at" name="start_at" value="{{ old('start_at') }}"
                                   class="form-control @error('start_at') is-invalid @enderror"
                                   placeholder="Pick date &amp; time…" autocomplete="off" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFp('start_at')" title="Clear">&#x2715;</button>
                        </div>
                        @error('start_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('End At') }} <span class="text-muted small">(optional — leave blank to run indefinitely)</span></label>
                        <div class="input-group">
                            <input type="text" id="end_at" name="end_at" value="{{ old('end_at') }}"
                                   class="form-control @error('end_at') is-invalid @enderror"
                                   placeholder="Pick date &amp; time…" autocomplete="off" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFp('end_at')" title="Clear">&#x2715;</button>
                        </div>
                        @error('end_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-4">{{ __('Create Promo Video') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function clearFp(id) {
        var el = document.getElementById(id);
        if (el && el._flatpickr) { el._flatpickr.clear(); }
    }
    flatpickr('#start_at', { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, allowInput: false });
    flatpickr('#end_at',   { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, allowInput: false });
</script>
@endpush
