@extends('layouts.app')

@section('header-title', __('Captcha Settings'))

@section('content')
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-xl-8 col-lg-9 m-auto">

                {{-- Live frontend status --}}
                <div class="alert d-flex align-items-center justify-content-between mb-3
                    {{ $frontendCaptchaEnabled ? 'alert-warning' : 'alert-success' }}">
                    <div>
                        @if($frontendCaptchaEnabled)
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>{{ __('Captcha is currently ACTIVE') }}</strong> — {{ __('The signup/login pages require visitors to complete a captcha challenge.') }}
                        @else
                            <i class="fas fa-unlock me-2"></i>
                            <strong>{{ __('Captcha is currently DISABLED') }}</strong> — {{ __('Signup and login pages do NOT require a captcha.') }}
                        @endif
                    </div>
                    <form action="{{ route('admin.googleReCaptcha.resync') }}" method="POST" class="ms-3 mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary"
                            title="{{ __('Force re-sync settings to the frontend') }}">
                            <i class="fas fa-sync-alt me-1"></i>{{ __('Force Re-sync') }}
                        </button>
                    </form>
                </div>

                <form action="{{ route('admin.googleReCaptcha.update') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header py-3">
                            <h4 class="m-0">{{ __('Captcha Configuration') }}</h4>
                        </div>
                        <div class="card-body pb-4">
                            <div class="mb-3 border rounded p-2 d-flex align-items-center gap-2">
                                <label class="m-0 fw-bold">{{ __('Enable Captcha') }}</label>
                                <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                                    data-bs-title="Toggle">
                                    <input name="is_active" type="checkbox" {{ $reCaptcha?->is_active ? 'checked' : '' }} />
                                    <span class="slider round"></span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Captcha Provider') }}</label>
                                <select name="provider" class="form-select">
                                    <option value="google" {{ ($reCaptcha?->provider ?? 'google') === 'google' ? 'selected' : '' }}>{{ __('Google reCAPTCHA') }}</option>
                                    <option value="cloudflare" {{ ($reCaptcha?->provider ?? 'google') === 'cloudflare' ? 'selected' : '' }}>{{ __('Cloudflare Turnstile') }}</option>
                                </select>
                                <div class="form-text">{{ __('Choose which captcha to show on the frontend login/signup pages.') }}</div>
                            </div>

                            <div class="mb-4">
                                <x-input type="text" name="site_key" label="GOOGLE SITE KEY"
                                    placeholder="ex: 6LfrbF0qAAAAAB5hAhrIEmFSSd5_ZN492XsZBvhF" :value="$reCaptcha?->site_key" />
                            </div>

                            <div class="">
                                <x-input type="text" name="secret_key" label="GOOGLE SECRET KEY"
                                    placeholder="ex: 6LfrbF0qAAAAAIVYBH93-R2dJP2gKEp4hHBmRfz8" :value="$reCaptcha?->secret_key" />
                            </div>

                            <hr class="my-4" />

                            <div class="mb-4">
                                <x-input type="text" name="turnstile_site_key" label="CLOUDFLARE TURNSTILE SITE KEY"
                                    placeholder="ex: 0x4AAAAAAABb..." :value="$reCaptcha?->turnstile_site_key" />
                            </div>

                            <div>
                                <x-input type="text" name="turnstile_secret_key" label="CLOUDFLARE TURNSTILE SECRET KEY"
                                    placeholder="ex: 0x4AAAAAAABb..." :value="$reCaptcha?->turnstile_secret_key" />
                            </div>
                        </div>
                        @hasPermission('admin.googleReCaptcha.update')
                            <div class="card-footer py-3 ">
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary py-2">{{ __('Save And Update') }}</button>
                                </div>
                            </div>
                        @endhasPermission
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
