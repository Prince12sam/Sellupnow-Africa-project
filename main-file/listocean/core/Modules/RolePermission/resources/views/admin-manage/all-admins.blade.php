@extends('backend.admin-master')
@section('site-title')
    {{__('All Admins')}}
@endsection
@section('style')
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Admins') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <a href="{{ route('admin.create') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Add New Admin')}}</a>
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
                                <th>{{__('Image')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($all_admins->count() >=1)
                                @foreach($all_admins as $admin)
                                    <tr>
                                        <td>{{ $admin->id }}</td>
                                        <td>{{ $admin->name }}</td>
                                        <td>{!! render_image_markup_by_attachment_id($admin->image,'','thumb') !!}</td>
                                        <td>
                                            <a class="cmnBtn btn_5 btn_bg_warning btnIcon radius-5" href="{{ route('admin.edit',$admin->id) }}"><i class="las la-pen"></i></a>
                                            <a class="cmnBtn btn_5 btn_bg_secondary btnIcon radius-5 change_admin_password"
                                                data-bs-target="#adminPasswordModal"
                                                data-bs-toggle="modal"
                                                data-admin-id="{{ $admin->id }}">
                                                <i class="las la-lock"></i>
                                            </a>
                                            @if($admin->name != 'Super Admin')
                                                <x-popup.delete-popup :url="route('admin.delete',$admin->id)"/>
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
@include('rolepermission::admin-manage.password-modal')
@endsection
@section('scripts')
    @include('rolepermission::admin-manage.admin-js')
@endsection
