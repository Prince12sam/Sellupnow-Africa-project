@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Blog')}}
@endsection
@section('style')
    <x-summernote.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <x-media.css/>
    <x-css.blog-inline-css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h2 class="dashboard__card__header__title mb-3">{{__('Edit Blog Items')}}</h2>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <a href="{{ route('admin.all.blog') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('All Blogs') }}</a>
                                <a href="{{ route('admin.blog.new') }}" class="cmnBtn btn_5 btn_bg_info radius-5">{{ __('Create New') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.blog.update',$blog_post->id)}}" method="POST" enctype="multipart/form-data" id="blog_new_form">
                    @csrf

                    <div class="form__input__single">
                        <label for="title" class="form__input__single__label">{{__('Title')}} <span class="text-danger">*</span> </label>
                        <input type="text" class="form__control radius-5" name="title" value="{{ $blog_post->title }}" placeholder="{{__('title')}}">
                    </div>

                    <div class="form__input__single permalink_label">
                        <label class="text-dark form__input__single__label">{{__('Permalink:')}} <span class="text-danger">*</span>
                            <span id="slug_show" class="display-inline"></span>
                            <span id="slug_edit" class="display-inline">
                              <button class="btn btn-warning btn-sm slug_edit_button"> <i class="fas fa-edit"></i> </button>
                              <input type="text" name="slug" value="{{$blog_post->slug}}" class="form__control radius-5 blog_slug mt-2" style="display: none">
                              <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                        </span>
                        </label>
                    </div>

                    <div class="form__input__single">
                        <label  for="blog_content" class="form__input__single__label">{{__('Blog Content')}} <span class="text-danger">*</span> </label>
                        <input type="hidden" name="blog_content" value="{{ $blog_post->blog_content }}">
                        <div class="summernote" data-content="{{ $blog_post->blog_content }}"></div>
                    </div>

                    <div class="form__input__single">
                        <label for="title" class="form__input__single__label">{{__('Excerpt')}}</label>
                        <textarea name="excerpt" id="excerpt" class="form__control max-height-150" cols="30" rows="10">{{ $blog_post->excerpt }}</textarea>
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
                                                    <input type="text" class="form__control" name="meta_title" id="meta_title" value="{{$blog_post->meta_data->meta_title ?? ''}}" placeholder="{{ __('Title') }}">
                                                </div>
                                                <div class="form__input__single">
                                                    <label for="meta_tags" class="form__input__single__label">{{__('Meta Tags')}}</label>
                                                    <input type="text" class="form__control" name="meta_tags" id="meta_tags" value="{{$blog_post->meta_data->meta_tags ?? ''}}" data-role="tagsinput" placeholder="{{ __('Tag') }}">
                                                </div>
                                                <div class="form__input__single">
                                                    <label for="meta_description" class="form__input__single__label">{{__('Meta Description')}}</label>
                                                    <textarea class="form__control" name="meta_description"  cols="20" rows="4">{!! $blog_post->meta_data->meta_description ?? '' !!}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="facebook_meta" role="tabpanel" aria-labelledby="nav-22-tab">
                                            <div class="form__input__single">
                                                <label for="title" class="form__input__single__label">{{__('Facebook Meta Title')}}</label>
                                                <input type="text" class="form__control" data-role="tagsinput" name="facebook_meta_tags" value="{{$blog_post->meta_data->facebook_meta_tags ?? ''}}">
                                            </div>
                                            <div class="row">
                                                <div class="form__input__single col-md-12">
                                                    <label for="title" class="form__input__single__label">{{__('Facebook Meta Description')}}</label>
                                                    <textarea name="facebook_meta_description"  class="form__control max-height-140"  cols="20"  rows="4">{!! $blog_post->meta_data->facebook_meta_description ?? '' !!}</textarea>
                                                </div>
                                            </div>
                                            <div class="form__input__single">
                                                <label for="image" class="form__input__single__label">{{__('Facebook Meta Image')}}</label>
                                                <div class="media-upload-btn-wrapper">
                                                    <div class="img-wrap">
                                                        {!! render_attachment_preview_for_admin($blog_post->meta_data->facebook_meta_image ?? '') !!}
                                                    </div>
                                                    <input type="hidden" name="facebook_meta_image" value="{{$blog_post->meta_data->facebook_meta_image ?? ''}}">
                                                    <button type="button" class="cmnBtn btn_5 btn_bg_secondary radius-5 media_upload_form_btn"
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
                                                <input type="text" class="form__control" data-role="tagsinput"  name="twitter_meta_tags" value="{{$blog_post->meta_data->twitter_meta_tags ?? ''}}">
                                            </div>
                                            <div class="row">
                                                <div class="form__input__single col-md-12">
                                                    <label for="title">{{__('Twitter Meta Description')}}</label>
                                                    <textarea name="twitter_meta_description" class="form__control max-height-140" cols="20" rows="4">{!! $blog_post->meta_data->twitter_meta_description ?? '' !!}</textarea>
                                                </div>
                                            </div>
                                            <div class="form__input__single">
                                                <label for="image" class="form__input__single__label">{{__('Twitter Meta Image')}}</label>
                                                <div class="media-upload-btn-wrapper">
                                                    <div class="img-wrap">
                                                        {!! render_attachment_preview_for_admin($blog_post->meta_data->twitter_meta_image ?? '') !!}
                                                    </div>
                                                    <input type="hidden" name="twitter_meta_image" value="{{$blog_post->meta_data->twitter_meta_image ?? ''}}">
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
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Post Type')}}</h2>
                <div class="form__input__flex">
                    <div class="form__input__single">
                        <div class="form-check form-check-inline d-block">
                            <input class="form-check-input post_type" type="radio" checked name="inlineRadioOptions" id="radio_general" value="option1">
                            <i class="las la-cog"></i>
                            <label class="form-check-label" for="inlineRadio1">{{__('General')}}</label>
                        </div>
                    </div>

                    <div class="form__input__single">
                        <label for="featured" class="form__input__single__label">
                            <strong>{{__('Select Categories')}} <span class="text-danger">*</span></strong>
                        </label>
                        <div class="category-section">
                            <select name="category_id" id="category_id" class="form__control select2_activation">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach($all_category as $category)
                                    <option value="{{ $category->id }}" @if($blog_post->category_id==$category->id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form__input__single" id="blog_tag_list">
                        <label for="title" class="form__input__single__label">{{__('Blog Tag')}}</label>
                        <div class="category-section">
                            <select name="tag_id[]" id="tag_id" class="form__control select2_activation" multiple>
                                <option value="" disabled>{{ __('Select Tag') }}</option>
                                @foreach($all_tags as $tag)
                                    <option value="{{ $tag->id }}" @if(in_array($tag->id, $blog_post->tags->pluck('id')->toArray())) selected @endif>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="form__input__single">
                        <label for="featured" class="form__input__single__label"><strong>{{__('Featured')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox" name="featured" @if($blog_post->featured) checked @endif>
                            <label></label>
                        </div>
                    </div>

                    <div id="category_list" class="form__input__single">
                        <label for="visibility" class="form__input__single__label">{{__('Visibility')}}</label>
                        <select name="visibility" class="form__control" id="visibility">
                            <option value="public" @if($blog_post->visibility == 'public') selected @endif>{{__('Public')}}</option>
                            <option value="logged_user" @if($blog_post->visibility == 'logged_user') selected @endif>{{__('Logged User')}}</option>
                        </select>
                    </div>
                    <div class="form__input__single">
                        <label for="status" class="form__input__single__label">{{__('Status')}}</label>
                        <select name="status" class="form__control" id="status">
                            <option value="draft" @if($blog_post->status == 'draft') selected @endif>{{__("Draft")}}</option>
                            <option value="publish" @if($blog_post->status == 'publish') selected @endif>{{__("Publish")}}</option>
                            <option value="archive" @if($blog_post->status == 'archive') selected @endif>{{__("Archive")}}</option>
                            <option class="selected_schedule" value="schedule" @if($blog_post->status == 'schedule') selected @endif>{{__("Schedule")}}</option>
                        </select>
                        <input type="date" name="schedule_date" class="form__control mt-2 date" value="{{$blog_post->schedule_date ?? ''}}" style="display: none" id="edit_schedule">
                    </div>
                    <div class="form__input__single">
                        <label for="image" class="form__input__single__label">{{__('Blog Image')}}</label>
                        <div class="media-upload-btn-wrapper">
                            <div class="img-wrap">
                                {!! render_attachment_preview_for_admin($blog_post->image ?? '') !!}
                            </div>
                            <input type="hidden" name="image" value="{{$blog_post->image}}">
                            <button type="button" class="cmnBtn btn_5 btn_bg_secondary radius-5 media_upload_form_btn"
                                    data-btntitle="{{__('Select Image')}}"
                                    data-modaltitle="{{__('Upload Image')}}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#media_upload_modal">
                                {{__('Upload Image')}}
                            </button>
                        </div>
                    </div>
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
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <x-summernote.js/>
    <x-media.js/>
    <script>
        //Date Picker
        flatpickr('#edit_schedule', {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d H:i",
        });
        var blogTagInput = $('#blog_tag_list .tags_filed');
        var oldTag = '';
        blogTagInput.tagsinput();
        //For Tags
        $(document).on('keyup','#blog_tag_list .bootstrap-tagsinput input[type="text"]',function (e) {
            e.preventDefault();
            var el = $(this);
            var inputValue = $(this).val();
            $.ajax({
                type: 'get',
                url :  "{{ route('admin.get.tags.by.ajax') }}",
                async: false,
                data: {
                    query: inputValue
                },
                success: function (data){
                    oldTag = inputValue;
                    let html = '';
                    var showAutocomplete = '';
                    $('#show-autocomplete').html('<ul class="autocomplete-warp"></ul>');
                    if(el.val() != '' && data.markup != ''){
                        data.result.map(function (tag, key) {
                            html += '<li class="tag_option" data-id="'+key+'" data-val="'+tag+'">' + tag + '</li>'
                        })
                        $('#show-autocomplete ul').html(html);
                        $('#show-autocomplete').show();
                    } else {
                        $('#show-autocomplete').hide();
                        oldTag = '';
                    }
                },
                error: function (res){
                }
            });
        });

        $(document).on('click', '.tag_option', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let tag = $(this).data('val');
            blogTagInput.tagsinput('add', tag);
            $(this).parent().remove();
            blogTagInput.tagsinput('remove', oldTag);
        });

    </script>

    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {

                //Status Code
                if($('#status').val() == 'schedule') {
                    $('.date').show();
                    $('.date').focus();
                }

                $(document).on('change','#status',function(e){
                    e.preventDefault();
                    if ($(this).val() == 'schedule') {
                        $('.date').show();
                        $('.date').focus();
                    } else {
                        $('.date').hide();
                    }
                });

                //Permalink Code
                var sl =  $('.blog_slug').val();
                var slugPrefix = "{{getSlugFromReadingSetting('blog_page') ?? 'blog'}}";
                var url = `{{url('/')}}/`+slugPrefix+'/' + sl;
                var data = $('#slug_show').text(url).css('color', 'blue');
                var form = $('#blog_new_form');

                function makeSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }
                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.blog_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.blog_slug').val();
                    var slug = makeSlug(update_input);
                    var slugPrefix = "{{getSlugFromReadingSetting('blog_page') ?? 'blog'}}";
                    var url = `{{url('/')}}/`+slugPrefix+'/' + slug;
                    $('#slug_show').text(url);
                    $('.blog_slug').val(slug);
                    $('.blog_slug').hide();
                });
                checkPostStatus();
                function checkPostStatus(){
                    if ($('#status').val() == 'schedule') {
                        $('.date').show();
                        $('.date').focus();
                    } else {
                        $('.date').hide();
                    }
                }

                $(document).on('change','#status',function(e){
                    e.preventDefault();
                    if ($(this).val() == 'schedule') {
                        $('.date').show();
                        $('.date').focus();
                    } else {
                        $('.date').hide();
                    }
                });


                <x-btn.submit/>
                $(document).on('change', '#langchange', function (e) {
                    $('#langauge_change_select_get_form').trigger('submit');
                });

                $(document).on('change', '.post_type', function () {
                    var val = $(this).val();
                    if (val === 'option2') {
                        $('.video_section').show();
                    } else {
                        $('.video_section').hide();
                    }
                });
            });

            $('.summernote').summernote({
                height: 400,
                codemirror: {
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

        })(jQuery)
    </script>
@endsection
