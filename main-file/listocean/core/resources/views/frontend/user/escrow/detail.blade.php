@extends('frontend.layout.master')
@section('site_title') {{ __('Escrow Order #:id', ['id' => $tx->id]) }} @endsection
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

                            @php
                                $isBuyer  = auth()->id() == $tx->buyer_user_id;
                                $isSeller = auth()->id() == $tx->seller_user_id;
                                $statusColors = [
                                    'payment_pending'   => 'secondary',
                                    'funded'            => 'info',
                                    'seller_confirmed'  => 'primary',
                                    'seller_delivered'  => 'warning',
                                    'released'          => 'success',
                                    'refunded'          => 'danger',
                                    'disputed'          => 'danger',
                                ];
                                $color = $statusColors[$tx->status] ?? 'secondary';
                            @endphp

                            <div class="relevant-ads box-shadow1 p-24 mb-4">
                                {{-- Back + heading --}}
                                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="{{ route('user.escrow.orders') }}" class="text-muted" style="font-size:22px;" title="{{ __('Back') }}">
                                            <i class="las la-arrow-left"></i>
                                        </a>
                                        <div>
                                            <h4 class="mb-0" style="font-weight:700;">{{ __('Order #:id', ['id' => $tx->id]) }}</h4>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y, H:i') }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $color }}" style="font-size:14px;padding:7px 14px;">
                                        {{ str_replace('_', ' ', ucfirst($tx->status)) }}
                                    </span>
                                </div>

                                {{-- Listing card --}}
                                <div class="d-flex align-items-center gap-3 p-3 mb-4"
                                     style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;">
                                    @if(!empty($tx->listing_image))
                                        <img src="{{ get_image_url_id_wise($tx->listing_image) }}" alt="{{ $tx->listing_title }}"
                                             style="width:70px;height:70px;object-fit:cover;border-radius:8px;" loading="lazy">
                                    @endif
                                    <div>
                                        <div style="font-weight:600;">{{ $tx->listing_title ?? __('Listing removed') }}</div>
                                        @if(!empty($tx->listing_slug))
                                            <a href="{{ route('frontend.listing.details', $tx->listing_slug) }}"
                                               class="text-muted" style="font-size:12px;" target="_blank">
                                                {{ __('View listing') }} <i class="las la-external-link-alt"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Party + amounts --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="p-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                                            <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Buyer') }}</div>
                                            <div style="font-weight:600;">{{ $tx->buyer_name ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="p-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                                            <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Seller') }}</div>
                                            <div style="font-weight:600;">{{ $tx->seller_name ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="p-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                                            <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Listing price') }}</div>
                                            <div style="font-weight:600;">{{ amount_with_currency_symbol($tx->listing_price) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="p-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
                                            <div class="text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Total paid') }}</div>
                                            <div style="font-weight:600;color:var(--main-color-one,#524EB7);">{{ amount_with_currency_symbol($tx->total_amount) }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Action buttons ── --}}
                                @if(!in_array($tx->status, ['released','refunded']))
                                    <div class="d-flex flex-wrap gap-2 mb-4">

                                        {{-- SELLER actions --}}
                                        @if($isSeller && $tx->status === 'funded')
                                            <form action="{{ route('user.escrow.accept', $tx->id) }}" method="POST">
                                                @csrf
                                                <button class="cmn-btn">
                                                    <i class="las la-check me-1"></i>{{ __('Accept Order') }}
                                                </button>
                                            </form>
                                        @endif
                                        @if($isSeller && $tx->status === 'seller_confirmed')
                                            <form action="{{ route('user.escrow.deliver', $tx->id) }}" method="POST">
                                                @csrf
                                                <button class="cmn-btn">
                                                    <i class="las la-truck me-1"></i>{{ __('Mark as Delivered') }}
                                                </button>
                                            </form>
                                        @endif

                                        {{-- BUYER actions --}}
                                        @if($isBuyer && $tx->status === 'seller_delivered')
                                            <form action="{{ route('user.escrow.confirm', $tx->id) }}" method="POST">
                                                @csrf
                                                <button class="cmn-btn"
                                                        onclick="return confirm('{{ __('Release payment to seller? This cannot be undone.') }}')">
                                                    <i class="las la-check-double me-1"></i>{{ __('Confirm Receipt') }}
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Dispute (buyer only, while active) --}}
                                        @if($isBuyer && in_array($tx->status, ['funded','seller_confirmed','seller_delivered']))
                                            <button class="red-btn" id="disputeToggle"
                                                    onclick="document.getElementById('disputeForm').classList.toggle('d-none')">
                                                <i class="las la-exclamation-triangle me-1"></i>{{ __('Raise Dispute') }}
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Dispute form --}}
                                    @if($isBuyer && in_array($tx->status, ['funded','seller_confirmed','seller_delivered']))
                                        <div id="disputeForm" class="d-none mb-4 p-3"
                                             style="background:#fff8f0;border-radius:8px;border:1px solid #fed7aa;">
                                            <form action="{{ route('user.escrow.dispute', $tx->id) }}" method="POST">
                                                @csrf
                                                <label class="form-label fw-semibold">{{ __('Reason for dispute') }}</label>
                                                <textarea name="note" rows="3" class="form-control mb-2"
                                                          placeholder="{{ __('Describe the issue…') }}" required></textarea>
                                                <button type="submit" class="red-btn">
                                                    {{ __('Submit Dispute') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endif

                                {{-- Deadlines --}}
                                @if($tx->seller_accept_deadline_at && $tx->status === 'funded')
                                    <p class="text-muted mb-4" style="font-size:12px;">
                                        <i class="las la-clock me-1"></i>
                                        {{ __('Seller must accept by :date', ['date' => \Carbon\Carbon::parse($tx->seller_accept_deadline_at)->format('d M Y, H:i')]) }}
                                    </p>
                                @endif
                                @if($tx->buyer_confirm_deadline_at && $tx->status === 'seller_delivered')
                                    <p class="text-muted mb-4" style="font-size:12px;">
                                        <i class="las la-clock me-1"></i>
                                        {{ __('Auto-release on :date if not confirmed', ['date' => \Carbon\Carbon::parse($tx->buyer_confirm_deadline_at)->format('d M Y, H:i')]) }}
                                    </p>
                                @endif

                                {{-- ── Event timeline ── --}}
                                @if($events->count())
                                    <h5 class="mb-3" style="font-weight:700;">{{ __('Order Timeline') }}</h5>
                                    <ul class="list-unstyled" style="position:relative;padding-left:28px;">
                                        <div style="position:absolute;left:9px;top:0;bottom:0;width:2px;background:#e2e8f0;"></div>
                                        @foreach($events as $ev)
                                            <li class="mb-3" style="position:relative;">
                                                <span style="position:absolute;left:-24px;top:2px;width:12px;height:12px;border-radius:50%;background:var(--main-color-one,#524EB7);border:2px solid #fff;box-shadow:0 0 0 2px #e2e8f0;"></span>
                                                <div style="font-weight:600;font-size:14px;">
                                                    {{ str_replace('_', ' ', ucfirst($ev->event)) }}
                                                </div>
                                                @if($ev->note)
                                                    <div class="text-muted" style="font-size:13px;">{{ $ev->note }}</div>
                                                @endif
                                                <div class="text-muted" style="font-size:11px;">
                                                    {{ \Carbon\Carbon::parse($ev->created_at)->format('d M Y, H:i') }}
                                                    ({{ ucfirst($ev->actor_type ?? '') }})
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
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
