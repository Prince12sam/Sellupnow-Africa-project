@extends('backend.admin-master')
@section('site-title')
    {{__('User Identity verification Email Template')}}
@endsection
@section('style')
    <x-media.css/>
    <x-summernote.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard_orderDetails__header__flex">
                    <div class="dashboard_orderDetails__header__left">
                         <h2 class="dashboard__card__header__title mb-3">{{__('User Identity verification Email Template')}}</h2>
                    </div>
                    <div class="dashboard_orderDetails__header__right">
                        <a href="{{route('admin.email.template.all')}}" class="cmnBtn btn_5 btn_bg_info radius-5">{{__('All Email Templates')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.email.user.identity.verification.template')}}" method="POST">
                    @csrf
                    <div class="form__input__single">
                        <label for="user_identity_verification_subject" class="form__input__single__label">{{__('Email Subject')}}</label>
                        <input type="text" class="form__control radius-5" name="user_identity_verification_subject" value="{{get_static_option('user_identity_verification_subject') ?? 'User Verification Request'}}">
                    </div>
                    <div class="form__input__single">
                        <label for="admin_user_identity_verification_message" class="form__input__single__label">{{__('Email Message')}}</label>
                        <textarea class="form__control summernote" name="admin_user_identity_verification_message">{!! get_static_option('admin_user_identity_verification_message') ?? '<p>Hello @name,</p></p>You have a new request for user verification. Email: @email</p>'  !!} </textarea>
                        <div class="d-grid">
                            <small class="form-text"><strong class="text-danger"> @name </strong>{{__('will be replaced by dynamically with name.')}}</small>
                            <small class="form-text"><strong class="text-danger"> @email </strong>{{__('will be replaced by dynamically with email.')}}</small>
                        </div>
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
    <x-media.js />
    <x-summernote.js/>
@endsection
