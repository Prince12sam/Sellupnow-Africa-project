@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Identity Verification Requests') }}</h4>

        @php
            $active = $statusFilter ?? 'queue';
            $mk = fn ($v) => route('admin.identityVerification.index', ['status' => $v]);
            $btn = fn ($v) => $active === $v ? 'btn btn-primary btn-sm' : 'btn btn-outline-primary btn-sm';
        @endphp

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ $mk('queue') }}" class="{{ $btn('queue') }}">{{ __('Queue') }}</a>
            <a href="{{ $mk('pending') }}" class="{{ $btn('pending') }}">{{ __('Pending') }}</a>
            <a href="{{ $mk('approved') }}" class="{{ $btn('approved') }}">{{ __('Approved') }}</a>
            <a href="{{ $mk('declined') }}" class="{{ $btn('declined') }}">{{ __('Declined') }}</a>
            <a href="{{ $mk('all') }}" class="{{ $btn('all') }}">{{ __('All') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}.</th>
                                <th>{{ __('User') }}</th>
                                <th style="min-width: 120px">{{ __('Phone') }}</th>
                                <th style="min-width: 180px">{{ __('Email') }}</th>
                                <th class="text-center">{{ __('Request Status') }}</th>
                                <th class="text-center">{{ __('Account Verified') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $key => $req)
                                <tr>
                                    <td class="text-center">{{ $requests->firstItem() + $key }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $req->fullName ?? __('User') }}</div>
                                        <div class="text-muted" style="font-size: 12px;">#{{ $req->user_id }}</div>
                                    </td>
                                    <td>{{ $req->phone ?? '--' }}</td>
                                    <td>{{ $req->email ?? '--' }}</td>
                                    <td class="text-center">
                                        @if((int) $req->status === 0)
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                        @elseif((int) $req->status === 1)
                                            <span class="badge bg-success">{{ __('Approved') }}</span>
                                        @elseif((int) $req->status === 2)
                                            <span class="badge bg-danger">{{ __('Declined') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if((int) $req->user_verified_status === 1)
                                            <span class="badge bg-success">{{ __('Yes') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('No') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.identityVerification.show', $req->id) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('Details') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $requests->withQueryString()->links() }}
        </div>
    </div>
@endsection
