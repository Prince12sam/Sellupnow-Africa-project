@extends('backend.admin-master')
@section('site-title')
    {{__('Cache Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="col-12 mt-5">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__("Cache Settings")}}</h2>
                    <x-validation.error/>
                    <form action="{{route('admin.general.cache.settings')}}" method="POST" id="cache_settings_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="cache_type" id="cache_type" class="form-control">

                        <div class="btn_wrapper mt-4">
                            <button class="cmnBtn btn_5 btn_bg_blue radius-5 clear-cache-submit-btn" id="view" data-value="view">{{__('Clear View Cache')}}</button>
                        </div>
                        <div class="btn_wrapper mt-4">
                             <button class="cmnBtn btn_5 btn_bg_blue radius-5 clear-cache-submit-btn" id="route" data-value="route">{{__('Clear Route Cache')}}</button>
                        </div>
                        <div class="btn_wrapper mt-4">
                          <button class="cmnBtn btn_5 btn_bg_blue radius-5 clear-cache-submit-btn" id="config" data-value="config">{{__('Clear Configure Cache')}}</button>
                        </div>
                        <div class="btn_wrapper mt-4">
                           <button class="cmnBtn btn_5 btn_bg_blue radius-5 clear-cache-submit-btn" id="clear" data-value="cache">{{__('Clear Cache')}}</button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                $(document).on('click','.clear-cache-submit-btn',function(e){
                    e.preventDefault();
                    $(this).addClass("disabled")
                    $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> {{__("Cleaning Cache")}}');
                    $('#cache_type').val($(this).data('value'));
                    $('#cache_settings_form').trigger('submit');
                });
            });
        })(jQuery);
    </script>
@endsection
