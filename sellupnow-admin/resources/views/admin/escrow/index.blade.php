@extends('layouts.app')

@section('header-title', __('Escrow Transactions'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Escrow Transactions') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('admin.escrow.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <select class="form-control" name="status">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>{{ __('All Statuses') }}</option>
                            @foreach($statusMeta as $key => $meta)
                                <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>
                                    {{ __($meta['label']) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input class="form-control" type="text" name="search"
                               value="{{ request('search') }}"
                               placeholder="{{ __('Search by ID, listing title or transaction ID…') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.escrow.index') }}" class="btn btn-secondary w-100">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary badges --}}
        <div class="row g-2 mb-3">
            @php
                $badgeGroups = [
                    'payment_pending'  => ['label' => __('Payment Pending'), 'colour' => 'secondary'],
                    'funded'           => ['label' => __('Funded'),           'colour' => 'info'],
                    'seller_confirmed' => ['label' => __('Seller Accepted'),  'colour' => 'primary'],
                    'seller_delivered' => ['label' => __('Seller Delivered'), 'colour' => 'warning'],
                    'released'         => ['label' => __('Released'),         'colour' => 'success'],
                    'disputed'         => ['label' => __('Disputed'),         'colour' => 'danger'],
                    'refunded'         => ['label' => __('Refunded'),         'colour' => 'danger'],
                ];
            @endphp
            @foreach($badgeGroups as $s => $info)
                <div class="col-auto">
                    <a href="{{ route('admin.escrow.index', ['status' => $s]) }}"
                       class="badge text-decoration-none bg-{{ $info['colour'] }} {{ $status === $s ? 'opacity-100' : 'opacity-50' }} px-3 py-2 fs-6">
                        {{ $info['label'] }}
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-header py-3">
                <h5 class="card-title m-0">
                    {{ __('Transactions') }}
                    <small class="text-muted fw-normal ms-2">{{ $transactions->total() }} {{ __('total') }}</small>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">#</th>
                                <th>{{ __('Listing') }}</th>
                                <th>{{ __('Buyer') }}</th>
                                <th>{{ __('Seller') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-end pe-3">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                                @php
                                    $meta = $statusMeta[$t->status] ?? ['label' => $t->status, 'class' => 'bg-secondary'];
                                @endphp
                                <tr>
                                    <td class="ps-3">{{ $t->id }}</td>
                                    <td>
                                        <div>#{{ $t->listing_id }}</div>
                                        <small class="text-muted">{{ \Illuminate\Support\Str::limit((string)($t->listing_title ?? ''), 40) }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $t->buyer_name ?? __('Unknown') }}</div>
                                        <small class="text-muted">#{{ $t->buyer_user_id }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $t->seller_name ?? __('Unknown') }}</div>
                                        <small class="text-muted">#{{ $t->seller_user_id }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ number_format((float)$t->total_amount, 2) }} {{ $t->currency }}</strong>
                                        @if((float)$t->admin_fee_amount > 0)
                                            <div><small class="text-muted">{{ __('Fee') }}: {{ number_format((float)$t->admin_fee_amount, 2) }}</small></div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $meta['class'] }}">{{ __($meta['label']) }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d M Y') : '—' }}</small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.escrow.show', $t->id) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        {{ __('No escrow transactions found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
@endsection
