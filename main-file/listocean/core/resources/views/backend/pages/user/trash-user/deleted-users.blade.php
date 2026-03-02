@extends('backend.admin-master')
@section('site-title')
    {{__('Trash List')}}
@endsection
@section('style')
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Trash List') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control blog_string_search" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                    <x-validation.error/>
                </div>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <x-notice.general-notice
                            :class="'mb-5'"
                            :description="__('Notice: Permanently deleting a user results in the irreversible removal of all their associated information from the system, making these data non-recoverable.')"/>
                        <!-- Table Start -->
                        <div class="search_result">
                            @include('backend.pages.user.trash-user.search-result-for-delete-users')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('backend.pages.user.trash-user.delete-user-js')
@endsection
