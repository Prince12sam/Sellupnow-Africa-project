@extends('backend.admin-master')
@section('site-title')
    {{__('SEO Settings')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('SEO Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.general.seo.settings')}}" method="POST" class="validateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="site_meta_tags" class="form__input__single__label">{{ __('Site Meta Tags') }}</label>
                            <input type="text" class="form__control radius-5" name="site_meta_tags" id="site_meta_tags" data-role="tagsinput" value="{{get_static_option('site_meta_tags')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="site_meta_description" class="form__input__single__label">{{ __('Site Meta Description') }}</label>
                            <textarea class="form__control" name="site_meta_description"  id="site_meta_description">{{get_static_option('site_meta_description')}}</textarea>
                        </div>
                        <div class="form__input__single">
                            <label for="og_meta_title" class="form__input__single__label">{{__('Og Meta Title')}}</label>
                            <input type="text" class="form__control radius-5" name="og_meta_title" value="{{get_static_option('og_meta_title')}}" id="og_meta_title">
                        </div>
                        <div class="form__input__single">
                            <label for="og_meta_description" class="form__input__single__label">{{__('Og Meta Description')}}</label>
                            <textarea class="form__control" name="og_meta_description" id="og_meta_description">{{get_static_option('og_meta_description')}}</textarea>
                        </div>
                        <div class="form__input__single">
                            <label for="og_meta_site_name" class="form__input__single__label">{{__('Og Meta Site Name')}}</label>
                            <input type="text" class="form__control" name="og_meta_site_name"  value="{{get_static_option('og_meta_site_name')}}" id="og_meta_site_name">
                        </div>
                        <div class="form__input__single">
                            <label for="og_meta_url" class="form__input__single__label">{{__('Og Meta URL')}}</label>
                            <input type="text" class="form__control" name="og_meta_url" id="og_meta_url"   value="{{get_static_option('og_meta_url')}}" >
                        </div>
                        <div class="form__input__single">
                            <label for="og_meta_image" class="form__input__single__label">{{__('Og Meta Image Image')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap">
                                    @php
                                        $og_meta_image = get_attachment_image_by_id(get_static_option('og_meta_image'),null,true);
                                        $og_meta_image_btn_label =__( 'Upload Image');
                                    @endphp
                                    @if (!empty($og_meta_image))
                                        <div class="attachment-preview">
                                            <div class="thumbnail">
                                                <div class="centered">
                                                    <img class="avatar user-thumb" src="{{$og_meta_image['img_url']}}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        @php  $site_breadcrumb_bg_btn_label = __('Change Image'); @endphp
                                    @endif
                                </div>
                                <input type="hidden" id="og_meta_image" name="og_meta_image" value="{{get_static_option('og_meta_image')}}">
                                <button type="button" class="btn btn-info media_upload_form_btn"
                                        data-btntitle="{{__('Select Image')}}"
                                        data-modaltitle="{{__('Upload Image')}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">
                                    {{__($site_breadcrumb_bg_btn_label)}}
                                </button>
                            </div>
                            <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png, Recommended image size 1920x600')}}</small>
                        </div>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <x-media.js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection
