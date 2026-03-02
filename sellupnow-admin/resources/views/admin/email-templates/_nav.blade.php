{{-- Email Templates side navigation --}}
@php
    $emailTemplateNav = [
        ['label' => 'Overview',              'route' => 'admin.emailTemplate.index',               'active' => 'admin.emailTemplate.index'],
        ['label' => 'User Register',         'route' => 'admin.emailTemplate.register',            'active' => 'admin.emailTemplate.register'],
        ['label' => 'Email Verification',    'route' => 'admin.emailTemplate.emailVerify',         'active' => 'admin.emailTemplate.emailVerify'],
        ['label' => 'Identity Verification', 'route' => 'admin.emailTemplate.identityVerification','active' => 'admin.emailTemplate.identityVerification'],
        ['label' => 'Wallet Deposit',        'route' => 'admin.emailTemplate.walletDeposit',       'active' => 'admin.emailTemplate.walletDeposit'],
        ['label' => 'Listing Approval',      'route' => 'admin.emailTemplate.listingApproval',     'active' => 'admin.emailTemplate.listingApproval'],
        ['label' => 'Listing Publish',       'route' => 'admin.emailTemplate.listingPublish',      'active' => 'admin.emailTemplate.listingPublish'],
        ['label' => 'Listing Unpublished',   'route' => 'admin.emailTemplate.listingUnpublished',  'active' => 'admin.emailTemplate.listingUnpublished'],
        ['label' => 'Guest: Add Listing',    'route' => 'admin.emailTemplate.guestAddListing',     'active' => 'admin.emailTemplate.guestAddListing'],
        ['label' => 'Guest: Approve Listing','route' => 'admin.emailTemplate.guestApproveListing', 'active' => 'admin.emailTemplate.guestApproveListing'],
        ['label' => 'Guest: Publish Listing','route' => 'admin.emailTemplate.guestPublishListing', 'active' => 'admin.emailTemplate.guestPublishListing'],
    ];
@endphp
<div class="list-group list-group-flush mb-4 mb-lg-0">
    @foreach($emailTemplateNav as $nav)
        <a href="{{ route($nav['route']) }}"
           class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2
                  {{ request()->routeIs($nav['active']) ? 'active' : '' }}">
            <i class="fa-solid fa-envelope fa-sm opacity-75"></i>
            {{ __($nav['label']) }}
        </a>
    @endforeach
</div>
