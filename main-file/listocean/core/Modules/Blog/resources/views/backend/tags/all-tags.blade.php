@extends('backend.admin-master')
@section('site-title')
    {{__('All Tags')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-12">
            <div class="dashboard__card bg__white padding-20 radius-10 mb-2">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Tags') }}</h4>
                            @can('tag-bulk-delete')
                                <x-bulk-action.bulk-action/>
                            @endcan
                        </div>
                        <div class="dashboard__inner__header__right">
                            <a href="{{ route('admin.blog.tags.store') }}" class="cmnBtn btn_5 btn_bg_blue radius-5"
                               data-bs-toggle="modal"
                               data-bs-target="#add_blog_tag_modal">{{ __('Add New Tag') }}</a>
                            <div class="btn-wrapper mt-3">
                                <input class="form__control radius-5 search_blog_tag_result" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <div class="search_blog_tag_result">
                            @include('blog::backend.tags.search-tags')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Tag add  Modal -->
    <div class="modal fade" id="add_blog_tag_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal_xl__fixed">
            <div class="popup_contents modal-content">
                <div class="popup_contents__header">
                    <div class="popup_contents__header__flex">
                        <div class="popup_contents__header__contents">
                            <h2 class="popup_contents__header__title">{{ __('Add Tag') }}</h2>
                        </div>
                        <div class="popup_contents__header__close" data-bs-dismiss="modal">
                            <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                        </div>
                    </div>
                </div>
                <div class="popup_contents__body">
                    <form action="{{route('admin.blog.tags.store')}}" method="post">
                        @csrf
                        <input type="hidden" name="lang" value="{{$default_lang}}">
                        <div class="form__input__single">
                            <label for="edit_name" class="form__input__single__label">{{__('Name')}}</label>
                            <input type="text" class="form__control radius-5"  name="name" placeholder="{{__('Name')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="edit_name" class="form__input__single__label">{{__('Status')}}</label>
                            <select name="status" class="select2_activation" id="edit_status">
                                <option value="draft">{{__("Draft")}}</option>
                                <option value="publish">{{__("Publish")}}</option>
                            </select>
                        </div>
                        <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                            <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Submit')}}</button>
                            <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Tag Edit Modal -->
    <div class="modal fade" id="category_edit_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal_xl__fixed">
            <div class="popup_contents modal-content">
                <div class="popup_contents__header">
                    <div class="popup_contents__header__flex">
                        <div class="popup_contents__header__contents">
                            <h2 class="popup_contents__header__title">{{ __('Edit Tag') }}</h2>
                        </div>
                        <div class="popup_contents__header__close" data-bs-dismiss="modal">
                            <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                        </div>
                    </div>
                </div>
                <div class="popup_contents__body">
                    <form action="{{route('admin.blog.tags.update')}}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="tag_id">
                        <input type="hidden" name="lang" value="{{$default_lang}}">
                        <div class="form__input__single">
                            <label for="edit_name" class="form__input__single__label">{{__('Name')}}</label>
                            <input type="text" class="form__control radius-5"  name="name"  id="edit_name" placeholder="{{__('Name')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="edit_name" class="form__input__single__label">{{__('Status')}}</label>
                            <select name="status" class="form__control" id="edit_status">
                                <option value="draft">{{__("Draft")}}</option>
                                <option value="publish">{{__("Publish")}}</option>
                            </select>
                        </div>
                        <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                            <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Update')}}</button>
                            <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @can('tag-bulk-delete')
        <x-bulk-action.bulk-action-js :url="route('admin.blog.tags.bulk.action')" />
    @endcan
    <script type="text/javascript">
        (function(){
            "use strict";
            $(document).ready(function(){

                    // change status
                    $(document).on('click','.swal_status_change',function(e){
                        e.preventDefault();
                        Swal.fire({
                            title: '{{__("Are you sure to change status complete? Once you done you can not revert this !!")}}',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: "{{ __('Yes, change it!') }}"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $(this).next().find('.swal_form_submit_btn').trigger('click');
                            }
                        });
                    });

                $(document).on('change','#langchange',function(e){
                    $('#langauge_change_select_get_form').trigger('submit');
                });

                $(document).on('click','.category_edit_btn',function(){
                    var el = $(this);
                    var id = el.data('id');
                    var name = el.data('name');
                    var status = el.data('status');
                    var modal = $('#category_edit_modal');
                    modal.find('#tag_id').val(id);
                    modal.find('#edit_status option[value="'+status+'"]').attr('selected',true);
                    modal.find('#edit_name').val(name);
                });


                // live search
                $(document).on('keyup','.search_blog_tag_result',function(){
                    let string_search = $(this).val();
                    $.ajax({
                        url:"{{ route('admin.blog.tags.search') }}",
                        method:'GET',
                        data:{string_search:string_search},
                        success:function(res){
                            if(res.status=='nothing'){
                                $('.search_blog_tag_result').html('<h5 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h5>');
                            }else{
                                $('.search_blog_tag_result').html(res);
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
                        url:"{{ route('admin.blog.tags.paginate').'?page='}}" + page,
                        data:{string_search:string_search},
                        success:function(res){
                            $('.search_blog_tag_result').html(res);
                        }
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
