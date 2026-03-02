@extends('backend.admin-master')
@section('site-title')
    {{__('Add New Category')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/fontawesome-iconpicker.min.css')}}">
    <x-summernote.css/>
    <x-media.css/>
    <style>
        span {
            display: inline;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="header-wrap d-flex justify-content-between mb-4">
                    <div class="left-content">
                        <h4 class="header-title">{{__('Add New Category')}}   </h4>
                    </div>
                    <div class="right-content">
                        <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{route('admin.category')}}">{{__('All Categories')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.category.new')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="name" class="form__input__single__label">{{__('Name')}}</label>
                            <input type="text" class="form__control radius-5" name="name" id="name" placeholder="{{__('Name')}}">
                        </div>
                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Description')}}</label>
                            <input type="hidden" name="description">
                            <div class="summernote"></div>
                        </div>
                        <div class="form__input__single permalink_label">
                            <label class="text-dark">{{__('Permalink * :')}}
                                <span id="slug_show" class="display-inline"></span>
                                <span id="slug_edit" class="display-inline">
                                    <button class="cmnBtn btn_5 btn_bg_warning radius-5 slug_edit_button"><i class="las la-edit fs-4"></i></button>
                                    <input type="text" name="slug" class="form__control radius-5 category_slug mt-2" style="display: none">
                                    <button class="cmnBtn btn_5 btn_bg_blue radius-5 slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                </span>
                            </label>
                        </div>

                        <x-icon.icon-add/>

                        <div class="form__input__single">
                            <label for="image" class="form__input__single__label">{{__('Upload Category Image')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap"></div>
                                <input type="hidden" name="image">
                                <button type="button" class="cmnBtn btn_5 btn_bg_blue radius-5 media_upload_form_btn"
                                        data-btntitle="{{__('Select Image')}}"
                                        data-modaltitle="{{__('Upload Image')}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">
                                    {{__('Upload Image')}}
                                </button>
                            </div>
                        </div>
                        <x-meta.meta-section/>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script src="{{asset('assets/backend/js/fontawesome-iconpicker.min.js')}}"></script>
    <script>
        <x-icon.icon-picker/>
    </script>
    <x-summernote.js/>
    <x-media.js />
    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {
                //Permalink Code
                $('.permalink_label').hide();
                $(document).on('keyup', '#name', function (e) {
                    let slug = converToSlug($(this).val());
                    let url = "{{url('/listing/category/')}}/" + slug;
                    $('.permalink_label').show();
                    let data = $('#slug_show').text(url).css('color', 'blue');
                    $('.category_slug').val(slug);

                });
                function converToSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }
                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.category_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    let update_input = $('.category_slug').val();
                    let slug = converToSlug(update_input);
                    let url = `{{url('/listing/category/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.category_slug').val(slug)
                    $('.category_slug').hide();
                });
            });
        })(jQuery)
    </script>
@endsection
