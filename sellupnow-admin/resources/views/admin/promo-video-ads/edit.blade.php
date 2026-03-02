@extends('layouts.app')

@section('header-title', __('Edit Promo Video'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Edit Promo Video') }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.promoVideoAds.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">#{{ $row->id }}</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('Current Video') }}</label>
                    @if(!empty($row->video_url))
                        @php
                            $ext = strtolower(pathinfo(parse_url($row->video_url, PHP_URL_PATH), PATHINFO_EXTENSION));
                            $isHosted = in_array($ext, ['mp4','webm','ogg','mov']);
                        @endphp
                        @if($isHosted)
                            <div>
                                <video src="{{ $row->video_url }}" controls
                                       style="max-width:480px;max-height:270px;border-radius:6px"></video>
                            </div>
                        @else
                            <div>
                                <a href="{{ $row->video_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                    {{ __('Open video link') }} &nearr;
                                </a>
                                <small class="text-muted ms-2">{{ $row->video_url }}</small>
                            </div>
                        @endif
                    @else
                        <div class="text-muted">{{ __('No video yet.') }}</div>
                    @endif

                    @if(!empty($row->thumbnail_url))
                        <div class="mt-2">
                            <label class="form-label">{{ __('Current Thumbnail') }}</label><br>
                            <img src="{{ $row->thumbnail_url }}" alt="thumbnail"
                                 style="max-width:160px;max-height:90px;border-radius:4px;object-fit:cover">
                        </div>
                    @endif
                </div>

                <form action="{{ route('admin.promoVideoAds.update', $row->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    {{-- ── Replace video (optional) ─────────────────────────── --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('Replace Video File') }} <span class="text-muted small">({{ __('optional — leave blank to keep current') }})</span></label>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
                               class="form-control @error('video_file') is-invalid @enderror">
                        <div class="form-text">mp4, webm, ogg, mov — max 200 MB</div>
                        @error('video_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Replace Thumbnail') }} <span class="text-muted small">({{ __('optional') }})</span></label>
                        <input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp"
                               class="form-control @error('thumbnail_file') is-invalid @enderror">
                        @error('thumbnail_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Moderation') }}</label>
                        @php
                            $moderation = $row->is_approved ? 'approved' : ($row->is_rejected ? 'rejected' : 'pending');
                        @endphp
                        <select class="form-control" name="moderation">
                            <option value="pending" {{ old('moderation', $moderation) === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="approved" {{ old('moderation', $moderation) === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                            <option value="rejected" {{ old('moderation', $moderation) === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        </select>
                        @error('moderation')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Sponsored') }}</label>
                        <select class="form-control" name="is_sponsored">
                            <option value="0" {{ (int)old('is_sponsored', (int)($row->is_sponsored ?? 0)) === 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                            <option value="1" {{ (int)old('is_sponsored', (int)($row->is_sponsored ?? 0)) === 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                        </select>
                        @error('is_sponsored')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('Reject reason (if rejected)') }}</label>
                        <input type="text" name="reject_reason" value="{{ old('reject_reason', $row->reject_reason ?? '') }}" class="form-control">
                        @error('reject_reason')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Caption') }}</label>
                        <textarea class="form-control" name="caption" rows="3">{{ old('caption', $row->caption ?? '') }}</textarea>
                        @error('caption')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('CTA text') }}</label>
                        <input type="text" name="cta_text" value="{{ old('cta_text', $row->cta_text ?? '') }}" class="form-control">
                        @error('cta_text')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">{{ __('CTA URL') }}</label>
                        <input type="text" name="cta_url" value="{{ old('cta_url', $row->cta_url ?? '') }}" class="form-control">
                        @error('cta_url')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Start at') }} <span class="text-muted small">(optional — blank = live immediately)</span></label>
                        <div class="input-group">
                            <input type="text" id="start_at" name="start_at"
                                   value="{{ old('start_at', !empty($row->start_at) ? \Carbon\Carbon::parse($row->start_at)->format('Y-m-d H:i') : '') }}"
                                   class="form-control" placeholder="Pick date &amp; time…" autocomplete="off" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFp('start_at')" title="Clear">&#x2715;</button>
                        </div>
                        @error('start_at')<small class="text-danger d-block">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('End at') }} <span class="text-muted small">(optional — blank = run indefinitely)</span></label>
                        <div class="input-group">
                            <input type="text" id="end_at" name="end_at"
                                   value="{{ old('end_at', !empty($row->end_at) ? \Carbon\Carbon::parse($row->end_at)->format('Y-m-d H:i') : '') }}"
                                   class="form-control" placeholder="Pick date &amp; time…" autocomplete="off" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFp('end_at')" title="Clear">&#x2715;</button>
                        </div>
                        @error('end_at')<small class="text-danger d-block">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Banner/Ad on this promo video (Trending videos)') }}</label>
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
                        @error('bottom_advertisement_id')<small class="text-danger">{{ $message }}</small>@enderror
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
