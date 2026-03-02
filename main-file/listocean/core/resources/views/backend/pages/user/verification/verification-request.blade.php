@extends('backend.admin-master')
@section('site-title')
    {{ __('All Requests') }}
@endsection
@section('style')
    <style>
        .document-preview {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .document-preview img {
            width: 214px;
            margin-right: 0px;
        }
        .swal2-container {
            z-index: 99999;
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
                            <h4 class="dashboard__inner__header__title">{{ __('All Requests') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control blog_string_search" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <div class="search_result">
                            @include('backend.pages.user.verification.verification-request-search')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.pages.user.verification.identity-verify-details-modal')
@endsection
@section('scripts')
    @include('backend.pages.user.verification.verification-js')
@endsection
