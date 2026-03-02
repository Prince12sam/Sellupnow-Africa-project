@extends('backend.admin-master')
@section('site-title')
    {{__('Site Identity Settings')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
<div class="row g-4 mt-0">
    <div class="col-xl-6 col-lg-6 mt-0">
        <div class="dashboard__card bg__white padding-20 radius-10">
            <div class="form__input__flex">
                <div class="form__input__single">
                    <h2 class="dashboard__card__header__title mb-3">{{__("Site Identity Settings")}}</h2>
                    <x-validation.error/>
                    <form action="{{route('admin.general.site.identity')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                    <x-image.image :title="__('Site Logo')" :name="'site_logo'" :dimentions="'160x50'"/>
                    <x-image.image :title="__('Site White Logo')" :name="'site_white_logo'" :dimentions="'160x50'"/>
                    <x-image.image :title="__('Favicon')" :name="'site_favicon'" :dimentions="'40x40'"/>
                   <div class="mt-3">
                       <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Update Changes')}}</button>
                   </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>
</div>
<x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function () {
                <x-btn.update/>
            });
        })(jQuery);
    </script>
@endsection
