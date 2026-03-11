@extends('frontend.layout.master')
@section('site_title') {{ __('Submit Banner Ad') }} @endsection
@section('content')
<div class="profile-setting profile-pages section-padding2">
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
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <a href="{{ route('user.banner-ads.index') }}" class="text-muted" style="font-size:22px;" title="{{ __('Back') }}">
                                        <i class="las la-arrow-left"></i>
                                    </a>
                                    <h4 class="mb-0" style="font-weight:700;">{{ __('Submit Banner Ad Request') }}</h4>
                                    @if($bannerQuota === -1)
                                        <span class="badge bg-success ms-auto">{{ __('Unlimited') }}</span>
                                    @elseif($bannerQuota > 0)
                                        <span class="badge ms-auto" style="background:#f0f4ff;color:#524EB7;border:1px solid #c7d2fe;">
                                            {{ $bannerUsed }}/{{ $bannerQuota }} {{ __('used') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Quota error --}}
                                @error('quota')
                                    <div class="alert alert-danger d-flex gap-2 align-items-start mb-4" style="font-size:13px;">
                                        <i class="las la-exclamation-circle" style="font-size:18px;margin-top:1px;"></i>
                                        <div>{{ $message }}</div>
                                    </div>
                                @enderror

                                {{-- Notice --}}
                                <div class="alert alert-info d-flex gap-2 align-items-start mb-4" style="font-size:13px;">
                                    <i class="las la-info-circle" style="font-size:18px;margin-top:1px;"></i>
                                    <div>
                                        {{ __('Your ad will be reviewed before going live. An admin will approve or reject it within 1–2 business days.') }}
                                    </div>
                                </div>

                                <form action="{{ route('user.banner-ads.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-3">
                                        {{-- Ad title --}}
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">{{ __('Ad Title') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                                   value="{{ old('title') }}" required maxlength="255"
                                                   placeholder="{{ __('e.g., Summer Sale - 50% Off') }}">
                                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Desired slot --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">{{ __('Desired Placement') }} <span class="text-danger">*</span></label>
                                            <select name="requested_slot" class="form-select @error('requested_slot') is-invalid @enderror" required>
                                                <option value="">{{ __('— Select a slot —') }}</option>
                                                @foreach($slots as $key => $label)
                                                    <option value="{{ $key }}" {{ old('requested_slot') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('requested_slot') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Redirect URL --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">{{ __('Click-through URL') }} <span class="text-danger">*</span></label>
                                            <input type="url" name="redirect_url" class="form-control @error('redirect_url') is-invalid @enderror"
                                                   value="{{ old('redirect_url') }}"
                                                   placeholder="https://example.com/landing-page" required>
                                            @error('redirect_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Image upload --}}
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">{{ __('Banner Image') }} <span class="text-danger">*</span></label>
                                            <input type="file" name="image" id="bannerImageInput"
                                                   class="form-control @error('image') is-invalid @enderror"
                                                   accept="image/jpeg,image/png,image/gif,image/webp" required>
                                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="text-muted d-block mt-1">
                                                {{ __('Accepted formats: JPG, PNG, GIF, WebP. Max size: 2 MB.') }}
                                            </small>
                                            {{-- Preview --}}
                                            <div id="imagePreviewWrap" class="mt-2 d-none">
                                                <img id="imagePreview" src="" alt="Preview"
                                                     style="max-height:120px;max-width:100%;border-radius:6px;border:1px solid #e2e8f0;">
                                            </div>
                                        </div>

                                        {{-- Recommended dimensions reference --}}
                                        <div class="col-12">
                                            <div class="p-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;">
                                                <strong class="d-block mb-2">{{ __('Recommended dimensions by slot:') }}</strong>
                                                <ul class="mb-0 ps-3">
                                                    @foreach($slots as $key => $label)
                                                        <li><strong>{{ $label }}</strong>:
                                                            @switch($key)
                                                                @case('homepage_hero_banner')       1920 × 280 px @break
                                                                @case('homepage_footer_banner')     1200 × 120 px @break
                                                                @case('listing_details_left')       300 × 250 px @break
                                                                @case('listing_details_under_gallery') 770 × 120 px @break
                                                                @case('listing_details_right')      300 × 250 px @break
                                                                @case('user_profile_under_header')  770 × 100 px @break
                                                                @case('listings_under_image')       300 × 250 px @break
                                                                @default                            Flexible @break
                                                            @endswitch
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        {{-- Submit --}}
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="cmn-btn">
                                                <i class="las la-paper-plane me-1"></i>{{ __('Submit for Review') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>{{-- /main-body --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('bannerImageInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const wrap    = document.getElementById('imagePreviewWrap');
    const preview = document.getElementById('imagePreview');
    const reader  = new FileReader();
    reader.onload  = ev => { preview.src = ev.target.result; wrap.classList.remove('d-none'); };
    reader.readAsDataURL(file);
});
</script>
@endpush
