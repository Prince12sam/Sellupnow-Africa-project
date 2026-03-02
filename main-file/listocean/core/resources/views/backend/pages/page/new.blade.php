@extends('backend.admin-master')
@section('site-title')
    {{__('Add New Page')}}
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
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Add New Page') }}</h4>
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <a href="{{ route('admin.page') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('All Pages')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.page.new')}}" method="POST" enctype="multipart/form-data" id="blog_new_form">
                    @csrf
                    <input type="hidden" name="lang" value="{{$default_lang}}">
                    <div class="form__input__flex mt-3">
                        <div class="form__input__single">
                            <label for="title" class="form__input__single__label">{{__('Title')}} <span class="text-danger">*</span> </label>
                            <input type="text" class="form__control" name="title" id="title" placeholder="{{ __('Title') }}">
                        </div>
                        <div class="form__input__single permalink_label">
                            <label class="text-dark">{{__('Permalink * :')}}
                                <span id="slug_show" class="display-inline"></span>
                                <span id="slug_edit" class="d-inline">
                                  <button class="btn btn-warning btn-sm slug_edit_button"> <i class="fas fa-edit"></i> </button>
                                  <input type="text" name="slug" class="form__control radius-5 blog_slug mt-2" style="display: none">
                                  <button class="cmnBtn btn_5 btn_bg_info radius-5 slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                </span>
                            </label>
                        </div>
                        <div class="form__input__single classic-editor-wrapper">
                            <label class="form__input__single__label">{{__('Content')}}</label>
                            <input type="hidden" name="page_content">
                            <div class="summernote"></div>
                        </div>
                    </div>

                    <x-backend.page-meta-data-create :sidebarHeading="'Page Meta'"/>

                    <div class="form__input__single">
                        <label for="title" class="form__input__single__label">{{__('Navbar Variant')}}</label>
                        <div class="form__input__single">
                            <input type="hidden" class="form-control" id="navbar_variant" value="01" name="navbar_variant">
                        </div>
                        <div class="row">
                            @for($i = 1; $i < 3; $i++)
                                <div class="col-lg-12 col-md-12">
                                    <div class="img-select selected">
                                        <div class="img-wrap">
                                            <img src="{{asset('assets/backend/variant-images/navbar/'.$i.'.jpg')}}" data-home_id="0{{$i}}" alt="">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="form__input__single">
                        <label for="title" class="form__input__single__label">{{__('Footer Variant')}}</label>
                        <div class="form__input__single">
                            <input type="hidden" class="form-control" id="footer_variant" value="01" name="footer_variant">
                        </div>
                        <div class="row">
                            @for($i = 1; $i < 4; $i++)
                                <div class="col-lg-12 col-md-12">
                                    <div class="img-select selected">
                                        <div class="img-wrap">
                                            <img src="{{asset('assets/backend/variant-images/footer/'.$i.'.jpg')}}" data-home_id="0{{$i}}" alt="">
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
                <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="featured" class="form__input__single__label"><strong>{{__('Page Builder Enable/Disable')}}</strong></label>
                            <div class="switch_box style_7">
                                <input type="checkbox" name="page_builder_status">
                                <label></label>
                            </div>
                        </div>
                        <div class="form__input__single">
                            <label for="featured" class="form__input__single__label"><strong>{{__('Breadcrumb Show/Hide')}}</strong></label>
                            <div class="switch_box style_7">
                                <input type="checkbox" name="breadcrumb_status">
                                <label></label>
                            </div>
                        </div>

                        <div class="form__input__single col-md-12">
                            <div class="btn-wrapper page-builder-btn-wrapper d-none">
                                <button type="button" class="btn btn-primary">{{__('Open Page Builder')}}</button>
                                <div class="d-flex">
                                    <small class="text-info mt-3">{{__('Page builder option is available in page edit only')}}</small>
                                </div>
                            </div>
                        </div>


                        <div class="form__input__single col-md-12 layout d-none">
                            <label class="form__input__single__label">{{__('Page Layout')}}</label>
                            <select name="layout" class="form__control">
                                <option value="normal_layout" >{{__('Normal Layout')}}</option>
                                <option value="home_page_layout">{{__('Home Page')}}</option>
                                <option value="home_page_layout_two">{{__('Home Page Layout Two')}}</option>
                                <option value="sidebar_layout">{{__('Sidebar Layout')}}</option>
                            </select>
                        </div>
                        <div class="form__input__single col-md-12 page_class d-none">
                            <label class="form__input__single__label">{{__('Page Class')}}</label>
                            <select name="page_class" class="form__control">
                                <option value="" >{{__('None')}}</option>
                                <option value="nav-absolute">{{__('Custom Class')}}</option>
                            </select>
                            <small class="text-info">{{ __('Adjust page frontend view selecting by none or custom class') }}</small>
                        </div>
                        <div class="form__input__single col-md-12 page_class d-none">
                            <label class="form__input__single__label">{{__('Back To Top Icon Color')}}</label>
                            <select name="back_to_top" class="form__control">
                                <option value="" >{{__('Default Color')}}</option>
                                <option value="style-02" >{{__('Blue')}}</option>
                                <option value="style-03" >{{__('Orange')}}</option>
                            </select>
                        </div>
                        <div class="form__input__single col-md-12">
                            <label class="form__input__single__label">{{__('Visibility')}}</label>
                            <select name="visibility" class="form__control">
                                <option value="all">{{__('All')}}</option>
                                <option value="user">{{__('Only Logged In User')}}</option>
                            </select>
                        </div>
                    </div>
                    <x-fields.status :name="'status'" :title="__('Status')"/>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Submit') }}</button>
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
                <x-btn.submit/>

                function makeSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }

                //Permalink Code
                $('.permalink_label').hide();

                $(document).on('keyup', '#title', function (e) {
                    var slug = makeSlug($(this).val());
                    var url = `{{url('/')}}/` + slug;
                    $('.permalink_label').show();
                    var data = $('#slug_show').text(url).css('color', 'blue');
                    $('.blog_slug').val(slug);

                });

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
                if ($('.summernote').length > 1) {
                    $('.summernote').each(function (index, value) {
                        $(this).summernote('code', $(this).data('content'));
                    });
                }
            });

            //For Navbar
            var imgSelect = $('.img-select');
            var id = $('#navbar_variant').val();
            imgSelect.removeClass('selected');
            $('img[data-home_id="'+id+'"]').parent().parent().addClass('selected');
            $(document).on('click','.img-select img',function (e) {
                e.preventDefault();
                imgSelect.removeClass('selected');
                $(this).parent().parent().addClass('selected').siblings();
                $('#navbar_variant').val($(this).data('home_id'));
            })

            //For Footer
            var imgSelect = $('.img-select');
            var id = $('#footer_variant').val();
            imgSelect.removeClass('selected');
            $('img[data-home_id="'+id+'"]').parent().parent().addClass('selected');
            $(document).on('click','.img-select img',function (e) {
                e.preventDefault();
                imgSelect.removeClass('selected');
                $(this).parent().parent().addClass('selected').siblings();
                $('#footer_variant').val($(this).data('home_id'));
            })

        })(jQuery);
    </script>
@endsection
