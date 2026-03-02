@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">{{ __('Create Featured Ad Package') }}</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.featuredAdPackage.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Description') }}</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Duration (days)') }}</label>
                        <input type="number" min="1" name="duration_days" value="{{ old('duration_days', 7) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Listing limit') }}</label>
                        <input type="number" min="1" name="advertisement_limit" value="{{ old('advertisement_limit', 1) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Price') }}</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', 0) }}" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Currency (3 letters)') }}</label>
                        <input type="text" name="currency" value="{{ old('currency') }}" class="form-control" placeholder="USD">
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ __('Active') }}</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
                    <a class="btn btn-secondary" href="{{ route('admin.featuredAdPackage.index') }}">{{ __('Back') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
