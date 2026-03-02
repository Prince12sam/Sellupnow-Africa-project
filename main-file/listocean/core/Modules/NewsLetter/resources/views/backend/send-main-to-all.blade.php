@extends('backend.admin-master')
@section('style')
    <x-summernote.css />
@endsection
@section('site-title')
    {{ __('Send Mail To All Newsletter Subscriber') }}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <x-msg.success/>
                <x-msg.error/>
                <h4 class="dashboard__card__header__title mb-3">{{ __('Send Mail To All Newsletter Subscriber') }}</h4>
                <div class="dashboard__card__body custom__form mt-4">
                    <form action="{{ route('admin.newsletter.mail') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="edit_icon">{{ __('Subject') }}</label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                placeholder="{{ __('Subject') }}">
                        </div>
                        <div class="form-group">
                            <label for="message">{{ __('Message') }}</label>
                            <input type="hidden" name="message">
                            <div class="summernote"></div>
                        </div>
                        <div class="btn_wrapper mt-4">
                            <button id="submit" type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Send Mail')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <x-summernote.js />
@endsection
