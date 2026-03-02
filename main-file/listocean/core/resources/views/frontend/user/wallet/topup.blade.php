@extends('frontend.layout.master')
@section('site_title')
    {{ __('Top Up Wallet') }}
@endsection
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
                                <x-validation.frontend-error />

                                <div class="relevant-ads box-shadow1 p-24">

                                    <div class="d-flex align-items-center gap-2 mb-4">
                                        <a href="{{ route('user.wallet.index') }}" class="text-muted">
                                            <i class="las la-arrow-left"></i>
                                        </a>
                                        <h5 class="mb-0" style="font-weight:600;">{{ __('Top Up Your Wallet') }}</h5>
                                    </div>

                                    {{-- Current Balance --}}
                                    <div class="alert alert-info mb-4">
                                        {{ __('Current Balance') }}: <strong>{{ amount_with_currency_symbol($wallet->balance) }}</strong>
                                    </div>

                                    @if(!$paystackEnabled)
                                        <div class="alert alert-warning">
                                            <i class="las la-exclamation-triangle me-1"></i>
                                            {{ __('Online payments are currently unavailable. Please contact the administrator.') }}
                                        </div>
                                    @else
                                        <form action="{{ route('user.wallet.topup.submit') }}" method="POST" id="topupForm">
                                            @csrf
                                            <div class="row g-4">

                                                {{-- Amount --}}
                                                <div class="col-md-6">
                                                    <label for="amount" class="form-label fw-semibold">
                                                        {{ __('Amount to Top Up') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <span class="position-absolute fw-bold"
                                                              style="top:50%;transform:translateY(-50%);left:12px;">
                                                            {{ get_static_option('site_currency_symbol') ?? '₵' }}
                                                        </span>
                                                        <input type="number" name="amount" id="amount"
                                                               class="input-filed w-100"
                                                               style="padding-left:2rem;"
                                                               value="{{ old('amount') }}"
                                                               min="1" max="100000" step="0.01"
                                                               placeholder="0.00" required>
                                                    </div>

                                                    {{-- Quick amount buttons --}}
                                                    <div class="d-flex gap-2 flex-wrap mt-2">
                                                        @foreach([50, 100, 200, 500, 1000] as $q)
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary quick-amount"
                                                                    data-amount="{{ $q }}">
                                                                {{ get_static_option('site_currency_symbol') ?? '₵' }}{{ $q }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                {{-- Gateway badge --}}
                                                <div class="col-md-6 d-flex align-items-center">
                                                    <div class="p-3 rounded w-100" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="https://paystack.com/assets/img/favicon.png"
                                                                 alt="Paystack" width="32" height="32"
                                                                 style="border-radius:6px;"
                                                                 onerror="this.style.display='none'">
                                                            <div>
                                                                <p class="mb-0 fw-semibold">{{ __('Secure Payment via Paystack') }}</p>
                                                                <small class="text-muted">
                                                                    {{ __('Card · Bank Transfer · Mobile Money') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <button type="submit" class="red-btn" id="payBtn">
                                                        <i class="las la-lock me-1"></i>
                                                        {{ __('Proceed to Secure Checkout') }}
                                                    </button>
                                                    <a href="{{ route('user.wallet.index') }}" class="cmn-btn ms-2">
                                                        {{ __('Cancel') }}
                                                    </a>
                                                </div>

                                            </div>
                                        </form>
                                    @endif

                                </div>

                            </div>{{-- /main-body --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    // Quick amount selector
    document.querySelectorAll('.quick-amount').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('amount').value = this.dataset.amount;
        });
    });

    // Prevent double-submit
    document.getElementById('topupForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('payBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="las la-spinner la-spin me-1"></i> {{ __("Redirecting to Paystack...") }}';
        }
    });
</script>
@endsection
