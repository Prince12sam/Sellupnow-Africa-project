@extends('layouts.app')

@section('header-title', __('Escrow') . ' #' . $row->id)

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Escrow Transaction') }} <span class="text-muted">#{{ $row->id }}</span></h4>
        <a href="{{ route('admin.escrow.index') }}" class="btn btn-secondary">
            &larr; {{ __('Back to list') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $meta       = $statusMeta[$row->status] ?? ['label' => $row->status, 'class' => 'bg-secondary'];
            $isClosed   = in_array($row->status, ['released', 'refunded']);
            $isActionable = in_array($row->status, ['funded', 'seller_confirmed', 'seller_delivered', 'disputed']);
            $customerWebUrl = rtrim(env('CUSTOMER_WEB_URL', config('app.url')), '/');
        @endphp

        <div class="row g-3">
            {{-- Left: transaction detail --}}
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">{{ __('Transaction Details') }}</h5>
                        <span class="badge {{ $meta['class'] }} fs-6">{{ __($meta['label']) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 p-3 text-center">
                                    <div class="text-muted small">{{ __('Listing Price') }}</div>
                                    <div class="fs-4 fw-bold">{{ number_format((float)$row->listing_price, 2) }} {{ $row->currency }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light border-0 p-3 text-center">
                                    <div class="text-muted small">{{ __('Admin Fee') }} ({{ number_format((float)($row->admin_fee_percent ?? 0), 1) }}%)</div>
                                    <div class="fs-5 fw-semibold">{{ number_format((float)$row->admin_fee_amount, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light border-0 p-3 text-center">
                                    <div class="text-muted small">{{ __('Total Charged') }}</div>
                                    <div class="fs-5 fw-semibold text-primary">{{ number_format((float)$row->total_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <th style="width:170px" class="text-muted ps-0">{{ __('Listing') }}</th>
                                    <td>
                                        <a href="{{ $customerWebUrl }}/listing/{{ $row->listing_slug ?? $row->listing_id }}" target="_blank" rel="noopener">
                                            {{ $row->listing_title ?? '#' . $row->listing_id }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Payment Gateway') }}</th>
                                    <td>{{ $row->payment_gateway ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Transaction ID') }}</th>
                                    <td><code>{{ $row->payment_transaction_id ?? '—' }}</code></td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Created') }}</th>
                                    <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y, H:i') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Funded At') }}</th>
                                    <td>{{ $row->funded_at ? \Carbon\Carbon::parse($row->funded_at)->format('d M Y, H:i') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Seller Accept Deadline') }}</th>
                                    <td>
                                        {{ $row->seller_accept_deadline_at ? \Carbon\Carbon::parse($row->seller_accept_deadline_at)->format('d M Y, H:i') : '—' }}
                                        @if($row->seller_accept_deadline_at && !$row->seller_accepted_at && in_array($row->status, ['funded']))
                                            @php $dl = \Carbon\Carbon::parse($row->seller_accept_deadline_at); @endphp
                                            @if($dl->isPast())
                                                <span class="badge bg-danger ms-1">{{ __('Overdue') }}</span>
                                            @else
                                                <span class="text-muted ms-1">({{ $dl->diffForHumans() }})</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Seller Accepted At') }}</th>
                                    <td>{{ $row->seller_accepted_at ? \Carbon\Carbon::parse($row->seller_accepted_at)->format('d M Y, H:i') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Seller Delivered At') }}</th>
                                    <td>{{ $row->seller_delivered_at ? \Carbon\Carbon::parse($row->seller_delivered_at)->format('d M Y, H:i') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Buyer Confirm Deadline') }}</th>
                                    <td>
                                        {{ $row->buyer_confirm_deadline_at ? \Carbon\Carbon::parse($row->buyer_confirm_deadline_at)->format('d M Y, H:i') : '—' }}
                                        @if($row->buyer_confirm_deadline_at && !$row->buyer_confirmed_at && $row->status === 'seller_delivered')
                                            @php $dl2 = \Carbon\Carbon::parse($row->buyer_confirm_deadline_at); @endphp
                                            @if($dl2->isPast())
                                                <span class="badge bg-warning text-dark ms-1">{{ __('Deadline passed') }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Buyer Confirmed At') }}</th>
                                    <td>{{ $row->buyer_confirmed_at ? \Carbon\Carbon::parse($row->buyer_confirmed_at)->format('d M Y, H:i') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted ps-0">{{ __('Released At') }}</th>
                                    <td>{{ $row->released_at ? \Carbon\Carbon::parse($row->released_at)->format('d M Y, H:i') : '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Event timeline --}}
                <div class="card mt-3">
                    <div class="card-header py-3">
                        <h5 class="card-title m-0">{{ __('Event Timeline') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">{{ __('Event') }}</th>
                                        <th>{{ __('Actor') }}</th>
                                        <th>{{ __('Status Change') }}</th>
                                        <th>{{ __('Note') }}</th>
                                        <th>{{ __('Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($events as $ev)
                                        <tr>
                                            <td class="ps-3">
                                                <code>{{ $ev->event }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $ev->actor_type }}</span>
                                                @if($ev->actor_name)
                                                    <div><small>{{ $ev->actor_name }}</small></div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ev->from_status)
                                                    <small class="text-muted">{{ $ev->from_status }}</small>
                                                    <span class="mx-1">→</span>
                                                @endif
                                                @if($ev->to_status)
                                                    <small>{{ $ev->to_status }}</small>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $ev->note ?? '—' }}</small></td>
                                            <td>
                                                <small>{{ $ev->created_at ? \Carbon\Carbon::parse($ev->created_at)->format('d M H:i') : '—' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">{{ __('No events recorded.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: parties + admin actions --}}
            <div class="col-lg-5">
                {{-- Buyer --}}
                <div class="card mb-3">
                    <div class="card-header py-3">
                        <h6 class="card-title m-0">{{ __('Buyer') }}</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted ps-0" style="width:80px">{{ __('Name') }}</th>
                                <td>{{ $row->buyer_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">{{ __('Email') }}</th>
                                <td>{{ $row->buyer_email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">{{ __('Phone') }}</th>
                                <td>{{ $row->buyer_phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">{{ __('ID') }}</th>
                                <td>#{{ $row->buyer_user_id }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Seller --}}
                <div class="card mb-3">
                    <div class="card-header py-3">
                        <h6 class="card-title m-0">{{ __('Seller') }}</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted ps-0" style="width:80px">{{ __('Name') }}</th>
                                <td>{{ $row->seller_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">{{ __('Email') }}</th>
                                <td>{{ $row->seller_email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">{{ __('Phone') }}</th>
                                <td>{{ $row->seller_phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted ps-0">{{ __('ID') }}</th>
                                <td>#{{ $row->seller_user_id }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Admin actions --}}
                @if($isActionable)
                    <div class="card border-warning">
                        <div class="card-header py-3 bg-warning bg-opacity-10">
                            <h6 class="card-title m-0 text-warning">{{ __('Admin Actions') }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                {{ __('These actions are irreversible. Use only after reviewing the dispute or when a deadline has passed.') }}
                            </p>

                            {{-- Release to seller --}}
                            <form method="POST" action="{{ route('admin.escrow.release', $row->id) }}" class="mb-3">
                                @csrf
                                <label class="form-label fw-semibold text-success">
                                    {{ __('Release funds to seller') }}
                                </label>
                                <input type="text" name="note" class="form-control form-control-sm mb-2"
                                       placeholder="{{ __('Optional note (e.g. buyer confirm deadline passed)') }}">
                                <button type="submit" class="btn btn-success btn-sm w-100"
                                        onclick="return confirm('{{ __('Release funds to seller? This cannot be undone.') }}')">
                                    <i class="fa fa-check me-1"></i> {{ __('Release to Seller Wallet') }}
                                </button>
                            </form>

                            <hr class="my-2">

                            {{-- Refund buyer --}}
                            <form method="POST" action="{{ route('admin.escrow.refund', $row->id) }}" class="mb-3">
                                @csrf
                                <label class="form-label fw-semibold text-danger">
                                    {{ __('Refund buyer') }}
                                </label>
                                <div class="text-muted small mb-1">
                                    {{ __('Marks escrow as refunded. Issue the actual Paystack refund in your Paystack dashboard separately.') }}
                                </div>
                                <input type="text" name="note" class="form-control form-control-sm mb-2"
                                       placeholder="{{ __('Optional note (e.g. seller failed to deliver)') }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                        onclick="return confirm('{{ __('Mark as refunded? You must also process the Paystack refund manually.') }}')">
                                    <i class="fa fa-undo me-1"></i> {{ __('Mark as Refunded') }}
                                </button>
                            </form>

                            @if($row->status !== 'disputed')
                                <hr class="my-2">

                                {{-- Flag as disputed --}}
                                <form method="POST" action="{{ route('admin.escrow.dispute', $row->id) }}">
                                    @csrf
                                    <label class="form-label fw-semibold">
                                        {{ __('Flag as Disputed') }}
                                    </label>
                                    <input type="text" name="note" class="form-control form-control-sm mb-2"
                                           placeholder="{{ __('Reason for flagging (optional)') }}">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fa fa-flag me-1"></i> {{ __('Flag Dispute') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @elseif($isClosed)
                    <div class="card">
                        <div class="card-body text-center text-muted py-4">
                            <i class="fa fa-lock fa-2x mb-2 d-block opacity-50"></i>
                            {{ __('This escrow is closed — no admin actions available.') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
