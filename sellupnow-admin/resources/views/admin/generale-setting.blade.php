@extends('layouts.app')

@php
    $generaleSetting = $generaleSetting ?? generaleSetting('setting');
    $currencies = $currencies ?? \App\Models\Currency::query()->get();
@endphp

@section('title', __('Admin Settings'))

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="bi bi-gear-fill"></i> {{ __('Admin Settings') }}
            <button class="btn btn-primary btn-sm ms-3" id="runUpdateScript">
                {{ __('Run Latest Update Script') }}
            </button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex align-items-center gap-2 py-3">
            <i class="bi bi-globe"></i>
            <h5 class="mb-0">{{ __('Customer Web SEO / Scripts / Custom CSS/JS') }}</h5>
        </div>
        <div class="card-body">
    <form action="{{ route('admin.generale-setting.website-general-settings') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-4">
                            <div class="col-sm-6">
                                <x-select label="Default Currency" name="currency">
                                    <option value="">
                                        {{ __('Select Currency') }}
                                    </option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            {{ $generaleSetting?->currency_id == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div class="col-sm-6 mt-4 mt-sm-0">
                                <x-select label="Currency Position" name="currency_position">
                                    <option value="prefix"
                                        {{ $generaleSetting?->currency_position == 'prefix' ? 'selected' : '' }}>
                                        {{ __('Prefix') }}
                                    </option>
                                    <option value="suffix"
                                        {{ $generaleSetting?->currency_position == 'suffix' ? 'selected' : '' }}>
                                        {{ __('Suffix') }}
                                    </option>
                                </x-select>
                            </div>
                        </div>

                        <textarea class="form-control" rows="3" name="site_meta_description">{{ old('site_meta_description', $listoceanOptions['site_meta_description'] ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('OG Meta Title') }}</label>
                        <input type="text" class="form-control" name="og_meta_title" value="{{ old('og_meta_title', $listoceanOptions['og_meta_title'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('OG Meta Site Name') }}</label>
                        <input type="text" class="form-control" name="og_meta_site_name" value="{{ old('og_meta_site_name', $listoceanOptions['og_meta_site_name'] ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('OG Meta URL') }}</label>
                        <input type="text" class="form-control" name="og_meta_url" value="{{ old('og_meta_url', $listoceanOptions['og_meta_url'] ?? '') }}" placeholder="https://...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('OG Meta Image') }}</label>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $listoceanOgImageUrl ?? 'https://placehold.co/200x50/png' }}" id="previewListoceanOgImage" alt="" style="max-width: 200px; height: auto;" class="rounded border">
                            <input type="file" class="form-control" name="og_meta_image_upload" accept="image/*" onchange="previewFile(event, 'previewListoceanOgImage')">
                        </div>
                        @error('og_meta_image_upload')
                            <p class="text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('OG Meta Description') }}</label>
                        <textarea class="form-control" rows="3" name="og_meta_description">{{ old('og_meta_description', $listoceanOptions['og_meta_description'] ?? '') }}</textarea>
                    </div>

                    <div class="col-12 mt-2">
                        <hr>
                        <h6 class="mb-2">{{ __('Third Party / Scripts') }}</h6>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Disqus Key') }}</label>
                        <input type="text" class="form-control" name="site_disqus_key" value="{{ old('site_disqus_key', $listoceanOptions['site_disqus_key'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Tawk API Key') }}</label>
                        <input type="text" class="form-control" name="tawk_api_key" value="{{ old('tawk_api_key', $listoceanOptions['tawk_api_key'] ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Google Analytics') }}</label>
                        <textarea class="form-control" rows="2" name="site_google_analytics">{{ old('site_google_analytics', $listoceanOptions['site_google_analytics'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Third Party Tracking Code') }}</label>
                        <textarea class="form-control" rows="2" name="site_third_party_tracking_code">{{ old('site_third_party_tracking_code', $listoceanOptions['site_third_party_tracking_code'] ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Captcha v3 Site Key') }}</label>
                        <input type="text" class="form-control" name="site_google_captcha_v3_site_key" value="{{ old('site_google_captcha_v3_site_key', $listoceanOptions['site_google_captcha_v3_site_key'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Captcha v3 Secret Key') }}</label>
                        <input type="text" class="form-control" name="site_google_captcha_v3_secret_key" value="{{ old('site_google_captcha_v3_secret_key', $listoceanOptions['site_google_captcha_v3_secret_key'] ?? '') }}">
                    </div>

                    <div class="col-12 mt-2">
                        <hr>
                        <h6 class="mb-2">{{ __('Adsense') }}</h6>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="enable_google_adsense" value="1"
                                @checked(!empty(old('enable_google_adsense', $listoceanOptions['enable_google_adsense'] ?? '')))>
                            <label class="form-check-label">{{ __('Enable Google Adsense') }}</label>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">{{ __('Publisher ID') }}</label>
                            <input type="text" class="form-control" name="google_adsense_publisher_id" value="{{ old('google_adsense_publisher_id', $listoceanOptions['google_adsense_publisher_id'] ?? '') }}">
                        </div>
                        <div class="mt-2">
                            <label class="form-label">{{ __('Customer ID') }}</label>
                            <input type="text" class="form-control" name="google_adsense_customer_id" value="{{ old('google_adsense_customer_id', $listoceanOptions['google_adsense_customer_id'] ?? '') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Instagram Access Token') }}</label>
                        <input type="text" class="form-control" name="instagram_access_token" value="{{ old('instagram_access_token', $listoceanOptions['instagram_access_token'] ?? '') }}">
                    </div>

                    <div class="col-12 mt-2">
                        <hr>
                        <h6 class="mb-2">{{ __('Custom CSS / JS') }}</h6>
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('Custom CSS (customer web dynamic-style.css)') }}</label>
                        <textarea class="form-control" rows="10" name="custom_css_area" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">{{ old('custom_css_area', $listoceanCustomCss ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('Custom JS (customer web dynamic-script.js)') }}</label>
                        <textarea class="form-control" rows="10" name="custom_js_area" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">{{ old('custom_js_area', $listoceanCustomJs ?? '') }}</textarea>
                    </div>
                    
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('Update Customer Web Settings') }}</button>
                </div>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.generale-setting.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mt-3">
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="">
                            <x-input type="text" label="Website Name" name="name" placeholder="Enter Website Name"
                                :value="$generaleSetting?->name" />
                        </div>

                        <div class="mt-4">
                            <x-input label="Website Title" name="title" type="text"
                                placeholder="Enter Website Title for title bar" :value="$generaleSetting?->title" />
                        </div>

                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <x-select label="Default Currency" name="currency">
                                    <option value="">
                                        {{ __('Select Currency') }}
                                    </option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            {{ $generaleSetting?->currency_id == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div class="col-sm-6 mt-4 mt-sm-0">
                                <x-select label="Currency Position" name="currency_position">
                                    <option value="prefix"
                                        {{ $generaleSetting?->currency_position == 'prefix' ? 'selected' : '' }}>
                                        {{ __('Prefix') }}
                                    </option>
                                    <option value="suffix"
                                        {{ $generaleSetting?->currency_position == 'suffix' ? 'selected' : '' }}>
                                        {{ __('Suffix') }}
                                    </option>
                                </x-select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-sm-6 mt-4 mt-sm-5">

                                <div class="dropzone-container">
                                    <label for="thumbnail" class="logoRatio">
                                        <img src="{{ $generaleSetting?->logo ?? 'https://placehold.co/200x50/png' }}"
                                            id="previewLogo" alt="" width="100%" class="dropzone-area">
                                    </label>
                                    <div class="mt-3">
                                        <h5>
                                            {{ __('Logo ') }}
                                            <span class="text-primary bg-light">Ratio 4:1 (200 x 50 px)</span>
                                            <span class="text-danger">*</span>
                                        </h5>
                                        @error('logo')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <input id="thumbnail" accept="image/*" type="file" data-crop="true" name="logo"
                                        class="form-control" onchange="previewFile(event, 'previewLogo')"
                                        data-preview="previewLogo" data-width="200" data-height="50">
                                    <small class="text-muted d-block">
                                        {{ __('Supported formats: jpg, jpeg, png') }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-sm-6 mt-4">

                                <div class="dropzone-container">
                                    <label for="Favicon" class="logoFav">
                                        <img src="{{ $generaleSetting?->favicon ?? 'https://placehold.co/300x300/png' }}"
                                            id="previewFavicon" alt="" width="100%" class="dropzone-area">
                                    </label>
                                    <div class="mt-3">
                                        <h5>
                                            {{ __('Favicon ') }}
                                            <span class="text-primary bg-light">Ratio 1:1 (300 x 300 px)</span>
                                            <span class="text-danger">*</span>
                                        </h5>
                                        @error('favicon')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <input id="Favicon" accept="image/*" type="file" data-crop="true" name="favicon"
                                        class="form-control" onchange="previewFile(event, 'previewFavicon')"
                                        data-preview="previewFavicon" data-width="300" data-height="300">
                                    <small class="text-muted d-block">
                                        {{ __('Supported formats: jpg, jpeg, png') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 mt-4">

                        <div class="dropzone-container">
                            <label for="AppIcon" class="AppIcon">
                                <img src="{{ $generaleSetting?->app_logo ?? 'https://placehold.co/300x300/png' }}"
                                    id="previewAppIcon" alt="" width="100%" class="dropzone-area">
                            </label>
                            <div class="mt-3">
                                <h5>
                                    {{ __('App Logo ') }}
                                    <span class="text-primary bg-light">Ratio 1:1 (300 x 300 px)</span>
                                    <span class="text-danger">*</span>
                                </h5>
                                @error('app_logo')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <input id="Applogo" accept="image/*" type="file" data-crop="true" name="app_logo"
                                class="form-control" onchange="previewFile(event, 'previewAppIcon')"
                                data-preview="previewAppIcon" data-width="300" data-height="300">
                            <small class="text-muted d-block">
                                {{ __('Supported formats: jpg, jpeg, png') }}
                            </small>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!--######## Change Login Background ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center gap-2 py-3">
                <i class="bi bi-app-indicator"></i>
                <h5 class="mb-0">
                    {{ __('Change Login Background') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <x-image-picker type="file" name="admin_login_background" label="Login Background" placeholder="Enter Login Background"
                            :value="$generaleSetting?->admin_login_img" />
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <x-image-picker type="file" name="shop_login_background" label="Login Background" placeholder="Enter Login Background"
                            :value="$generaleSetting?->shop_login_img" />
                    </div>
                </div>
            </div>
        </div>


        <!--######## Others Information ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center gap-2 py-3">
                <i class="bi bi-app-indicator"></i>
                <h5 class="mb-0">
                    {{ __('Others Information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <x-input type="number" name="mobile" label="Mobile Number" placeholder="Enter Mobile Number"
                            :value="$generaleSetting?->mobile" />
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0">
                        <x-input type="email" name="email" label="Email Address" placeholder="Enter Email Address"
                            :value="$generaleSetting?->email" />
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0">
                        <x-input type="text" name="address" label="Address" placeholder="Enter Address"
                            :value="$generaleSetting?->address" />
                    </div>

                </div>
            </div>
        </div>

        <!--######## download app link ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-app-indicator"></i>
                    <h5 class="mb-0">
                        {{ __('Download App Link') }}
                    </h5>
                </div>

                <div>
                    <label class="m-0 fw-bold" for="toggle">
                        {{ __('Show/Hide Website Navigation Download App') }}
                    </label>
                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-title="Show/Hide">
                        <input id="toggle" type="checkbox" {{ $generaleSetting?->show_download_app ? 'checked' : '' }}
                            name="show_download_app">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="" class="mb-1">
                            {{ __('Google PlayStore App Link') }}
                        </label>
                        <textarea name="google_playstore_url" class="form-control" rows="3"
                            placeholder="Enter Google PlayStore App Link">{{ $generaleSetting?->google_playstore_url }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="" class="mb-1">
                            {{ __('Apple Store App Link') }}
                        </label>
                        <textarea name="app_store_url" class="form-control" rows="3" placeholder="Enter Apple Store App Link">{{ $generaleSetting?->app_store_url }}</textarea>
                    </div>
                    <div class="col-12 mt-3">
                        <hr>
                    </div>

                    @php
                        $androidBadgeUrl = null;
                        $iosBadgeUrl     = null;
                        try {
                            $loDb   = DB::connection('listocean');
                            $loBase = rtrim(env('LISTOCEAN_BASE_URL', env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090')), '/');

                            $androidBadgeId = $loDb->table('static_options')->where('option_name','android_app_badge')->value('option_value');
                            $iosBadgeId     = $loDb->table('static_options')->where('option_name','ios_app_badge')->value('option_value');

                            if ($androidBadgeId) {
                                $mu = $loDb->table('media_uploads')->where('id', (int)$androidBadgeId)->first();
                                if ($mu && !empty($mu->path)) {
                                    $androidBadgeUrl = $loBase . '/assets/uploads/media-uploader/' . $mu->path;
                                }
                            }
                            if ($iosBadgeId) {
                                $mu = $loDb->table('media_uploads')->where('id', (int)$iosBadgeId)->first();
                                if ($mu && !empty($mu->path)) {
                                    $iosBadgeUrl = $loBase . '/assets/uploads/media-uploader/' . $mu->path;
                                }
                            }
                        } catch (\Throwable $e) {}
                    @endphp

                    <div class="col-md-6 mt-3">
                        <label class="form-label">{{ __('Upload Android Badge (optional)') }}</label>
                        @if($androidBadgeUrl)
                            <div class="mb-2">
                                <img src="{{ $androidBadgeUrl }}" alt="Android Badge" style="height:50px;border-radius:8px;border:1px solid #dee2e6;padding:4px;background:#fff;">
                                <small class="text-success d-block mt-1"><i class="bi bi-check-circle-fill"></i> Currently saved — upload new to replace</small>
                            </div>
                        @endif
                        <input type="file" name="android_app_badge" accept="image/*" class="form-control">
                        <small class="text-muted d-block">Shown on the customer homepage as the Google Play download button.</small>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label">{{ __('Upload iOS Badge (optional)') }}</label>
                        @if($iosBadgeUrl)
                            <div class="mb-2">
                                <img src="{{ $iosBadgeUrl }}" alt="iOS Badge" style="height:50px;border-radius:8px;border:1px solid #dee2e6;padding:4px;background:#fff;">
                                <small class="text-success d-block mt-1"><i class="bi bi-check-circle-fill"></i> Currently saved — upload new to replace</small>
                            </div>
                        @endif
                        <input type="file" name="ios_app_badge" accept="image/*" class="form-control">
                        <small class="text-muted d-block">Shown on the customer homepage as the App Store download button.</small>
                    </div>

                    <div class="col-12 mt-3">
                        <hr>
                    </div>

                </div>
            </div>
        </div>

        <!--######## Footer Information ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap py-3">
                <div class="d-flex align-items-center gap-1">
                    <i class="bi bi-align-bottom"></i>
                    <h5 class="mb-0">
                        {{ __('Footer Section Info') }}
                    </h5>
                </div>

                <div>
                    <label class="m-0 fw-bold" for="toggle">
                        {{ __('Show/Hide Admin Bottom Footer Section') }}
                    </label>
                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-title="Show/Hide">
                        <input id="toggle" type="checkbox" {{ $generaleSetting?->show_footer ? 'checked' : '' }}
                            name="show_footer">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-input type="number" name="footer_phone" label="Hotline Number"
                            placeholder="Enter Mobile Number" :value="$generaleSetting?->footer_phone" />
                    </div>

                    <div class="col-md-6 mt-4 mt-lg-0">
                        <x-input type="text" name="footer_text" label="Footer Text" placeholder="Enter Footer Text"
                            :value="$generaleSetting?->footer_text ?? 'All right reserved by company'" />
                    </div>

                    <div class="col-md-6 mt-4">

                        <div class="dropzone-container">
                            <label for="FooterLogo" class="FooterLogo">
                                <img src="{{ $generaleSetting?->footerLogo ?? 'https://placehold.co/200x50/png' }}"
                                    id="previewFooterLogo" alt="" width="100%" class="dropzone-area">
                            </label>
                            <div class="mt-3">
                                <h5>
                                    {{ __('Frontend Footer Logo') }}
                                    <span class="text-primary bg-light">Ratio 4:1 (200 x 50 px)</span>
                                    <span class="text-danger">*</span>
                                </h5>
                                @error('footer_logo')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <input id="FooterLogo" accept="image/*" type="file" data-crop="true" name="footer_logo"
                                class="form-control" onchange="previewFile(event, 'previewFooterLogo')"
                                data-preview="previewFooterLogo" data-width="200" data-height="50">
                            <small class="text-muted d-block">
                                {{ __('Supported formats: jpg, jpeg, png') }}
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4">

                        <div class="dropzone-container">
                            <label for="thumbnail" class="footerQrCode">
                                <img src="{{ $generaleSetting?->footerQr ?? 'https://placehold.co/200x200/png' }}"
                                    id="footerQrCode" alt="" width="100%" class="dropzone-area">
                            </label>
                            <div class="mt-3">
                                <h5>
                                    {{ __('Frontend Scan the QR') }}
                                    <span class="text-primary bg-light">Ratio 1:1 (200 x 200 px)</span>
                                    <span class="text-danger">*</span>
                                </h5>
                                @error('footer_qrcode')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <input id="footerQrCode" accept="image/*" type="file" data-crop="true"
                                name="footer_qrcode" class="form-control" onchange="previewFile(event, 'footerQrCode')"
                                data-preview="footerQrCode" data-width="200" data-height="200">
                            <small class="text-muted d-block">
                                {{ __('Supported formats: jpg, jpeg, png') }}
                            </small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @hasPermission('admin.generale-setting.update')
            <div class="d-flex justify-content-end mt-4 mb-3">
                <button type="submit" class="btn btn-primary py-2.5 px-3">
                    {{ __('Save And Update') }}
                </button>
            </div>
        @endhasPermission

    </form>

    <form action="{{ route('admin.generale-setting.update.command') }}" method="POST" id="scriptRunForm">
        @csrf
    </form>



@endsection
@push('scripts')
    <script>
        $('#runUpdateScript').click(function() {
            Swal.fire({
                title: "{{ __('Are you sure? want to run update script') }}",
                text: "When you run this script, all data related to the latest version (v{{ config('app.version') }}) will be reset. Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "{{ __('Yes, Run!') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("scriptRunForm").submit();
                }
            });
        })
    </script>
    @if (session('runUpdateScriptError'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                html: `@foreach (session('runUpdateScriptError') as $error)
                    {{ $error }} <br><br>
                @endforeach`,
            });
        </script>
    @endif
@endpush
