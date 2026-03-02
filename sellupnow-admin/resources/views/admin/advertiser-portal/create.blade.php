@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h3>Create Advertiser Campaign (Scaffold)</h3></div>
    <div class="card-body">
        <form method="post" action="{{ route('admin.advertiserPortal.store') }}">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input name="name" class="form-control" required />
            </div>
            <div class="mb-3">
                <label>Budget</label>
                <input name="budget" type="number" step="0.01" class="form-control" required />
            </div>
            <div class="mb-3">
                <label>Target Categories (comma-separated)</label>
                <input name="target_categories" class="form-control" />
            </div>
            <button class="btn btn-primary">Create</button>
        </form>
    </div>
</div>
@endsection
