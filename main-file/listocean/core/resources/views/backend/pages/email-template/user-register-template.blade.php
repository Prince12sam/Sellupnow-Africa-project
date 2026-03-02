@extends('backend.admin-master')
@section('site-title')
    {{__('User Register Email Template')}}
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
                         <h2 class="dashboard__card__header__title mb-3">{{__('User Register Email Template')}}</h2>
                    </div>
                    <div class="dashboard_orderDetails__header__right">
                        <a href="{{route('admin.email.template.all')}}" class="cmnBtn btn_5 btn_bg_info radius-5">{{__('All Email Templates')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.email.user.register.template')}}" method="POST">
                    @csrf
                    <div class="form__input__single">
                        <label for="user_register_subject" class="form__input__single__label">{{__('Email Subject')}}</label>
                        <input type="text" class="form__control radius-5" name="user_register_subject" value="{{get_static_option('user_register_subject') ?? 'New User Registration'}}">
                    </div>
                    <div class="form__input__single">
                        <label for="user_register_message" class="form__input__single__label">{{__('User Register message for user')}}</label>
                        <input type="hidden" name="user_register_message"  class="form__control" value="{{get_static_option('user_register_message')}}">
                        <textarea class="form__control summernote" name="user_register_message">{!! get_static_option('user_register_message') ?? '<p>Hello @name,</p></p>You have user registered Username: @username Email: @email</p>'  !!} </textarea>
                        <div class="d-grid">
                            <small class="form-text"><strong class="text-danger"> @name </strong>{{__('will be replaced by dynamically with name.')}}</small>
                            <small class="form-text"><strong class="text-danger"> @username </strong>{{__('will be replaced by dynamically with username.')}}</small>
                            <small class="form-text"><strong class="text-danger"> @email </strong>{{__('will be replaced by dynamically with email.')}}</small>
                            <small class="form-text"><strong class="text-danger"> @password </strong>{{__('will be replaced by dynamically with password.')}}</small>
                        </div>
                    </div>

                    <div class="form__input__single">
                        <label for="user_register_message_for_admin" class="form__input__single__label">{{__('User Register message for admin')}}</label>
                        <textarea class="form__control summernote" name="user_register_message_for_admin">{!! get_static_option('user_register_message_for_admin') ?? '<p>Hello @name,</p></p>You have user registered Username: @username Email: @email</p>'  !!} </textarea>
                    </div>
                    <div class="d-grid">
                        <small class="form-text"><strong class="text-danger"> @name </strong>{{__('will be replaced by dynamically with name.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @username </strong>{{__('will be replaced by dynamically with username.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @email </strong>{{__('will be replaced by dynamically with email.')}}</small>
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
