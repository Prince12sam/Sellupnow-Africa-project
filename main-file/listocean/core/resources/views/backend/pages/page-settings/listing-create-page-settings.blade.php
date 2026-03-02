@extends('backend.admin-master')
@section('site-title')
    {{__('Listing Create Page Settings')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Listing Create Page Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.listing.create.settings')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__single">
                        <label class="form__input__single__label">{{__('Who Will Create Listing?')}}</label>
                        <select name="listing_create_settings" id="listing_create_settings"  class="form-control">
                            <option value="" disabled>{{ __('Select') }}</option>
                                <option value="all_user" {{ get_static_option('listing_create_settings')=='all_user' ? 'selected' : '' }} >{{ __('All User') }}</option>
                                <option value="verified_user" {{ get_static_option('listing_create_settings')=='verified_user' ? 'selected' : '' }} >{{ __('Only Verified User') }}</option>
                        </select>
                    </div>

                    <div class="form__input__single">
                        <label class="form__input__single__label">{{__('Select Status')}}</label>
                        <select name="listing_create_status_settings" id="listing_create_status_settings" class="form-control">
                            <option value="" disabled>{{ __('Select') }}</option>
                            <option value="pending" {{ get_static_option('listing_create_status_settings')=='pending' ? 'selected' : '' }} >{{ __('Pending') }}</option>
                            <option value="approved" {{ get_static_option('listing_create_status_settings')=='approved' ? 'selected' : '' }} >{{ __('Approved') }}</option>
                        </select>
                        <p class="mb-3 text-info">{{ __('You can set the user create listing status auto Approved/Pending from here.') }}</p>
                    </div>

                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js />
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection
