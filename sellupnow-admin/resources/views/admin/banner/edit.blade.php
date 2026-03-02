@extends('layouts.app')

@section('header-title', __('Edit Banner'))

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-image"></i> {{ __('Edit Banner') }}
        </div>
    </div>
    <form action="{{ route('admin.banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">

            <div class="col-md-6">
                <div class="card mt-3 h-100">
                    <div class="card-body">
                        <div class="">
                            <x-input label="Title" name="title" type="text" placeholder="Enter Short Title"
                                :value="$banner->title" />
                        </div>

                        <div class="mt-4">

                            <div>
                                <h5>
                                    {{ __('Banner ') }}
                                    <span class="text-primary bg-light">Ratio (4500 x 2000 px)</span>
                                    <span class="text-danger">*</span>
                                </h5>
                                @error('banner')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <x-image-picker name="banner" :value="$banner->banner" />
                        </div>

                        {{-- ── Homepage Banner Position ─── --}}
                        <div class="mt-4">
                            <label class="form-label fw-bold">{{ __('Position on Homepage') }}</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="position"
                                        id="pos_after" value="after_hero"
                                        {{ ($currentPosition ?? 'after_hero') === 'after_hero' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pos_after">
                                        {{ __('After hero section') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="position"
                                        id="pos_before" value="before_hero"
                                        {{ ($currentPosition ?? '') === 'before_hero' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pos_before">
                                        {{ __('Before hero section (top of page)') }}
                                    </label>
                                </div>
                                <!-- Marketplace position removed — managed in standalone Marketplace Banner -->
                            </div>
                        </div>

                        @if ($businessModel != 'single')
                            <div
                                class="mt-4 border d-inline-flex align-items-center justify-content-center gap-2 p-2 rounded">
                                <label for="forShop" class="form-label mb-0 fw-bold">
                                    {{ __('This Banner For Own Shop') }}
                                </label>
                                <input type="checkbox" name="for_shop" id="forShop" style="width: 20px; height: 20px"
                                    {{ $banner->shop_id ? 'checked' : '' }} class="form-check-input m-0" />
                            </div>
                        @endif

                        <div class="col-12 d-flex justify-content-end mt-4">
                            <button class="btn btn-primary py-2 px-5">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>
@endsection
