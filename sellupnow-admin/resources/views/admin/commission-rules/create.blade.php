@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Create Commission Rule</h3>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('admin.commissionRules.store') }}">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input name="name" class="form-control" />
            </div>
            <div class="mb-3">
                <label>Scope</label>
                <select name="scope" class="form-control">
                    <option value="global">Global</option>
                    <option value="category">Category</option>
                    <option value="shop">Shop</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Scope ID</label>
                <input name="scope_id" class="form-control" />
            </div>
            <div class="mb-3">
                <label>Percentage (%)</label>
                <input name="percentage" type="number" step="0.01" class="form-control" value="0" />
            </div>
            <div class="mb-3">
                <label>Fixed</label>
                <input name="fixed" type="number" step="0.01" class="form-control" value="0" />
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="activeCheck">
                <label class="form-check-label" for="activeCheck">Active</label>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection
