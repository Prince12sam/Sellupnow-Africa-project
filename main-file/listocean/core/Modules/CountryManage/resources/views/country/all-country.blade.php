@extends('backend.admin-master')
@section('site-title')
    {{__('All Countries')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Countries') }}</h4>
                            @can('country-bulk-delete')
                                <x-bulk-action.bulk-action/>
                            @endcan
                        </div>
                        <div class="dashboard__inner__header__right">
                            <x-btn.add-modal :title="__('Add Country')" />
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
                            @include('countrymanage::country.search-result')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('countrymanage::country.add-modal')
    @include('countrymanage::country.edit-modal')
@endsection
@section('scripts')
    @can('country-bulk-delete')
        <x-bulk-action.bulk-delete-js :url="route('admin.country.delete.bulk.action')"/>
    @endcan
    @include('countrymanage::country.country-js')
@endsection
