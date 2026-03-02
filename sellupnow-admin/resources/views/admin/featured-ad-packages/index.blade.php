@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">{{ __('Featured Ad Packages (Customer Web)') }}</h4>
        <a href="{{ route('admin.featuredAdPackage.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
    </div>

    @if(!$hasTable)
        <div class="alert alert-warning">{{ __('Table featured_ad_packages was not found in the Customer Web database.') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Days') }}</th>
                            <th>{{ __('Limit') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Currency') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($packages as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ (int)($p->duration_days ?? 0) }}</td>
                            <td>{{ (int)($p->advertisement_limit ?? 0) }}</td>
                            <td>{{ number_format((float)($p->price ?? 0), 2) }}</td>
                            <td>{{ $p->currency ?? '--' }}</td>
                            <td>{!! !empty($p->is_active) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.featuredAdPackage.edit', ['id' => $p->id]) }}">{{ __('Edit') }}</a>
                                <a class="btn btn-sm btn-outline-danger" href="{{ route('admin.featuredAdPackage.destroy', ['id' => $p->id]) }}" onclick="return confirm('Delete this package?')">{{ __('Delete') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">{{ __('No packages found.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
