@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <div>
            <h4 class="mb-0">{{ __('Wallet (Customer Web)') }}</h4>
            <div class="text-muted" style="font-size: 12px;">{{ __('Search a Customer Web user and credit/debit their wallet balance. Every change is recorded in wallet history.') }}</div>
        </div>
    </div>

    <div class="container-fluid mt-3">
        @if(!($hasUsersTable ?? false))
            <div class="alert alert-danger">
                {{ __('Customer Web users table is not accessible. Please check the listocean DB connection.') }}
            </div>
        @endif
        @if(!($hasWalletTable ?? false) || !($hasHistoryTable ?? false))
            <div class="alert alert-warning">
                {{ __('Customer Web wallet tables are missing (wallets / wallet_histories). Run the customer web wallet migrations first.') }}
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.siteWallet.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Search User') }}</label>
                        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="{{ __('Enter User ID, email, or username') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                        <a href="{{ route('admin.siteWallet.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
                    </div>
                </form>

                @if(($users ?? collect())->isNotEmpty())
                    <div class="table-responsive mt-3">
                        <table class="table table-striped align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $u)
                                <tr>
                                    <td>{{ $u->id }}</td>
                                    <td>
                                        <div class="fw-medium">
                                            {{ trim(trim(($u->first_name ?? '')).' '.trim(($u->last_name ?? ''))) ?: ($u->username ?? __('User')) }}
                                        </div>
                                        <div class="text-muted" style="font-size: 12px;">{{ $u->username ?? '' }}</div>
                                    </td>
                                    <td>{{ $u->email ?? '--' }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.siteWallet.index', ['user_id' => $u->id]) }}">{{ __('Manage') }}</a>
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.siteCustomer.show', ['id' => $u->id]) }}">{{ __('View User') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif(($q ?? '') !== '')
                    <div class="text-muted mt-3">{{ __('No users found for your search.') }}</div>
                @endif
            </div>
        </div>

        @if(!empty($selectedUser))
            @php
                $currentBalance = (float) ($wallet->balance ?? 0);
            @endphp

            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3 justify-content-between">
                        <div>
                            <h5 class="mb-1">{{ __('Selected User') }}: #{{ $selectedUser->id }}</h5>
                            <div class="text-muted">
                                {{ trim(trim(($selectedUser->first_name ?? '')).' '.trim(($selectedUser->last_name ?? ''))) ?: ($selectedUser->username ?? __('User')) }}
                                • {{ $selectedUser->email ?? '--' }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted" style="font-size: 12px;">{{ __('Current Balance') }}</div>
                            <div style="font-size: 22px; font-weight: 600;">{{ number_format($currentBalance, 2) }}</div>
                        </div>
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('admin.siteWallet.adjust') }}" class="row g-2 align-items-end">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">

                        <div class="col-md-3">
                            <label class="form-label">{{ __('Action') }}</label>
                            <select name="action" class="form-select" required>
                                <option value="credit">{{ __('Credit (Add Money)') }}</option>
                                <option value="debit">{{ __('Debit (Remove Money)') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Note (optional)') }}</label>
                            <input type="text" name="note" class="form-control" maxlength="500" placeholder="{{ __('Reason / reference') }}">
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('{{ __('Confirm wallet adjustment?') }}')">
                                {{ __('Update Wallet') }}
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <h6 class="mb-2">{{ __('Recent Wallet History') }}</h6>
                        <table class="table table-striped align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Gateway') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Transaction') }}</th>
                                <th class="text-end">{{ __('Date') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(($walletHistories ?? collect()) as $tx)
                                <tr>
                                    <td>{{ $tx->id }}</td>
                                    <td>{{ $tx->type ?? '--' }}</td>
                                    <td>{{ number_format((float)($tx->amount ?? 0), 2) }}</td>
                                    <td>{{ $tx->payment_gateway ?? '--' }}</td>
                                    <td>{{ $tx->payment_status ?? '--' }}</td>
                                    <td>{{ $tx->transaction_id ?? '--' }}</td>
                                    <td class="text-end">{{ $tx->created_at ?? '--' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">{{ __('No wallet history found') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
