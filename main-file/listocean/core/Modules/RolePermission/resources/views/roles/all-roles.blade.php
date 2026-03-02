@extends('backend.admin-master')
@section('site-title')
    {{ __('All Roles') }}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Roles') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <a class="cmnBtn btn_5 btn_bg_blue radius-5"
                               href="javascript:void(0)"
                               data-bs-target="#addRoleModal"
                               data-bs-toggle="modal" >
                                {{__('Add Role')}}
                            </a>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <table class="table_activation">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($roles->count() >=1)
                                @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            @if($role->name == 'Super Admin')
                                                <span class="text-secondary">{{ __('By default super admin has all permissions.') }}</span>
                                            @else
                                                <a href="javascript:void(0)"
                                                   class="cmnBtn btn_5 btn_bg_warning radius-5  edit_role_modal"
                                                   data-bs-target="#editRoleModal"
                                                   data-bs-toggle="modal"
                                                   data-role-id="{{ $role->id }}"
                                                   data-role-name="{{ $role->name }}"
                                                >{{ __('Edit Role') }}</a>
                                                <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{ route('admin.role.permission',$role->id) }}">
                                                    {{ __('Assign Permission') }}
                                                </a>
                                                <x-popup.delete-popup :url="route('admin.role.delete',$role->id)"/>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <x-table.no-data-found :colspan="'7'" :class="'text-danger text-center py-5'" />
                            @endif
                            </tbody>
                        </table>
                   </div>
                 </div>
            </div>
        </div>
    </div>
    @include('rolepermission::roles.add-role-modal')
    @include('rolepermission::roles.edit-role-modal')
@endsection
@section('scripts')
    @include('rolepermission::roles.role-js')
@endsection
