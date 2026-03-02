@extends('backend.admin-master')
@section('site-title')
    {{__('Order Mail Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Order Mail Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.email.global.template')}}" method="POST">
                    @csrf
                    <div class="form__input__single">
                        <label for="order_mail_success_message" class="form__input__single__label">{{__('Order Mail Success Message')}}</label>
                        <input type="text" class="form__control radius-5" name="order_mail_success_message" value="{{get_static_option('order_mail_success_message')}}">
                        <small class="form-text text-muted">{{__('this message will show when any one place order.')}}</small>
                    </div>
                    <div class="form__input__single">
                        <label for="contact_mail_success_message" class="form__input__single__label">{{__('Contact Mail Success Message')}}</label>
                        <input type="text" class="form__control radius-5" name="contact_mail_success_message" value="{{get_static_option('contact_mail_success_message')}}">
                        <small class="form-text text-muted">{{__('this message will show when any one contact you via contact page form.')}}</small>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
             </div>
        </div>
    </div>
@endsection
