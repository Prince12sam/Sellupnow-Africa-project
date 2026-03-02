@extends('backend.admin-master')
@section('site-title')
    {{__('Add Blog')}}
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
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Add Blog') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <a href="{{ route('admin.all.blog') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('All Blog')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.blog.new')}}" method="POST" enctype="multipart/form-data" id="blog_new_form">
                    @csrf
                    <div class="form__input__flex mt-3">
                        <div class="form__input__single">
                            <label for="title" class="form__input__single__label">{{__('Title')}} <span class="text-danger">*</span> </label>
                            <input type="text" class="form__control" name="title" id="title" placeholder="{{ __('Title') }}">
                        </div>
                        <div class="form__input__single permalink_label">
                            <label class="text-dark form__input__single__label">{{__('Permalink * :')}}
                                <span id="slug_show" class="display-inline"></span>
                                <span id="slug_edit" class="display-inline">
                                  <button class="btn btn-warning btn-sm slug_edit_button"> <i class="fas fa-edit"></i> </button>
                                  <input type="text" name="slug" class="form-control blog_slug mt-2" style="display: none">
                                  <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                </span>
                            </label>
                        </div>
                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Blog Content')}} <span class="text-danger">*</span> </label>
                            <input type="hidden" name="blog_content">
                            <div class="summernote"></div>
                        </div>
                        <div class="form__input__single">
                            <label for="title" class="form__input__single__label">{{__('Excerpt')}}</label>
                            <textarea name="excerpt" class="form-control max-height-150" cols="20" rows="5"></textarea>
                        </div>
                    </div>
                    <x-meta.meta-section/>
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
                                <strong>{{__('Select Categories')}} <span class="text-danger">*</span> </strong>
                            </label>
                            <div class="category-section">
                                <select name="category_id" id="category_id" class="form__control select2_activation">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach($all_category as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form__input__single">
                            <label for="featured" class="form__input__single__label"><strong>{{__('Featured')}}</strong></label>
                            <div class="switch_box style_7">
                                <input type="checkbox" name="featured">
                                <label></label>
                            </div>
                        </div>

                        <div id="category_list" class="form__input__single">
                            <label for="visibility" class="form__input__single__label">{{__('Visibility')}}</label>
                            <select name="visibility" class="form__control" id="visibility">
                                <option value="public">{{__('Public')}}</option>
                                <option value="logged_user">{{__('Logged User')}}</option>
                            </select>
                        </div>
                        <div class="form__input__single">
                            <label for="status" class="form__input__single__label">{{__('Status')}}</label>
                            <select name="status" class="form__control" id="status">
                                <option value="draft">{{__("Draft")}}</option>
                                <option value="publish">{{__("Publish")}}</option>
                                <option value="archive">{{__("Archive")}}</option>
                                <option class="selected_schedule"  value="schedule">{{__("Schedule")}}</option>
                            </select>
                            <input type="date" name="schedule_date" class="form-control mt-2 date" style="display: none" id="tag_data">
                        </div>
                        <div class="form__input__single">
                            <label for="image" class="form__input__single__label">{{__('Blog Image')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap"></div>
                                <input type="hidden" name="image">
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
    <x-summernote.js/>
    <x-media.js/>
    <script>
        //Date Picker
        flatpickr('#tag_data', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today"
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
                            html += '<li class="tag_option" data-id="'+key+'"  data-val="'+tag+'">' + tag + '</li>'
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

                //Permalink Code
                $('.permalink_label').hide();
                $(document).on('keyup', '#title', function (e) {
                    var slug = makeSlug($(this).val());
                    var url = `{{url('/blog/')}}/` + slug;
                    $('.permalink_label').show();
                    var data = $('#slug_show').text(url).css('color', 'blue');
                    $('.blog_slug').val(slug);

                });

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
                    var url = `{{url('/blog/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.blog_slug').val(slug);
                    $('.blog_slug').hide();
                });

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

                var el = $('.post_type_radio');
                $(document).on('change', '.post_type', function () {
                    var val = $(this).val();
                    if (val === 'option2') {
                        $('.video_section').show();
                    } else {
                        $('.video_section').hide();
                    }
                });

            });
        })(jQuery)
    </script>
@endsection
