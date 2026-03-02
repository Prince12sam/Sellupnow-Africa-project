@extends('frontend.layout.master')
@section('site_title') {{ __('Secure Escrow') }} @endsection
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

                            <div class="relevant-ads box-shadow1 p-24 mb-4">
                                <h4 class="mb-1" style="font-weight:700;">{{ __('Buy with Escrow') }}</h4>
                                <p class="text-muted mb-4" style="font-size:13px;">
                                    {{ __('Your payment is held securely until you confirm delivery. Only released when you are satisfied.') }}
                                </p>

                                {{-- Listing preview --}}
                                <div class="d-flex align-items-center gap-3 p-3 mb-4" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">
                                    @if(!empty($listing->image))
                                        <img src="{{ get_image_url_id_wise($listing->image) }}" alt="{{ $listing->title }}"
                                             style="width:70px;height:70px;object-fit:cover;border-radius:8px;" loading="lazy">
                                    @endif
                                    <div>
                                        <div style="font-weight:600;">{{ $listing->title }}</div>
                                        <div class="text-muted" style="font-size:13px;">{{ __('Sold by') }}: {{ $listing->user->name ?? '' }}</div>
                                    </div>
                                </div>

                                {{-- Price breakdown --}}
                                <div class="mb-4">
                                    <table class="table table-borderless mb-0" style="max-width:400px;">
                                        <tr>
                                            <td class="ps-0 text-muted">{{ __('Listing price') }}</td>
                                            <td class="text-end fw-semibold">{{ amount_with_currency_symbol($price) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 text-muted">{{ __('Platform fee') }}</td>
                                            <td class="text-end fw-semibold">{{ amount_with_currency_symbol($commission) }}</td>
                                        </tr>
                                        <tr style="border-top:2px solid #e2e8f0;">
                                            <td class="ps-0 fw-bold" style="font-size:16px;">{{ __('Total') }}</td>
                                            <td class="text-end fw-bold" style="font-size:16px;color:var(--main-color-one,#524EB7);">{{ amount_with_currency_symbol($total) }}</td>
                                        </tr>
                                    </table>
                                </div>

                                {{-- Wallet balance check --}}
                                @php $walletBalance = (new \App\Services\WalletService)->balance(auth()->id()); @endphp
                                @if($walletBalance < $total)
                                    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" style="font-size:13px;">
                                        <i class="las la-exclamation-triangle"></i>
                                        {{ __('Your wallet balance (:balance) is insufficient for this purchase. Please top up first.', ['balance' => amount_with_currency_symbol($walletBalance)]) }}
                                    </div>
                                    <a href="{{ route('user.wallet.topup') }}" class="red-btn">
                                        <i class="las la-wallet me-1"></i> {{ __('Top Up Wallet') }}
                                    </a>
                                @else
                                    <p class="text-success mb-3" style="font-size:13px;">
                                        <i class="las la-check-circle me-1"></i>
                                        {{ __('Wallet balance: :bal', ['bal' => amount_with_currency_symbol($walletBalance)]) }}
                                    </p>
                                    <form action="{{ route('user.escrow.checkout') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                        <button type="submit" class="red-btn"
                                                onclick="return confirm('{{ __('Confirm escrow purchase of :amt?', ['amt' => amount_with_currency_symbol($total)]) }}')">
                                            <i class="las la-lock me-1"></i>
                                            {{ __('Pay :amt Securely', ['amt' => amount_with_currency_symbol($total)]) }}
                                        </button>
                                    </form>
                                @endif

                                <div class="mt-4 p-3" style="background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;font-size:12px;color:#166534;">
                                    <i class="las la-shield-alt me-1"></i>
                                    {{ __('Your money is protected. Funds are only released when you confirm receipt.') }}
                                </div>
                            </div>

                        </div>{{-- /main-body --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
