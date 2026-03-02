@extends('layouts.app')

@section('header-title', __('Email Templates'))

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-1 mb-3">
        <h4 class="m-0">{{ __('Email Templates') }}</h4>
    </div>

    <div class="row g-3">
        @php
            $templates = [
                ['label' => __('User Registration'),          'route' => route('admin.emailTemplate.register')],
                ['label' => __('Email Verification'),          'route' => route('admin.emailTemplate.emailVerify')],
                ['label' => __('Identity Verification'),       'route' => route('admin.emailTemplate.identityVerification')],
                ['label' => __('Wallet Deposit'),              'route' => route('admin.emailTemplate.walletDeposit')],
                ['label' => __('Listing Approval'),            'route' => route('admin.emailTemplate.listingApproval')],
                ['label' => __('Listing Publish'),             'route' => route('admin.emailTemplate.listingPublish')],
                ['label' => __('Listing Unpublished'),         'route' => route('admin.emailTemplate.listingUnpublished')],
                ['label' => __('Guest — Add New Listing'),     'route' => route('admin.emailTemplate.guestAddListing')],
                ['label' => __('Guest — Approve Listing'),     'route' => route('admin.emailTemplate.guestApproveListing')],
                ['label' => __('Guest — Publish Listing'),     'route' => route('admin.emailTemplate.guestPublishListing')],
            ];
        @endphp

        @foreach($templates as $tpl)
        <div class="col-md-6 col-xl-4">
            <a href="{{ $tpl['route'] }}" class="card text-decoration-none h-100 hover-shadow">
                <div class="card-body d-flex align-items-center gap-3 py-4">
                    <span class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;">
                        <i class="fa-solid fa-envelope fa-lg"></i>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-semibold">{{ $tpl['label'] }}</h6>
                        <small class="text-muted">{{ __('Edit subject & message') }}</small>
                    </div>
                    <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
