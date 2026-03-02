@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Advertisement')}}
@endsection
@section('style')
    <x-media.css/>
    <style>
        .img-wrap img {
            max-width: 109px;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-6">
            <div class="dashboard__card bg__white padding-20 radius-10 mb-2">
                <x-validation.error/>
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Edit Advertisement') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <a href="{{route('admin.advertisement')}}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('All Advertisements') }}</a>
                        </div>
                    </div>
                </div>
                <div class="tableStyle_three mt-4">
                    <form action="{{route('admin.advertisement.update',$advertisement->id)}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="tab-content margin-top-40">
                            <div class="row">
                                <div class="form-group col-md-12" id="title">
                                    <label for="title">{{__('Title')}}</label>
                                    <input type="text" class="form-control" name="title" value="{{$advertisement->title}}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="title">{{__('Advertisement Type')}}</label>
                                    <select class="form-control" name="type" id="type">
                                        <option selected disabled>{{__('Select a Type')}}</option>
                                        <option @if($advertisement->type === 'image') selected @endif value="image">{{__('Image')}}</option>
                                        <option @if($advertisement->type === 'google_adsense') selected  @endif value="google_adsense">{{__('Google Adsense')}}</option>
                                        <option @if($advertisement->type === 'scripts') selected  @endif value="scripts">{{__('Scripts')}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="title">{{__('Advertisement Size')}}</label>
                                    <select class="form-control" name="size" id="size">
                                        <option selected disabled>{{__('Select a Size')}}</option>
                                        <option @if($advertisement->size === '350*250') selected @endif value="350*250">{{__('350 x 250')}}</option>
                                        <option @if($advertisement->size === '320*50') selected @endif value="320*50">{{__('320 x 50')}}</option>
                                        <option @if($advertisement->size === '160*600') selected @endif value="160*600">{{__('160 x 600')}}</option>
                                        <option @if($advertisement->size === '300*600') selected @endif value="300*600">{{__('300 x 600')}}</option>
                                        <option @if($advertisement->size === '336*280') selected @endif value="336*280">{{__('336 x 280')}}</option>
                                        <option @if($advertisement->size === '728*90') selected @endif value="728*90">{{__('728 x 90')}}</option>
                                        <option @if($advertisement->size === '730*180') selected @endif value="730*180">{{__('730 x 180')}}</option>
                                        <option @if($advertisement->size === '730*210') selected @endif value="730*210">{{__('730 x 210')}}</option>
                                        <option @if($advertisement->size === '300*1050') selected @endif value="300*1050">{{__('300 X 1050')}}</option>
                                        <option @if($advertisement->size === '950*160') selected @endif value="950*160">{{__('950 X 160')}}</option>
                                        <option @if($advertisement->size === '950*200') selected @endif value="950*200">{{__('950 X 200')}}</option>
                                        <option @if($advertisement->size === '250*1110') selected @endif value="250*1110">{{__('250 X 1110')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-12" id="slot" style="display: none">
                                    <label for="title">{{__('Advertisement Slot')}}</label>
                                    <input type="text" class="form-control" name="slot" value="{{$advertisement->slot}}">
                                </div>

                                <div class="form-group col-md-12" style="display: none" id="embed_code">
                                    <label for="title">{{__('Embed Code')}}</label>
                                    <textarea class="form-control" name="embed_code">{{$advertisement->embed_code}}</textarea>
                                </div>

                                <div class="form-group col-md-12" style="display: none" id="redirect_url">
                                    <label for="title">{{__('Redirect URL')}}</label>
                                    <input type="text" class="form-control" name="redirect_url" value="{{$advertisement->redirect_url}}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="title">{{__('Status')}}</label>
                                    <select class="form-control" name="status">
                                        <option @if($advertisement->status === 0) selected @endif value="0">{{__('Inactive')}}</option>
                                        <option @if($advertisement->status === 1) selected @endif value="1">{{__('Active')}}</option>
                                    </select>
                                </div>
                            </div>

                            <x-image.image :title="'Advertisement Image'" :name="'image'" :id="$advertisement->image" :value="$advertisement->image" :class="'image'"/>

                            <button id="submit" type="submit" class="btn btn-primary mt-5 submit_btn">{{__('Submit Advertise ')}}</button>
                        </div>
                    </form>
                </div>
            </div>
         </div>
    </div>
 <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                <x-btn.submit/>

                if($('#type').val() === 'image') {
                    $('.image').show();
                    $('#redirect_url').show();
                 }else if($('#type').val() === 'google_adsense') {
                    $('#slot').show();
                    $('.image').hide();

                }else if($('#type').val() === 'scripts'){
                    $('#embed_code').show();
                    $('.image').hide();
                }else{
                    $('.image').hide();
                    $('#slot').hide();
                    $('#embed_code').hide();
                }

                $(document).on('change','#type',function(e){
                    e.preventDefault();
                    let el = $(this).val();
                    if(el === 'image'){
                        $('.image').show();
                        $('#redirect_url').show();
                        $('#slot').hide();
                        $('#embed_code').hide();

                    }else if(el === 'google_adsense'){
                        $('#slot').show();
                        $('#redirect_url').hide();
                        $('#embed_code').hide();
                        $('.image').hide();

                    }else if(el === 'scripts'){
                        $('#embed_code').show();
                        $('#slot').hide();
                        $('#redirect_url').hide();
                        $('.image').hide();

                    }else{
                        $('#redirect_url').hide();
                    }
                });
            });
        })(jQuery);
    </script>
@endsection

