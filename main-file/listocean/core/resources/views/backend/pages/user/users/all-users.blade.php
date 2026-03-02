@extends('backend.admin-master')
@section('site-title')
    {{__('All Users')}}
@endsection
@section('style')
    <style>
        td.actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Users') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control blog_string_search" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error />
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <x-notice.general-notice
                            :class="'mb-5'"
                            :description4="__('Notice: Identity verify means user verified his identity by legal documents')"
                        />
                        <div class="search_result">
                            @include('backend.pages.user.users.search-result')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.pages.user.users.user-details-modal')
    @include('backend.pages.user.users.user-password-modal')
    @include('backend.pages.user.users.user-details-edit-modal')
    @include('backend.pages.user.users.identity-verify-details-modal')
@endsection
@section('scripts')
    @include('backend.pages.user.users.user-js')
@endsection
