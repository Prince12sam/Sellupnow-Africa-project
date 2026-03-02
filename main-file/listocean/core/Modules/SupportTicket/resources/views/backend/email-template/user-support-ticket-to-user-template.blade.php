@extends('backend.admin-master')
@section('site-title')
    {{__('Support Ticket Message To User Email Template')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard_orderDetails__header__flex">
                    <div class="dashboard_orderDetails__header__left">
                         <h2 class="dashboard__card__header__title mb-3">{{__('Support Ticket Message To User Email Template')}}</h2>
                    </div>
                    <div class="dashboard_orderDetails__header__right">
                        <a href="{{route('admin.email.template.all')}}" class="cmnBtn btn_5 btn_bg_info radius-5">{{__('All Email Templates')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.user.support.ticket.to.user.template')}}" method="POST">
                    @csrf
                    <div class="form__input__single">
                        <label for="support_ticket_subject" class="form__input__single__label">{{__('Email Subject')}}</label>
                        <input type="text" class="form__control radius-5" name="support_ticket_subject" value="{{get_static_option('support_ticket_subject') ?? 'Support Ticket Message Email'}}">
                    </div>
                    <div class="form__input__single">
                        <label for="support_ticket_message" class="form__input__single__label">{{__('Email Message')}}</label>
                        <textarea class="form__control summernote" name="support_ticket_message">{!! get_static_option('support_ticket_message') ?? 'Hello @name, You have a new message for the bellow ticket Ticket ID : #@ticket_id'  !!} </textarea>
                    </div>
                    <div class="d-grid">
                        <small class="form-text"><strong class="text-danger"> @name </strong>{{__('will be replaced by dynamically with name.')}}</small>
                        <small class="form-text"><strong class="text-danger"> @ticket_id </strong>{{__('will be replaced by dynamically with ticket_id.')}}</small>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
