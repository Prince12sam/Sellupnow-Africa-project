@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h3>Advertiser Purchases (Scaffold)</h3></div>
    <div class="card-body">
        <p class="text-muted">Recent purchases and impression billing will appear here.</p>
        <table class="table">
            <thead><tr><th>ID</th><th>Advertiser</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>
                <tr><td colspan="4" class="text-muted">No purchases (scaffold)</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
