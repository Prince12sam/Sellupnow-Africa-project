@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/codemirror.css')}}">
@endsection
@section('site-title')
    {{__('Custom Js')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Custom Js')}}</h2>
                 <p class="margin-bottom-30">{{__('you can only add js code here. no other code work here.')}}</p>
                <x-validation.error/>
                <form action="{{route('admin.general.custom.js')}}" method="POST">
                    @csrf
                    <div class="form__input__single">
                        <textarea name="custom_js_area" id="custom_js_area" cols="30" rows="10">{{$custom_js}}</textarea>
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
    <script src="{{asset('assets/backend/js/codemirror.js')}}"></script>
    <script>
        (function($) {
            "use strict";
            var editor = CodeMirror.fromTextArea(document.getElementById("custom_js_area"), {
                lineNumbers: true,
                mode: "text/javascript",
                matchBrackets: true
            });
        })(jQuery);
    </script>
@endsection
