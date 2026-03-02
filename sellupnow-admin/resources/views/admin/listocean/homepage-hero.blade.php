@extends('layouts.app')

@section('header-title', __('Homepage Hero'))
@section('header-subtitle', __('Control what appears in the front-page hero section'))

@section('content')
<div class="page-title mb-4">
    <div class="d-flex align-items-center gap-2">
        <i class="fa-solid fa-image text-primary"></i>
        <span class="fw-semibold fs-5">{{ __('Homepage Hero') }}</span>
    </div>
    <p class="text-muted small mb-0 mt-1">{{ __('Manage the hero background images and display settings.') }}</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.homepageHero.update') }}" method="POST">
    @csrf

    {{-- Display Settings --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
            <i class="fa-solid fa-sliders text-primary"></i>
            <span class="fw-semibold">{{ __('Display Settings') }}</span>
        </div>
        <div class="card-body">
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="hero_enabled" id="hero_enabled"
                       value="1" @checked((int)($heroEnabled ?? 1) === 1)>
                <label class="form-check-label fw-medium" for="hero_enabled">
                    {{ __('Show hero section on homepage') }}
                </label>
                <div class="text-muted small mt-1">{{ __('Disable to hide the entire hero section (search box + promo image) from the frontend.') }}</div>
            </div>
            <div class="row g-3">
                <div class="col-sm-4">
                    <label class="form-label fw-medium">{{ __('Padding Top') }} <span class="text-muted fw-normal">(px)</span></label>
                    <input type="number" class="form-control" name="padding_top"
                           value="{{ (int)($paddingTop ?? 0) }}" min="0" max="300" placeholder="60">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">{{ __('Padding Bottom') }} <span class="text-muted fw-normal">(px)</span></label>
                    <input type="number" class="form-control" name="padding_bottom"
                           value="{{ (int)($paddingBottom ?? 0) }}" min="0" max="300" placeholder="35">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">{{ __('Background Focal Point') }}</label>
                    <input type="text" class="form-control" name="background_position"
                           value="{{ $backgroundPosition ?? '' }}" placeholder="center center">
                    <div class="form-text">{{ __('e.g. top center · 50% 30%') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hero Background Images --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-panorama text-primary"></i>
                <span class="fw-semibold">{{ __('Hero Background Images') }}</span>
                <span class="ms-auto badge bg-secondary-subtle text-secondary fw-normal small">{{ __('Recommended: 1900 × 670 px') }}</span>
            </div>
            <p class="text-muted small mb-0 mt-1">{{ __('Upload up to 3 backgrounds. When multiple are set the hero auto-rotates between them. Leave a slot empty to keep its current image.') }}</p>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @php
                    $bgSlots = [
                        ['label' => __('Background #1'), 'badge' => 'primary', 'badgeText' => __('Primary'), 'name' => 'background_image', 'url' => $currentImageUrl ?? null],
                        ['label' => __('Background #2'), 'badge' => 'secondary', 'badgeText' => __('Optional'), 'name' => 'background_image_2', 'url' => $currentImageUrl2 ?? null],
                        ['label' => __('Background #3'), 'badge' => 'secondary', 'badgeText' => __('Optional'), 'name' => 'background_image_3', 'url' => $currentImageUrl3 ?? null],
                    ];
                @endphp
                @foreach($bgSlots as $slot)
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100" style="background:#f9fafc">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-{{ $slot['badge'] }}-subtle text-{{ $slot['badge'] }}">{{ $slot['badgeText'] }}</span>
                            <span class="text-muted small">{{ $slot['label'] }}</span>
                        </div>
                        @if(!empty($slot['url']))
                            <div class="rounded-2 overflow-hidden border mb-3" style="height:140px">
                                <img src="{{ $slot['url'] }}" alt="" class="w-100 h-100" style="object-fit:cover">
                            </div>
                            <div class="text-success small mb-3 d-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> {{ __('Image saved') }}
                            </div>
                        @else
                            <div class="rounded-2 border d-flex align-items-center justify-content-center mb-3 bg-white" style="height:140px; border-style:dashed !important">
                                <div class="text-center text-muted">
                                    <i class="fa-regular fa-image fa-2x mb-1 d-block"></i>
                                    <span class="small">{{ __('No image set') }}</span>
                                </div>
                            </div>
                        @endif
                        @error($slot['name'])<p class="text-danger small mb-2">{{ $message }}</p>@enderror
                        <x-image-picker name="{{ $slot['name'] }}" />
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end pb-2 mb-5">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
            <i class="fa-solid fa-floppy-disk me-2"></i>{{ __('Save Changes') }}
        </button>
    </div>
</form>

@endsection
