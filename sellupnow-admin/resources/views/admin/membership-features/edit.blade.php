@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between px-3">
        <div><h4 class="mb-0">{{ __('Edit Feature') }}</h4></div>
        <div><a href="{{ route('admin.membershipFeature.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a></div>
    </div>

    <div class="container-fluid mt-3">
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('admin.membershipFeature.update', $item->id) }}">
                @csrf
                <div class="mb-3"><label class="form-label">Key</label><input name="key" class="form-control" value="{{ old('key', $item->key) }}" required></div>
                <div class="mb-3"><label class="form-label">Label</label><input name="label" class="form-control" value="{{ old('label', $item->label) }}" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control">{{ old('description', $item->description) }}</textarea></div>
                <div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ $item->is_active ? 'checked' : '' }}><label for="is_active" class="form-check-label">Active</label></div>
                <div class="mt-3"><button class="btn btn-primary">Save</button></div>
            </form>
        </div></div>
    </div>
@endsection
