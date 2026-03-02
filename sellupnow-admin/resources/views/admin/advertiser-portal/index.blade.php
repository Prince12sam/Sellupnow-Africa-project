@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h3>Advertiser Portal (Scaffold)</h3>
        <a href="{{ route('admin.advertiserPortal.create') }}" class="btn btn-primary float-end">New Campaign</a>
    </div>
    <div class="card-body">
        <p class="text-muted">This is a scaffold for advertiser campaigns and promo impressions management.</p>
        <table class="table">
            <thead><tr><th>ID</th><th>Name</th><th>Budget</th><th>Actions</th></tr></thead>
            <tbody>
                <tr><td colspan="4" class="text-muted">No campaigns (scaffold)</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
