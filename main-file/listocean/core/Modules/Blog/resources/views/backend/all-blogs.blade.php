@extends('backend.admin-master')
@section('site-title')
    {{__('All Blogs')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Blogs') }}</h4>
                            @can('blog-bulk-delete')
                                <x-bulk-action.bulk-action/>
                            @endcan
                       </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <a href="{{ route('admin.blog.new') }}" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Add Blog') }}</a>
                            </div>
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control blog_string_search" name="string_search" placeholder="{{ __('Search') }}">
                            </div>
                       </div>
                   </div>
                 </div>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <div class="search_blog_result">
                            @include('blog::backend.search-blog')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @can('blog-bulk-delete')
        <x-bulk-action.bulk-action-js :url="route('admin.blog.bulk.action')" />
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

                // live search
                $(document).on('keyup','.blog_string_search',function(){
                    let string_search = $(this).val();
                    $.ajax({
                        url:"{{ route('admin.blog.search') }}",
                        method:'GET',
                        data:{string_search:string_search},
                        success:function(res){
                            if(res.status=='nothing'){
                                $('.search_blog_result').html('<h5 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h5>');
                            }else{
                                $('.search_blog_result').html(res);
                            }
                        }
                    });
                });

                // pagination
                $(document).on('click', '.pagination li a', function(e){
                    e.preventDefault();
                    let page = $(this).attr('href').split('page=')[1];
                    notices(page);
                });
                function notices(page){
                    $.ajax({
                        url:"{{ route('admin.blog.paginate').'?page='}}" + page,
                        success:function(res){
                            $('.search_blog_result').html(res);
                        }
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
