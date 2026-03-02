@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ __('Featured Ads Activations (Customer Web)') }}</h4>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('admin.featuredAdPackage.index') }}">{{ __('Packages') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.featuredAdReports.purchases') }}">{{ __('Purchases') }}</a>
        </div>
    </div>

    @if(!$hasTable)
        <div class="alert alert-warning">{{ __('Table featured_ad_activations was not found in the Customer Web database.') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Purchase') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Listing') }}</th>
                        <th>{{ __('Start') }}</th>
                        <th>{{ __('End') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($activations as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>#{{ $a->purchase_id }}</td>
                            <td>#{{ $a->user_id }}</td>
                            <td>#{{ $a->listing_id }}</td>
                            <td>{{ $a->start_at ?? '--' }}</td>
                            <td>{{ $a->end_at ?? '--' }}</td>
                            <td>{{ $a->status ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">{{ __('No activations found.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
