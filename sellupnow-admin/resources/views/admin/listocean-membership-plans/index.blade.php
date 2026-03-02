@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <div>
            <h4 class="mb-0">{{ __('Membership Plans (Customer Web)') }}</h4>
            <div class="text-muted" style="font-size: 12px;">{{ __('These plans are used for paid membership purchases on the customer website.') }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.membershipPlan.create') }}" class="btn btn-primary">{{ __('Add Plan') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        @if(!($hasTable ?? false))
            <div class="alert alert-warning">
                {{ __('The ListOcean membership_plans table is missing. Run the ListOcean migrations first.') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Duration (days)') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td>{{ $plan->id }}</td>
                                <td>
                                    <div class="fw-medium">{{ $plan->name }}</div>
                                    @if(!empty($plan->description))
                                        <div class="text-muted" style="font-size: 12px;">{{ \Illuminate\Support\Str::limit($plan->description, 90) }}</div>
                                    @endif
                                </td>
                                <td>{{ $plan->price }} {{ $plan->currency ?? '--' }}</td>
                                <td>{{ $plan->duration_days }}</td>
                                <td>
                                    @if((int)($plan->is_active ?? 0) === 1)
                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.membershipPlan.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                                    <form action="{{ route('admin.membershipPlan.destroy', $plan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('{{ __('Delete this plan?') }}')">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">{{ __('No plans found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
