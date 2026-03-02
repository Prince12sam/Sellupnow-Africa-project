@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <div>
            <h4 class="mb-0">{{ __('Create Membership Plan') }}</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.membershipPlan.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-medium mb-1">{{ __('Please fix the errors below:') }}</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.membershipPlan.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Price') }}</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', '0') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Currency (ISO)') }}</label>
                            <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" placeholder="NGN">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Duration (days)') }}</label>
                            <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', '30') }}" required>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('Create Plan') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
