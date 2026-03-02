@extends('frontend.layout.master')
@section('site_title')
    {{ __('My Wallet') }}
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

                                {{-- Balance Card --}}
                                <div class="relevant-ads box-shadow1 p-24 mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <p class="text-muted mb-1" style="font-size:13px;">{{ __('Current Balance') }}</p>
                                            <h2 class="mb-0" style="font-size:2rem;font-weight:700;color:var(--main-color-one, #524EB7);">
                                                {{ amount_with_currency_symbol($wallet->balance) }}
                                            </h2>
                                            <p class="text-muted mt-1 mb-0" style="font-size:12px;">
                                                {{ __('Currency') }}: {{ strtoupper($wallet->currency ?? 'USD') }}
                                            </p>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('user.wallet.topup') }}" class="red-btn">
                                                <i class="las la-plus-circle me-1"></i> {{ __('Top Up Wallet') }}
                                            </a>
                                            <a href="{{ route('user.membership.plans') }}" class="cmn-btn">
                                                <i class="las la-star me-1"></i> {{ __('Upgrade Membership') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Transaction History --}}
                                <div class="relevant-ads box-shadow1 p-24">
                                    <h5 class="mb-4" style="font-weight:600;">{{ __('Transaction History') }}</h5>

                                    @if($history->isEmpty())
                                        <div class="text-center py-5 text-muted">
                                            <i class="las la-receipt" style="font-size:3rem;opacity:.3;"></i>
                                            <p class="mt-2">{{ __('No transactions yet.') }}</p>
                                            <a href="{{ route('user.wallet.topup') }}" class="red-btn mt-2 d-inline-block">{{ __('Make your first top-up') }}</a>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('Date') }}</th>
                                                        <th>{{ __('Note') }}</th>
                                                        <th>{{ __('Type') }}</th>
                                                        <th class="text-end">{{ __('Amount') }}</th>
                                                        <th class="text-end">{{ __('Balance After') }}</th>
                                                        <th class="text-center">{{ __('Reference') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($history as $tx)
                                                        <tr>
                                                            <td style="white-space:nowrap;font-size:13px;">
                                                                {{ $tx->created_at->format('d M Y, H:i') }}
                                                            </td>
                                                            <td style="font-size:13px;">{{ $tx->note }}</td>
                                                            <td>
                                                                @if($tx->type === 'credit')
                                                                    <span class="badge bg-success">{{ __('Credit') }}</span>
                                                                @else
                                                                    <span class="badge bg-danger">{{ __('Debit') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end" style="font-weight:600;">
                                                                <span class="{{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                                    {{ $tx->type === 'credit' ? '+' : '-' }}{{ amount_with_currency_symbol($tx->amount) }}
                                                                </span>
                                                            </td>
                                                            <td class="text-end" style="font-size:13px;">
                                                                {{ amount_with_currency_symbol($tx->balance_after) }}
                                                            </td>
                                                            <td class="text-center" style="font-size:12px;color:#64748b;">
                                                                {{ $tx->reference_type ? ucwords(str_replace('_', ' ', $tx->reference_type)) : '—' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-end">
                                            {{ $history->links() }}
                                        </div>
                                    @endif
                                </div>

                            </div>{{-- /main-body --}}
                        </div>{{-- /down-body-wraper --}}
                    </div>{{-- /profile-setting-wraper --}}
                </div>
            </div>
        </div>
    </div>
@endsection
