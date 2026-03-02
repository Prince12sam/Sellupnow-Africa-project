@extends('frontend.layout.master')
@section('site_title',__('Support Tickets'))
@section('style')
    <x-summernote.css/>
@endsection
@section('content')
    <div class="profile-setting support-tickets setting-page-with-table section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-validation.frontend-error/>
                                <x-frontend.user.responsive-icon/>
                                <div class="paymentTable">
                                        <div class="single-profile-settings">
                                            <div class="single-profile-settings-header">
                                                <div class="single-profile-settings-header-flex">
                                                    <div class="d-flex justify-content-between">
                                                        <h4 class="memberTittle">{{__('All Tickets')}}</h4>
                                                    </div>
                                                    <div class="right_sidebar_search_bar_user_panel d-flex gap-2 align-items-center">
                                                        @if($tickets->count() > 0)
                                                            <x-search.search-in-table :id="'string_search'" :placeholder="__('search by priority or Status')" :class="'form-control radius-10'" />
                                                        @endif
                                                        <div class="edit-btn">
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#addModal">{{ __('Add New') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="single-profile-settings-inner profile-border-top">
                                                <div class="custom_table style-04 search_result">
                                                    @include('supportticket::user.search-result')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('supportticket::user.add-modal')
@endsection
@section('scripts')
    <x-summernote.js/>
    @include('supportticket::user.ticket-js')
@endsection
