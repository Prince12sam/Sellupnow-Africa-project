@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ __('Featured Ads Purchases (Customer Web)') }}</h4>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('admin.featuredAdPackage.index') }}">{{ __('Packages') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.featuredAdReports.activations') }}">{{ __('Activations') }}</a>
        </div>
    </div>

    @if(!$hasTable)
        <div class="alert alert-warning">{{ __('Table featured_ad_purchases was not found in the Customer Web database.') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Package') }}</th>
                        <th>{{ __('Days') }}</th>
                        <th>{{ __('Remaining') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Gateway') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Purchased') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($purchases as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>#{{ $p->user_id }}</td>
                            <td>{{ $p->package_name }}</td>
                            <td>{{ (int)($p->duration_days ?? 0) }}</td>
                            <td>{{ (int)($p->remaining_limit ?? 0) }} / {{ (int)($p->advertisement_limit ?? 0) }}</td>
                            <td>{{ number_format((float)($p->amount ?? 0), 2) }} {{ $p->currency ?? '' }}</td>
                            <td>{{ $p->payment_gateway ?? '--' }}</td>
                            <td>{{ $p->payment_status ?? '--' }}</td>
                            <td>{{ $p->purchased_at ?? $p->created_at ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">{{ __('No purchases found.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
