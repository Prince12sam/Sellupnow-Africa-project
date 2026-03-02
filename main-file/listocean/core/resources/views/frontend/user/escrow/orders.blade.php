@extends('frontend.layout.master')
@section('site_title') {{ __('Escrow Orders') }} @endsection
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
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h4 style="font-weight:700;">{{ __('Escrow Orders') }}</h4>
                                </div>

                                {{-- Tabs --}}
                                <ul class="nav nav-tabs mb-4" id="escrowTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $activeTab === 'buyer' ? 'active' : '' }}"
                                           href="{{ route('user.escrow.orders', ['tab' => 'buyer']) }}">
                                            <i class="las la-shopping-cart me-1"></i>{{ __('As Buyer') }}
                                            @if($buyerOrders->total())
                                                <span class="badge bg-secondary ms-1">{{ $buyerOrders->total() }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $activeTab === 'seller' ? 'active' : '' }}"
                                           href="{{ route('user.escrow.orders', ['tab' => 'seller']) }}">
                                            <i class="las la-store me-1"></i>{{ __('As Seller') }}
                                            @if($sellerOrders->total())
                                                <span class="badge bg-secondary ms-1">{{ $sellerOrders->total() }}</span>
                                            @endif
                                        </a>
                                    </li>
                                </ul>

                                @php
                                    $orders   = $activeTab === 'buyer' ? $buyerOrders : $sellerOrders;
                                    $isBuyer  = $activeTab === 'buyer';
                                    $statusColors = [
                                        'payment_pending'   => 'secondary',
                                        'funded'            => 'info',
                                        'seller_confirmed'  => 'primary',
                                        'seller_delivered'  => 'warning',
                                        'released'          => 'success',
                                        'refunded'          => 'danger',
                                        'disputed'          => 'danger',
                                    ];
                                @endphp

                                @if($orders->count())
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead style="background:#f8fafc;">
                                                <tr>
                                                    <th>{{ __('Listing') }}</th>
                                                    <th>{{ $isBuyer ? __('Seller') : __('Buyer') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($orders as $order)
                                                @php
                                                    $color = $statusColors[$order->status] ?? 'secondary';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div style="font-weight:600;max-width:200px;" class="text-truncate">
                                                            {{ $order->listing_title ?? __('Listing removed') }}
                                                        </div>
                                                    </td>
                                                    <td>{{ $order->counterparty_name ?? '—' }}</td>
                                                    <td class="fw-semibold">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $color }}">
                                                            {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-muted" style="font-size:12px;">
                                                        {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('user.escrow.detail', $order->id) }}"
                                                           class="cmn-btn" style="padding:5px 14px;font-size:12px;">
                                                            {{ __('View') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">{{ $orders->appends(['tab' => $activeTab])->links() }}</div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="las la-box" style="font-size:48px;color:#cbd5e1;"></i>
                                        <p class="text-muted mt-2">{{ $isBuyer ? __('You have not made any escrow purchases yet.') : __('No orders placed with you via escrow yet.') }}</p>
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
