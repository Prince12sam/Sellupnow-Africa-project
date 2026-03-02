@extends('frontend.layout.master')
@section('site_title')
    {{ __('My Featured Ads') }}
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

                                {{-- Header + wallet widget --}}
                                <div class="relevant-ads box-shadow1 p-24 mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <h4 class="mb-1">{{ __('My Featured Ads') }}</h4>
                                            <p class="text-muted mb-0" style="font-size:13px;">
                                                {{ __('Wallet balance') }}: <strong>{{ amount_with_currency_symbol($balance) }}</strong>
                                            </p>
                                        </div>
                                        <a href="{{ route('user.featuredAds.packages') }}" class="red-btn">
                                            {{ __('Feature a Listing') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Flash messages --}}
                                @if(session('success'))
                                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                                @endif

                                {{-- Purchases table --}}
                                <div class="relevant-ads box-shadow1 p-24">
                                    <h5 class="mb-3">{{ __('Purchase History') }}</h5>

                                    @if($purchases->isEmpty())
                                        <p class="text-muted">
                                            {{ __('You have no featured ad purchases yet.') }}
                                            <a href="{{ route('user.featuredAds.packages') }}">{{ __('Browse packages →') }}</a>
                                        </p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('Package') }}</th>
                                                        <th>{{ __('Listing ID') }}</th>
                                                        <th>{{ __('Paid') }}</th>
                                                        <th>{{ __('Method') }}</th>
                                                        <th>{{ __('Purchased') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchases as $purchase)
                                                        @php
                                                            $act    = $purchase->activeActivation;
                                                            $active = $act && $act->is_active && $act->ends_at >= now();
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $purchase->id }}</td>
                                                            <td>{{ $purchase->package?->name ?? '—' }}</td>
                                                            <td>{{ $purchase->listing_id }}</td>
                                                            <td>{{ amount_with_currency_symbol($purchase->amount_paid) }}</td>
                                                            <td>{{ ucwords(str_replace('_', ' ', $purchase->payment_method)) }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($purchase->purchased_at)->format('d M Y') }}</td>
                                                            <td>
                                                                @if($active)
                                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                                    <small class="d-block text-muted" style="font-size:11px;">
                                                                        {{ __('Until') }} {{ $act->ends_at->format('d M Y') }}
                                                                    </small>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ __('Expired') }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">
                                            {{ $purchases->links() }}
                                        </div>
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
