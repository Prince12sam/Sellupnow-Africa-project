@extends('backend.admin-master')
@section('site-title')
    {{__('All Brands')}}
@endsection
@section('style')
    <x-media.css/>
    <style>
        img.no-image {
            width: 119px;
        }
        .attachment-preview {
            width: 107px;
            height: 78px;
        }
        .media-upload-btn-wrapper .img-wrap .rmv-span {
            top: 1px;
            z-index: 2;
            width: 25px;
            height: 22px;
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
                            <h4 class="dashboard__inner__header__title">{{ __('All Brands') }}</h4>
                            @can('brand-bulk-delete')
                                <x-bulk-action.bulk-action/>
                            @endcan
                        </div>
                        <div class="dashboard__inner__header__right">
                            <x-btn.add-modal :title="__('Add Brand')" />
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control blog_string_search" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                    <x-validation.error/>
                </div>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <div class="search_result">
                            @include('brand::brand.search-result')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('brand::brand.add-modal')
    @include('brand::brand.edit-modal')
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    @can('brand-bulk-delete')
        <x-bulk-action.bulk-delete-js :url="route('admin.brand.delete.bulk.action')"/>
    @endcan
    @include('brand::brand.brand-js')
@endsection
