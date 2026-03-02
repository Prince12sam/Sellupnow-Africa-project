@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Commission Rule</h3>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('admin.commissionRules.update', $commissionRule->id) }}">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input name="name" class="form-control" value="{{ $commissionRule->name }}" />
            </div>
            <div class="mb-3">
                <label>Scope</label>
                <select name="scope" class="form-control">
                    <option value="global" {{ $commissionRule->scope == 'global' ? 'selected' : '' }}>Global</option>
                    <option value="category" {{ $commissionRule->scope == 'category' ? 'selected' : '' }}>Category</option>
                    <option value="shop" {{ $commissionRule->scope == 'shop' ? 'selected' : '' }}>Shop</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Scope ID</label>
                <input name="scope_id" class="form-control" value="{{ $commissionRule->scope_id }}" />
            </div>
            <div class="mb-3">
                <label>Percentage (%)</label>
                <input name="percentage" type="number" step="0.01" class="form-control" value="{{ $commissionRule->percentage }}" />
            </div>
            <div class="mb-3">
                <label>Fixed</label>
                <input name="fixed" type="number" step="0.01" class="form-control" value="{{ $commissionRule->fixed }}" />
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="activeCheck" {{ $commissionRule->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="activeCheck">Active</label>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
