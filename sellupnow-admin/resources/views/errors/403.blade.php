@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center vh-100 bg-light">
        <div class="d-flex justify-content-center align-items-center bg-danger bg-opacity-10 rounded-circle" style="width: 80px; height: 80px;">
            <i class="fas fa-exclamation-triangle text-danger fs-1"></i>
        </div>
        <h1 class="mt-4 fw-bold text-dark">403</h1>
        <p class="lead text-muted">Access Denied</p>
        <p class="text-secondary">
            Sorry, you don't have permission to view this page.
        </p>

        <a href="{{ request()->is('admin', 'admin/*') ? route('admin.dashboard.index') : route('shop.dashboard.index') }}" class="btn btn-primary mt-4 px-4 py-2">
            Back to Dashboard
        </a>

        <div class="mt-5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 300" style="max-width:500px;width:100%;height:auto;" role="img" aria-label="403 Forbidden">
                <rect width="500" height="300" rx="12" fill="#f1f5f9"/>
                <text x="250" y="130" text-anchor="middle" font-family="sans-serif" font-size="72" font-weight="700" fill="#94a3b8">403</text>
                <text x="250" y="185" text-anchor="middle" font-family="sans-serif" font-size="20" fill="#cbd5e1">Forbidden</text>
                <circle cx="250" cy="240" r="18" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                <line x1="243" y1="233" x2="257" y2="247" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>
                <line x1="257" y1="233" x2="243" y2="247" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
    </div>
@endsection
@push('css')
    <style>
        .bg-opacity-10 {
            background-color: rgba(255, 0, 0, 0.1) !important;
        }
    </style>
@endpush
