@extends('backend.admin-master')
@section('site-title')
    {{__('Listing Unpublished Template')}}
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
                        <h2 class="dashboard__card__header__title mb-3">{{__('Listing Unpublished Template')}}</h2>
                    </div>
                    <div class="dashboard_orderDetails__header__right">
                        <a href="{{route('admin.email.template.all')}}" class="cmnBtn btn_5 btn_bg_info radius-5">{{__('All Email Templates')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.email.user.new.listing.unpublished.template')}}" method="POST">
                    @csrf
                    <div class="form__input__single">
                        <label for="listing_unpublished_subject" class="form__input__single__label">{{__('Email Subject')}}</label>
                        <input type="text" class="form__control radius-5" name="listing_unpublished_subject" value="{{get_static_option('listing_unpublished_subject') ?? 'Listing unpublished'}}">
                    </div>
                    <div class="form__input__single">
                        <label for="listing_unpublished_message" class="form__input__single__label">{{__('Email Message for User')}}</label>
                        <textarea class="form__control summernote" name="listing_unpublished_message">{!! get_static_option('listing_unpublished_message') ?? '<p>Hello,</p></p> Your listing has been unpublished. Thanks. Listing ID: @listing_id </p>'  !!} </textarea>
                        <div class="d-grid">
                            <small class="form-text"><strong class="text-danger"> @listing_id </strong>{{__('will be replaced by dynamically with listing_id.')}}</small>
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
