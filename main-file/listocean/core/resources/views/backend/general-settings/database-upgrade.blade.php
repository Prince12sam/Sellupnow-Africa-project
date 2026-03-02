@extends('backend.admin-master')
@section('site-title')
    {{__('Database Upgrade')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="col-12 mt-5">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <h2 class="dashboard__card__header__title mb-3">{{__("Database Upgrade")}}</h2>
                    <x-validation.error/>
                    <form action="{{route('admin.general.database.upgrade')}}" method="POST" id="cache_settings_form">
                        @csrf
                        <div class="btn_wrapper mt-4">
                            <button class="cmnBtn btn_5 btn_bg_blue radius-5 clear-cache-submit-btn" id="view" data-value="view">{{__('Database Upgrade')}}</button>
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
                $(this).html('<i class="fas fa-spinner fa-spin"></i> {{__("Proccesing")}}')
            });
        });
    })(jQuery);
</script>
@endsection
