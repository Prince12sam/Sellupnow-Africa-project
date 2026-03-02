@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Page')}}
@endsection
@section('style')
    <x-summernote.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <x-media.css/>
    <style>
        #slug_edit .form-control {
            height: 30px;
            width: 100%;
        }

        .slug_edit_button {
            line-height: 0px;
            margin: 0;
            padding: 8px 8px;
        }

        .slug_update_button {
            line-height: 0px;
            margin: 0;
            padding: 12px;
        }

        .meta .flex-column{
            background-color: #f2f2f2;
        }

        .meta .flex-column a{
            color: #0c0c0c;
        }

        .display-inline{
            display: inline;
        }

    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h2 class="dashboard__card__header__title mb-3">{{__('Edit Page')}}</h2>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <a href="{{ route('admin.page') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('All Pages') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.page.update',$page_post->id)}}" method="POST" enctype="multipart/form-data" id="blog_new_form">
                    @csrf

                    <input type="hidden" name="lang" value="{{$default_lang}}">

                    <div class="form__input__single">
                        <label for="title" class="form__input__single__label">{{__('Title')}} <span class="text-danger">*</span> </label>
                        <input type="text" class="form__control radius-5" name="title" value="{{ $page_post->title }}" placeholder="{{__('title')}}">
                    </div>

                    <div class="form__input__single permalink_label">
                        <label class="text-dark form__input__single__label">{{__('Permalink:')}} <span class="text-danger">*</span>
                            <span id="slug_show" class="display-inline"></span>
                            <span id="slug_edit" class="display-inline">
                              <button class="btn btn-warning btn-sm slug_edit_button"> <i class="fas fa-edit"></i> </button>
                              <input type="text" name="slug" value="{{$page_post->slug}}" class="form__control radius-5 blog_slug mt-2" style="display: none">
                              <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                        </span>
                        </label>
                    </div>
                    <div class="form__input__single classic-editor-wrapper @if(!empty($page_post->page_builder_status)) d-none @endif ">
                        <label>{{__('Content')}}</label>
                        <input type="hidden" name="page_content" value="{{$page_post->page_content}}">
                        <div class="summernote" data-content="{{ $page_post->page_content }}"></div>
                    </div>
                    <!-- meta section start -->
                    <x-backend.page-meta-data-edit :sidebarHeading="'Page Meta'" :pagepost="$page_post" />
                    <!-- meta section end -->
                    <div class="form__input__single">
                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Navbar Variant')}}</label>
                            <input type="hidden" class="form-control" id="navbar_variant" value="{{$page_post->navbar_variant}}" name="navbar_variant">
                        </div>
                        <div class="row">
                            @for($i = 1; $i < 3; $i++)
                                <div class="col-lg-12 col-md-12">
                                    <div class="img-select img-select-nav @if($page_post->navbar_variant == $i ) selected @endif">
                                        <div class="img-wrap">
                                            <img src="{{asset('assets/backend/variant-images/navbar/'.$i.'.jpg')}}" data-nav_id="0{{$i}}" alt="">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="form__input__single">
                        <label class="form__input__single__label">{{__('Footer Variant')}}</label>
                        <div class="form__input__single">
                            <input type="hidden" class="form-control" id="footer_variant" value="{{$page_post->footer_variant}}" name="footer_variant">
                        </div>
                        <div class="row">
                            @for($i = 1; $i < 4; $i++)
                                <div class="col-lg-12 col-md-12">
                                    <div class="img-select img-select-footer @if($page_post->footer_variant == $i ) selected @endif">
                                        <div class="img-wrap">
                                            <img src="{{asset('assets/backend/variant-images/footer/'.$i.'.jpg')}}" data-foot_id="0{{$i}}" alt="">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Post Type')}}</h2>
                <div class="form__input__flex">

                    <div class="form__input__single d-flex">
                        <label for="featured" class="form__input__single__label"><strong>{{__('Page Builder Enable/Disable')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox" name="page_builder_status" @if(!empty($page_post->page_builder_status)) checked @endif>
                            <label></label>
                        </div>
                    </div>

                    <div class="form__input__single">
                        <label for="featured" class="form__input__single__label"><strong>{{__('Breadcrumb Show/Hide')}}</strong></label>
                        <div class="switch_box style_7">
                            <input type="checkbox" name="breadcrumb_status" @if(!empty($page_post->breadcrumb_status)) checked @endif>
                            <label></label>
                        </div>
                    </div>

                    <div class="form__input__single">
                        <div class="btn-wrapper page-builder-btn-wrapper @if(empty($page_post->page_builder_status)) d-none @endif ">
                            <a href="{{route('admin.dynamic.page.builder',['type' =>'dynamic-page','id' => $page_post->id])}}" target="_blank" class="btn btn-primary"> <i class="fas fa-external-link-alt"></i> {{__('Open Page Builder')}}</a>
                        </div>
                    </div>

                    <div class="form__input__single col-md-12 layout d-none">
                        <label>{{__('Page Layout')}}</label>
                        <select name="layout" class="form__control radius-5">
                            <option value="normal_layout" @if($page_post->layout == 'normal_layout') selected @endif>{{__('Normal Layout')}}</option>
                            <option value="home_page_layout" @if($page_post->layout == 'home_page_layout')selected  @endif>{{__('Home Page')}}</option>
                            <option value="home_page_layout_two" @if($page_post->layout == 'home_page_layout_two')selected  @endif>{{__('Home Page Layout Two')}}</option>
                            <option value="sidebar_layout" @if($page_post->layout == 'sidebar_layout')selected  @endif>{{__('Sidebar Layout')}}</option>
                        </select>
                    </div>
                    <div class="form__input__single col-md-12 page_class d-none">
                        <label>{{__('Page Class')}}</label>
                        <select name="page_class" class="form__control radius-5">
                            <option value="" >{{__('None')}}</option>
                            <option value="nav-absolute "@if($page_post->page_class == 'nav-absolute') selected @endif>{{__('Custom Class')}}</option>
                        </select>
                        @if($page_post->page_class == 'nav-absolute')
                            <small class="text-danger">{{ __('You must select custom class for this page') }}</small>
                        @else
                            <small class="text-danger">{{ __('You must select none for this page') }}</small>
                        @endif
                    </div>
                    <div class="form__input__single col-md-12 page_class d-none">
                        <label>{{__('Back To Top Icon Color')}}</label>
                        <select name="back_to_top" class="form__control radius-5">
                            <option value="" >{{__('Default Color')}}</option>
                            <option value="style-02" @if($page_post->back_to_top == 'style-02') selected @endif>{{__('Blue')}}</option>
                            <option value="style-03" @if($page_post->back_to_top == 'style-03') selected @endif>{{__('Orange')}}</option>
                        </select>
                    </div>
                    <div class="form__input__single col-md-12">
                        <label>{{__('Visibility')}}</label>
                        <select name="visibility" class="form__control radius-5">
                            <option value="all" @if($page_post->visibility == 'all')selected  @endif>{{__('All')}}</option>
                            <option value="user" @if($page_post->visibility == 'user')selected  @endif>{{__('Only Logged In User')}}</option>
                        </select>
                    </div>
                </div>
                    <label>{{ __('Status') }}</label>
                    <select name="status" class="form__control radius-5">
                        <option @if($page_post->status === 'publish') selected @endif value="publish">{{__('Publish')}}</option>
                        <option @if($page_post->status === 'draft') selected @endif value="draft">{{__('Draft')}}</option>
                    </select>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update') }}</button>
                    </div>
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
        (function ($) {
            "use strict";
            $(document).ready(function () {
                let builder_status = '{{$page_post->page_builder_status == "on"}}';
                if(builder_status){
                    $('.layout').removeClass('d-none');
                    $('.page_class').removeClass('d-none');
                }
                $(document).on('change','input[name="page_builder_status"]',function(){
                    if($(this).is(':checked')){
                        $('.classic-editor-wrapper').addClass('d-none');
                        $('.page-builder-btn-wrapper').removeClass('d-none');
                        $('.layout').removeClass('d-none');
                        $('.page_class').removeClass('d-none');
                    }else {
                        $('.classic-editor-wrapper').removeClass('d-none');
                        $('.page-builder-btn-wrapper').addClass('d-none');
                        $('.layout').addClass('d-none');
                        $('.page_class').addClass('d-none');
                    }
                });
                function makeSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }

                <x-btn.submit/>
                //Permalink Code
                var sl =  $('.blog_slug').val();
                var url = `{{url('/')}}/` + sl;
                var data = $('#slug_show').text(url).css('color', 'blue');

                var form = $('#blog_new_form');


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
                    var url = `{{url('/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.blog_slug').val(slug);
                    $('.blog_slug').hide();
                });


                $(document).on('change','#langchange',function(e){
                    $('#langauge_change_select_get_form').trigger('submit');
                });

                $(".summernote").tooltip("hide");

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
            });
            //For Navbar
            var imgSelect1 = $('.img-select-nav');
            var id = $('#navbar_variant').val();
            imgSelect1.removeClass('selected');
            $('img[data-nav_id="'+id+'"]').parent().parent().addClass('selected');
            $(document).on('click','.img-select-nav img',function (e) {
                e.preventDefault();
                imgSelect1.removeClass('selected');
                $(this).parent().parent().addClass('selected').siblings();
                $('#navbar_variant').val($(this).data('nav_id'));
            })

            //For Footer
            var imgSelect2 = $('.img-select-footer');
            var id = $('#footer_variant').val();
            imgSelect2.removeClass('selected');
            $('img[data-foot_id="'+id+'"]').parent().parent().addClass('selected');
            $(document).on('click','.img-select-footer img',function (e) {
                e.preventDefault();
                imgSelect2.removeClass('selected');
                $(this).parent().parent().addClass('selected').siblings();
                $('#footer_variant').val($(this).data('foot_id'));
            })

        })(jQuery);
    </script>
@endsection
