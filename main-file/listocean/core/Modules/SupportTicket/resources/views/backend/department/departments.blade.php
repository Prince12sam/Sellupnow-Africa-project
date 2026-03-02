@extends('backend.admin-master')
@section('site-title')
    {{__('All Departments')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Departments') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                             @can('department-add')
                                 <x-btn.add-modal :title="__('Add Department')" />
                              @endcan
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <div class="mt-4">
                            <x-notice.general-notice :description="__('Notice: Department status inactive means the department will not show while create a ticket.')" />
                        </div>
                        <div class="search_result">
                            @include('supportticket::backend.department.search-result')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('supportticket::backend.department.add-modal')
    @include('supportticket::backend.department.edit-modal')
@endsection
@section('scripts')
    @include('supportticket::backend.department.department-js')
@endsection
