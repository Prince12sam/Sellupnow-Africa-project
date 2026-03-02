@extends('layouts.app')

@section('header-title', __('Payment Gateways'))

@section('content')
    <div class="container-fluid mb-3">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="m-0">{{ __('Payment Gateways') }}</h4>
        </div>

        <div class="row">
            @foreach ($paymentGateways as $paymentGateway)
                @php
                    $configs = json_decode((string) $paymentGateway->config, true);
                    $configs = is_array($configs) ? $configs : [];
                @endphp

                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between gap-2 py-3">
                            <p class="paymentTitle m-0">
                                {{ strtoupper($paymentGateway->name) }}
                            </p>

                            <div class="d-flex align-items-center gap-2">
                                <span class="{{ $paymentGateway->is_active ? 'statusOn' : 'statusOff' }}">
                                    {{ $paymentGateway->is_active ? 'On' : 'Off' }}
                                </span>
                                @hasPermission('admin.paymentGateway.toggle')
                                <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                                    data-bs-title="{{ $paymentGateway->is_active ? 'Turn off' : 'Turn on' }}">
                                    <a href="{{ route('admin.paymentGateway.toggle', $paymentGateway->id) }}"
                                        class="confirm">
                                        <input type="checkbox" {{ $paymentGateway->is_active ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </a>
                                </label>
                                @endhasPermission
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="py-2">
                                <img id="preview{{ $paymentGateway->name }}" class="paymentLogo" src="{{ $paymentGateway->logo }}" alt="logo" loading="lazy"/>
                            </div>

                            <form action="{{ route('admin.paymentGateway.update', $paymentGateway->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mt-3">
                                    <x-select name="mode" label="Mode">
                                        <option value="test" {{ $paymentGateway->mode == 'test' ? 'selected' : '' }}>
                                            Test
                                        </option>
                                        <option value="live" {{ $paymentGateway->mode == 'live' ? 'selected' : '' }} {{ app()->environment('local') ? 'disabled' : '' }}>
                                            Live
                                        </option>
                                    </x-select>
                                </div>

                                @foreach ($configs as $key => $value)
                                    @php
                                        $label = ucwords(str_replace('_', ' ', $key));
                                    @endphp
                                    <div class="mt-3">
                                        <x-input :value="is_scalar($value) ? $value : json_encode($value)" name="config[{{ $key }}]" type="text"
                                            placeholder="{{ $label }}" label="{{ $label }}"
                                            required="true" readonly="{{ app()->environment('local') ? 'true' : '' }}"/>
                                    </div>
                                @endforeach

                                <div class="mt-3">
                                    <x-input name="title" type="text" label="Payment Gateway Title" :value="$paymentGateway->title"
                                        required="true" readonly="{{ app()->environment('local') ? 'true' : '' }}"/>
                                </div>

                                <div class="mt-3">
                                    <x-file name="logo" label="Choose Logo" preview="preview{{ $paymentGateway->name }}" />
                                </div>

                                @hasPermission('admin.paymentGateway.update')
                                <div class="mt-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary py-2">
                                        {{__('Save And Update')}}
                                    </button>
                                </div>
                                @endhasPermission
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-12 mb-2">
                <h4 class="m-0">{{ __('Customer Web (ListOcean)') }}</h4>
                <div class="text-muted" style="font-size: 12px;">{{ __('These settings control Paystack checkout for paid membership on the customer website.') }}</div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-2 py-3">
                        <p class="paymentTitle m-0">PAYSTACK</p>
                        @php
                            $lp = $listoceanPaystack ?? null;
                            $lpc = [];
                            if ($lp && !empty($lp->credentials)) {
                                $lpc = json_decode((string) $lp->credentials, true);
                                $lpc = is_array($lpc) ? $lpc : [];
                            }
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <span class="{{ (int)($lp->status ?? 0) === 1 ? 'statusOn' : 'statusOff' }}">
                                {{ (int)($lp->status ?? 0) === 1 ? 'On' : 'Off' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(!($hasListoceanPaymentGatewaysTable ?? false))
                            <div class="alert alert-warning">
                                {{ __('ListOcean payment_gateways table not found. Run ListOcean migrations for the PaymentGateways module, or use .env PAYSTACK_PUBLIC_KEY/PAYSTACK_SECRET_KEY fallback.') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.paymentGateway.website.paystack.update') }}" method="POST">
                            @csrf

                            <div class="mt-3">
                                <label class="form-label">{{ __('Public Key') }}</label>
                                <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $lpc['public_key'] ?? '') }}" {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }} required>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">{{ __('Secret Key') }}</label>
                                <input type="text" name="secret_key" class="form-control" value="{{ old('secret_key', $lpc['secret_key'] ?? '') }}" {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }} required>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">{{ __('Merchant Email') }}</label>
                                <input type="email" name="merchant_email" class="form-control" value="{{ old('merchant_email', $lpc['merchant_email'] ?? '') }}" {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }}>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">{{ __('Currency') }} <span class="text-danger">*</span></label>
                                <select name="currency" class="form-select" {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }} required>
                                    @foreach(['NGN' => 'NGN — Nigerian Naira', 'GHS' => 'GHS — Ghana Cedis (Mobile Money)', 'ZAR' => 'ZAR — South African Rand', 'USD' => 'USD — US Dollar', 'KES' => 'KES — Kenyan Shilling', 'EGP' => 'EGP — Egyptian Pound'] as $code => $label)
                                        <option value="{{ $code }}" {{ old('currency', $lpc['currency'] ?? 'NGN') === $code ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text text-info">{{ __('GHS lets customers pay via MTN, Vodafone & AirtelTigo Mobile Money. Your Paystack account must have GHS enabled (Settings → Business → Currencies).') }}</div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">{{ __('Payment Channels') }} <small class="text-muted">({{ __('leave blank for smart defaults') }})</small></label>
                                @php $savedChannels = $lpc['channels'] ?? []; @endphp
                                @foreach([
                                    'card'          => 'Card',
                                    'mobile_money'  => 'Mobile Money (MTN / Vodafone / AirtelTigo)',
                                    'bank'          => 'Bank',
                                    'bank_transfer' => 'Bank Transfer',
                                    'ussd'          => 'USSD',
                                ] as $ch => $chLabel)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="channels[]" value="{{ $ch }}"
                                           id="psch_{{ $ch }}"
                                           {{ in_array($ch, old('channels', $savedChannels)) ? 'checked' : '' }}
                                           {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="psch_{{ $ch }}">{{ $chLabel }}</label>
                                </div>
                                @endforeach
                                <div class="form-text">{{ __('If nothing is checked, GHS defaults to card + mobile_money + bank automatically.') }}</div>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="test_mode" id="listocean_paystack_test_mode" value="1"
                                       {{ old('test_mode', (int)($lp->test_mode ?? 0) === 1) ? 'checked' : '' }} {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }}>
                                <label class="form-check-label" for="listocean_paystack_test_mode">{{ __('Test Mode') }}</label>
                            </div>

                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="status" id="listocean_paystack_status" value="1"
                                       {{ old('status', (int)($lp->status ?? 0) === 1) ? 'checked' : '' }} {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }}>
                                <label class="form-check-label" for="listocean_paystack_status">{{ __('Enabled') }}</label>
                            </div>

                            @hasPermission('admin.paymentGateway.index')
                            <div class="mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary py-2" {{ !($hasListoceanPaymentGatewaysTable ?? false) ? 'disabled' : '' }}>
                                    {{ __('Save And Update') }}
                                </button>
                            </div>
                            @endhasPermission
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(".confirm").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "Are you sure?",
                text: "You want to change status!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Change it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endpush
