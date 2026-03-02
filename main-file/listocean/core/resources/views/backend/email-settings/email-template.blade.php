@extends('backend.admin-master')
@section('site-title')
    {{__('Email Global Template Settings')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote.css')}}">
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Email Global Template Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.email.global.template')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__single">
                        <label for="site_global_email_template" class="form__input__single__label">{{__('Email Template')}}</label>
                        <input type="hidden" name="site_global_email_template"  class="form__control" value="{{get_static_option('site_global_email_template')}}" >
                        <div class="summernote" data-content='{{get_static_option("site_global_email_template")}}'></div>
                        <small class="form-text text-muted">
                            <strong class="text-danger">{{ __('@username') }}</strong> {{__('Will replace by username of user and') }}  <br>
                            <strong class="text-danger">{{ __('@company') }}</strong> {{__('will be replaced by site title also') }}  <br>
                            <strong class="text-danger">{{ __('@message') }}</strong> {{__('will be replaced by dynamically with message.') }}
                        </small>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
     <x-summernote.js/>
@endsection
