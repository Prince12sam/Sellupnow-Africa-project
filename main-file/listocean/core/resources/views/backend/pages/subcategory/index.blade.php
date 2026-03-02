@extends('backend.admin-master')
@section('site-title')
    {{__('All Subcategories')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Subcategories') }}</h4>
                            @can('subcategory-bulk-delete')
                            <x-bulk-action.bulk-action/>
                            @endcan
                        </div>
                        <div class="dashboard__inner__header__right">
                            @can('subcategory-add')
                                <div class="btn-wrapper">
                                    <a href="{{ route('admin.subcategory.new') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Add Subcategory') }}</a>
                                </div>
                            @endcan
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control subcategory_string_search" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <div class="search_subcategory_result">
                            @include('backend.pages.subcategory.search-subcategory')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    @can('subcategory-bulk-delete')
        <x-bulk-action.bulk-action-js :url="route('admin.subcategory.bulk.action')"/>
    @endcan
    <script type="text/javascript">
        (function(){
            "use strict";
            $(document).ready(function(){

                    $(document).on('click','.swal_status_change',function(e){
                        e.preventDefault();
                        Swal.fire({
                            title: '{{__("Are you sure to change status?")}}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, change it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $(this).next().find('.swal_form_submit_btn').trigger('click');
                            }
                        });
                    });

                $(document).on('click', '.subcategory_edit_btn', function () {
                    let el = $(this);
                    let id = el.data('id');
                    let name = el.data('name');
                    let slug_value_show_permalink = el.data('slug');
                    let category_id = el.data('categoryid');
                    let form = $('#subcategory_edit_modal');

                    form.find('#up_id').val(id);
                    form.find('#up_name').val(name);
                    form.find('#up_slug').val(slug_value_show_permalink);
                    form.find('#up_category_id').val(category_id);

                    let url = "{{url('/subcategory/')}}/" + slug_value_show_permalink;
                    let data = $('#slug_show').text(url).css('color', 'blue');

                    let image = el.data('image');
                    let imageid = el.data('imageid');

                    if (imageid != '') {
                        form.find('.media-upload-btn-wrapper .img-wrap').html('<div class="attachment-preview"><div class="thumbnail"><div class="centered"><img class="avatar user-thumb" src="' + image + '" > </div></div></div>');
                        form.find('.media-upload-btn-wrapper input').val(imageid);
                        form.find('.media-upload-btn-wrapper .media_upload_form_btn').text('Change Image');
                    }
                });

                //Permalink Code
                function converToSlug(slug){
                    let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    finalSlug = slug.replace(/  +/g, ' ');
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }
                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.subcategory_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });
                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    let update_input = $('.subcategory_slug').val();
                    let slug = converToSlug(update_input);
                    let url = `{{url('/subcategory/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.subcategory_slug').hide();
                })

                // live search
                $(document).on('keyup','.subcategory_string_search',function(){
                    let string_search = $(this).val();
                    $.ajax({
                        url:"{{ route('admin.subcategory.search') }}",
                        method:'GET',
                        data:{string_search:string_search},
                        success:function(res){
                            if(res.status=='nothing'){
                                $('.search_subcategory_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                            }else{
                                $('.search_subcategory_result').html(res);
                            }
                        }
                    });
                });
                // pagination
                $(document).on('click', '.pagination li a', function(e){
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    let string_search = $('#string_search').val();
                    notices(page,string_search);
                });
                function notices(page,string_search){
                    $.ajax({
                        url:"{{ route('admin.subcategory.paginate').'?page='}}" + page,
                        data:{string_search:string_search},
                        success:function(res){
                            $('.search_subcategory_result').html(res);
                        }
                    });
                }

            });
        })(jQuery);
    </script>
@endsection
