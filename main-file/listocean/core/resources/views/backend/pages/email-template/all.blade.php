@extends('backend.admin-master')
@section('site-title')
    {{__('All Email Templates')}}
@endsection
@section('style')
    <x-datatable.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <x-validation.error/>
                <h2 class="dashboard__card__header__title mb-3">{{__('All Email Templates')}}</h2>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_dataTable">
                        <table class="dataTablesExample">
                            <thead>
                            <th>{{__('SN')}}</th>
                            <th>{{__('Title')}}</th>
                            <th>{{__('Action')}}</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('Email Global Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the any mail sent.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.global.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('User Register Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Register.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.register.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('User Identity Verification Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Verification.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.identity.verification.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('Guest Add New Listing Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the Guest Listing Add.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.guest.add.listing.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('Guest Listing Approve Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the Guest Listing Approve.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.guest.approve.listing.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('Guest Listing Publish Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the Guest Listing Publish.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.guest.publish.listing.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('New Listing Approve Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User New Listing Approval.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.new.listing.approval.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('Listing Publish Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Listing Publish.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.new.listing.publish.template')"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong class="serial-number"></strong></td>
                                <td>
                                    {{__('Listing Unpublished Template')}} <br>
                                    <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Listing Unpublished.') }}</span>
                                </td>
                                <td>
                                    <x-icon.edit-icon :url="route('admin.email.user.new.listing.unpublished.template')"/>
                                </td>
                            </tr>

                            @if(moduleExists("Wallet"))
                                <tr>
                                    <td><strong class="serial-number"></strong></td>
                                    <td>
                                        {{__('User Wallet Deposit')}} <br>
                                        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Wallet.') }}</span>
                                    </td>
                                    <td>
                                        <x-icon.edit-icon :url="route('admin.email.user.wallet.deposit.template')"/>
                                    </td>
                                </tr>
                            @endif
                             @if(moduleExists("Membership"))
                                 @include('membership::backend.email-template.email-template-lists')
                             @endif
                            @if(moduleExists("SupportTicket"))
                                 @include('supportticket::backend.email-template.email-template-lists')
                             @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-datatable.js/>
    <script>
        $(document).ready(function(){
            var table = $('.dataTablesExample').DataTable();
            function updateSerialNumbers() {
                var PageInfo = table.page.info();
                table.column(0, {page:'current'}).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });
            }
            // Update serial numbers on initial load
            updateSerialNumbers();
            table.on('draw.dt', function() {
                updateSerialNumbers();
            });
        });
    </script>
@endsection
