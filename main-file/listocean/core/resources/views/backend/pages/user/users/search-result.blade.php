<table class="table_activation">
    <thead>
    <tr>
        <th>{{__('ID')}}</th>
        <th>{{__('Name')}}</th>
        <th>{{__('Email')}}</th>
        <th>{{__('Phone')}}</th>
        <th>{{__('Account Status')}}</th>
        <th>{{__('Identity Verify')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @if($all_users->total() >=1)
        @foreach($all_users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->first_name.' '.$user->last_name }}</td>
                <td>
                    {{ $user->email }}
                    <strong>
                        @if($user->email_verified == 1)
                            <i class="las la-check-circle text-success"></i>
                        @else
                            <i class="las la-times-circle text-danger"></i>
                        @endif
                    </strong>
                </td>
                <td>{{ $user->phone }} </td>
                @if($user->is_suspend == 1)
                <td> <x-status.table.account-status :status="$user->is_suspend"/> </td>
                @else
                <td> <x-status.table.active-inactive :status="$user->status"/> </td>
                @endif
                <td class="verified_status_load_{{$user->id}}">
                    <x-status.table.verified-status :status="$user->verified_status"/>
                    @if(!empty($user->identity_verify) && $user->identity_verify?->status == null)
                        <span class="badge bg-danger" >{{__('new')}}</span>
                    @endif
                </td>
                <td class="actions">
                    <a class="cmnBtn btn_5 btn_bg_info radius-5 user_details"
                       data-bs-toggle="modal"
                       data-bs-target="#userDetailsModal"
                       data-user_id="{{ $user->id }}"
                       data-first_name="{{ $user->first_name }}"
                       data-last_name="{{ $user->last_name }}"
                       data-username="{{ $user->username }}"
                       data-email="{{ $user->email }}"
                       data-phone="{{ $user->phone }}"
                       data-country="{{ optional($user->user_country)->country }}"
                       data-country_id="{{ $user->country_id }}"
                       data-state="{{ optional($user->user_state)->state }}"
                       data-state_id="{{ $user->state_id }}"
                       data-city="{{ optional($user->user_city)->city }}"
                       data-city_id="{{ $user->city_id }}">
                        {{ __('User Details') }}
                    </a>

                    @can('user-verify-status')
                       <a class="cmnBtn btn_5 btn_bg_primary radius-5 user_identity_details"
                           data-bs-toggle="modal"
                           data-bs-target="#userIdentityModal"
                           data-user_id="{{ $user->id }}">
                            {{ __('View Identity') }}
                        </a>
                        @endcan

                    @can('user-password')
                        <a class="cmnBtn btn_5 btn_bg_secondary radius-5 btnIcon user_password_update_modal"
                           data-bs-toggle="modal"
                           data-bs-target="#userPasswordModal"
                           data-user_id_for_change_password="{{ $user->id }}">
                            <i class="las la-lock"></i>
                        </a>
                    @endcan

                   @can('user-password')
                        @if($user->email_verified == 0)
                            <x-status.table.email-verify :title="__('Verify Email')" :url="route('admin.user.verify.email',$user->id)"/>
                        @endif
                    @endcan

                    @can('user-delete')
                        <x-popup.delete-popup :url="route('admin.user.delete',$user->id)"/>
                        <x-status.table.status-change :url="route('admin.user.status',$user->id)"/>
                        @if($user->is_suspend == 1)
                            <x-status.table.suspend-status-change :class="'unsuspend_user_account'" :title="__('Unsuspend')" :url="route('admin.account.unsuspend',$user->id)"/>
                        @else
                            <x-status.table.status-change :class="'suspend_user_account'" :title="__('Suspend')" :url="route('admin.account.suspend',$user->id)"/>
                        @endif
                    @endcan

                </td>
            </tr>
        @endforeach
    @else
        <x-table.no-data-found :colspan="'7'" :class="'text-danger text-center py-5'" />
    @endif
    </tbody>
</table>
<x-pagination.laravel-paginate :allData="$all_users"/>
