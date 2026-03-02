@extends('backend.admin-master')
@section('site-title')
    {{__('Add New Child Category')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <x-summernote.css/>
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="header-wrap d-flex justify-content-between mb-3">
                    <div class="left-content">
                        <h4 class="header-title">{{__('Add New Child Category')}}   </h4>
                    </div>
                    <div class="right-content">
                        <a class="cmnBtn btn_5 btn_bg_info radius-5" href="{{route('admin.child.category')}}">{{__('All Child Categories')}}</a>
                    </div>
                </div>
                <x-validation.error/>
                <form action="{{route('admin.child.category.new')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="category" class="form__input__single__label"> {{__('Select Parent Category')}} <span class="text-danger">*</span> </label>
                            <select name="category_id" id="category" class="select2_activation radius-5">
                                <option value="">{{__('Select Category')}}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="subcategory" class="form__input__single__label"> {{__('Select Sub Category')}} <span class="text-danger">*</span> </label>
                            <select  name="sub_category_id" id="subcategory" class="select2_activation form__control radius-5 subcategory">
                                <option value="">{{__('Select Sub Category')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="name" class="form__input__single__label">{{__('Child Category')}}</label>
                            <input type="text" class="form__control radius-5" name="name" id="name" placeholder="{{__('Child Category Name')}}">
                        </div>
                        <div class="form__input__single permalink_label">
                            <label class="form__input__single__label">{{__('Permalink * :')}}
                                <span id="slug_show" class="display-inline"></span>
                                <span id="slug_edit" class="display-inline">
                                  <button class="btn btn-warning btn-sm slug_edit_button"> <i class="fas fa-edit"></i> </button>
                                  <input type="text" name="slug" class="form__control radius-5 child_category_slug mt-2" style="display: none">
                                  <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                 </span>
                            </label>
                        </div>

                        <div class="form__input__single">
                            <label class="form__input__single__label">{{__('Description')}}</label>
                            <input type="hidden" name="description">
                            <div class="summernote"></div>
                        </div>

                        <div class="form__input__single">
                            <label for="image" class="form__input__single__label">{{__('Upload Child Category Image')}}</label>
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
    <x-summernote.js/>
    <x-media.js />
    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {
                //Permalink Code
                $('.permalink_label').hide();
                $(document).on('keyup', '#name', function (e) {
                    var slug = converToSlug($(this).val());
                    var url = "{{url('/child-category/')}}/" + slug;
                    $('.permalink_label').show();
                    var data = $('#slug_show').text(url).css('color', 'blue');
                    $('.child_category_slug').val(slug);

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
                    $('.child_category_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.child_category_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/child-category/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.child_category_slug').val(slug);
                    $('.child_category_slug').hide();
                });

                // select category, sub category and Child Category
                $('#category').on('change',function(){
                    var category_id = $(this).val();
                    $.ajax({
                        method:'post',
                        url:"{{route('admin.select.subcategory')}}",
                        data:{category_id:category_id},
                        success:function(res){
                            if(res.status=='success'){
                                var alloptions = '';
                                var allSubCategory = res.sub_categories;
                                $.each(allSubCategory,function(index,value){
                                    alloptions +="<option value='" + value.id + "'>" + value.name + "</option>";
                                });
                                $(".subcategory").html(alloptions);
                                $('#subcategory').niceSelect('update');
                            }
                        }
                    })
                });

            });
        })(jQuery)
    </script>
@endsection
