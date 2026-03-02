@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Category')}}
@endsection
@section('style')
    <x-media.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <x-summernote.css/>
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
                <div class="header-wrap d-flex justify-content-between">
                    <div class="left-content">
                        <h4 class="header-title">{{__('Edit Category')}}   </h4>
                    </div>
                    <div class="right-content">
                        <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{route('admin.category')}}">{{__('All Categories')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                 <form action="{{route('admin.category.edit',$category->id)}}" method="post" enctype="multipart/form-data" id="edit_category_form">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="name" class="form__input__single__label">{{__('Name')}}</label>
                            <input type="text" class="form__control radius-5" name="name" id="name" value="{{$category->name}}" placeholder="{{__('Name')}}">
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Description')}}</label>
                            <input type="hidden" name="description" value="{{ $category->description }}">
                            <div class="summernote" data-content="{{ $category->description }}"></div>
                        </div>

                        <div class="form__input__single permalink_label">
                            <label class="text-dark form__input__single__label">{{__('Permalink * :')}}
                                <span id="slug_show" class="display-inline"></span>
                                <span id="slug_edit" class="display-inline">
                                    <button type="button" class="cmnBtn btn_5 btn_bg_warning radius-5 slug_edit_button"> <i class="las la-edit fs-4"></i> </button>
                                    <input type="text" name="slug" class="form__control radius-5 category_slug mt-2" value="{{$category->slug}}" style="display: none">
                                    <button  class="cmnBtn btn_5 btn_bg_info radius-5 slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                </span>
                            </label>
                        </div>

                        <div class="form__input__single">
                            <label for="icon" class="d-block form__input__single__label">{{__('Category Icon')}}</label>
                            <div class="btn-group icon">
                                <button type="button" class="btn btn-primary iconpicker-component">
                                    <i class="{{$category->icon}}"></i>
                                </button>
                                <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                                        data-selected="{{$category->icon}}"
                                        data-bs-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">{{__('Toggle Dropdown')}}</span>
                                </button>
                                <div class="dropdown-menu"></div>
                            </div>
                            <input type="hidden" class="form__control radius-5" name="icon" id="edit_icon" value="{{$category->icon}}">
                        </div>


                        <div class="form__input__single">
                            <label for="image" class="form__input__single__label">{{__('Upload Category Image')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap">
                                    {!! render_image_markup_by_attachment_id($category->image, '','thumb') !!}
                                </div>
                                <input type="hidden" name="image" value="{{$category->image}}">
                                <button type="button" class="cmnBtn btn_5 btn_bg_blue radius-5 media_upload_form_btn"
                                        data-btntitle="{{__('Select Image')}}"
                                        data-modaltitle="{{__('Upload Image')}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">
                                    {{__('Upload Image')}}
                                </button>
                            </div>
                        </div>

                        <!-- meta section start -->
                        <div class="row mt-3">
                            <div class="col-xxl-12 col-lg-12">
                                <div class="collapse_wrapper dashboard__card style_one bg__white padding-20 radius-10">
                                    <div class="collapse_wrapper__header">
                                        <h5 class="collapse_wrapper__header__title">{{ __('Meta Section') }}</h5>
                                    </div>
                                    <div class="tab_wrapper style_seven">
                                        <!--Tab Button  -->
                                        <nav>
                                            <div class="nav nav-tabs" id="nav-tab8" role="tablist">
                                                <a class="nav-link active" id="nav-21-tab"
                                                   data-bs-toggle="tab"
                                                   href="#blog_meta"
                                                   role="tab"
                                                   aria-controls="nav-21"
                                                   aria-selected="true">{{ __('Blog Meta') }}</a>
                                                <a class="nav-link" id="nav-22-tab"
                                                   data-bs-toggle="tab"
                                                   href="#facebook_meta"
                                                   role="tab"
                                                   aria-controls="nav-22"
                                                   aria-selected="false">{{ __('Facebook Meta') }}</a>
                                                <a class="nav-link" id="nav-23-tab"
                                                   data-bs-toggle="tab"
                                                   href="#twitter_meta"
                                                   role="tab"
                                                   aria-controls="nav-23"
                                                   aria-selected="false">{{ __('Twitter Meta') }}</a>
                                            </div>
                                        </nav>
                                        <!--End-of Tab Button  -->
                                        <!-- Tab Contents -->
                                        <div class="tab-content" id="nav-tabContent8">
                                            <div class="tab-pane fade show active" id="blog_meta" role="tabpanel" aria-labelledby="nav-21-tab">
                                                <div class="form__input__flex mt-3">
                                                    <div class="form__input__single">
                                                        <label for="meta_title" class="form__input__single__label">{{__('Meta Title')}}</label>
                                                        <input type="text" class="form__control" name="meta_title" id="meta_title" value="{{$category->metaData->meta_title ?? ''}}" placeholder="{{ __('Title') }}">
                                                    </div>
                                                    <div class="form__input__single">
                                                        <label for="meta_tags" class="form__input__single__label">{{__('Meta Tags')}}</label>
                                                        <input type="text" class="form__control" name="meta_tags" id="meta_tags" value="{{$category->metaData->meta_tags ?? ''}}" data-role="tagsinput" placeholder="{{ __('Tag') }}">
                                                    </div>
                                                    <div class="form__input__single">
                                                        <label for="meta_description" class="form__input__single__label">{{__('Meta Description')}}</label>
                                                        <textarea class="form__control" name="meta_description"  cols="20" rows="4">{!! $category->metaData->meta_description ?? '' !!}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="facebook_meta" role="tabpanel" aria-labelledby="nav-22-tab">
                                                <div class="form__input__single">
                                                    <label for="title" class="form__input__single__label">{{__('Facebook Meta Title')}}</label>
                                                    <input type="text" class="form__control" data-role="tagsinput" name="facebook_meta_tags" value="{{$category->metaData->facebook_meta_tags ?? ''}}">
                                                </div>
                                                <div class="row">
                                                    <div class="form__input__single col-md-12">
                                                        <label for="title" class="form__input__single__label">{{__('Facebook Meta Description')}}</label>
                                                        <textarea name="facebook_meta_description"  class="form__control max-height-140"  cols="20"  rows="4">{!! $category->metaData->facebook_meta_description ?? '' !!}</textarea>
                                                    </div>
                                                </div>
                                                <div class="form__input__single">
                                                    <label for="image" class="form__input__single__label">{{__('Facebook Meta Image')}}</label>
                                                    <div class="media-upload-btn-wrapper">
                                                        <div class="img-wrap">
                                                            {!! render_attachment_preview_for_admin($category->metaData->facebook_meta_image ?? '') !!}
                                                        </div>
                                                        <input type="hidden" name="facebook_meta_image" value="{{$category->metaData->facebook_meta_image ?? ''}}">
                                                        <button type="button" class="cmnBtn btn_5 btn_bg_blue radius-5 media_upload_form_btn"
                                                                data-btntitle="{{__('Select Image')}}"
                                                                data-modaltitle="{{__('Upload Image')}}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#media_upload_modal">
                                                            {{__('Upload Image')}}
                                                        </button>
                                                        <span class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png,webp')}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="twitter_meta" role="tabpanel" aria-labelledby="nav-22-tab">
                                                <div class="form__input__single">
                                                    <label for="title" class="form__input__single__label">{{__('Twitter Meta Title')}}</label>
                                                    <input type="text" class="form__control" data-role="tagsinput"  name="twitter_meta_tags" value="{{$category->metaData->twitter_meta_tags ?? ''}}">
                                                </div>
                                                <div class="row">
                                                    <div class="form__input__single col-md-12">
                                                        <label for="title">{{__('Twitter Meta Description')}}</label>
                                                        <textarea name="twitter_meta_description" class="form__control max-height-140" cols="20" rows="4">{!! $category->metaData->twitter_meta_description ?? '' !!}</textarea>
                                                    </div>
                                                </div>
                                                <div class="form__input__single">
                                                    <label for="image" class="form__input__single__label">{{__('Twitter Meta Image')}}</label>
                                                    <div class="media-upload-btn-wrapper">
                                                        <div class="img-wrap">
                                                            {!! render_attachment_preview_for_admin($category->metaData->twitter_meta_image ?? '') !!}
                                                        </div>
                                                        <input type="hidden" name="twitter_meta_image" value="{{$category->metaData->twitter_meta_image ?? ''}}">
                                                        <button type="button"
                                                                class="cmnBtn btn_5 btn_bg_blue radius-5 media_upload_form_btn"
                                                                data-btntitle="{{__('Select Image')}}"
                                                                data-modaltitle="{{__('Upload Image')}}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#media_upload_modal">
                                                            {{__('Upload Image')}}
                                                        </button>
                                                        <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png')}}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- meta section end -->
                      </div>
                     <div class="btn_wrapper mt-4">
                         <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update') }}</button>
                     </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js />
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script src="{{asset('assets/backend/js/fontawesome-iconpicker.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/backend/css/fontawesome-iconpicker.min.css')}}">
    <x-summernote.js/>
    <script>
        <x-icon.icon-picker/>
    </script>
    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {
                //zone
                $(document).ready(function () {
                    $('.zone_settings').select2();
                });
                //Permalink Code
                var sl =  $('.category_slug').val();
                var url = `{{url('/listing/category/')}}/` + sl;
                var data = $('#slug_show').text(url).css('color', 'blue');

                function converToSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    //remove multiple space to single
                    finalSlug = slug.replace(/  +/g, ' ');
                    // remove all white spaces single or multiple spaces
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
                    var update_input = $('.category_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/listing/category/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.category_slug').val(slug)
                    $('.category_slug').hide();
                });

                // for summernote
                $('.summernote').summernote({
                    height: 400,   //set editable area's height
                    codemirror: { // codemirror options
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function (contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
                if ($('.summernote').length > 0) {
                    $('.summernote').each(function (index, value) {
                        $(this).summernote('code', $(this).data('content'));
                    });
                }
            });
        })(jQuery)
    </script>
@endsection
