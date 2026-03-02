@extends('backend.admin-master')
@section('site-title')
    {{__('All User Listings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-12 col-lg-12">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All User Listings') }}</h4>
                            @can('user-listing-bulk-delete')
                            <x-bulk-action.bulk-action/>
                            @endcan
                       </div>
                        <div class="dashboard__inner__header__right">
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control notice_string_search" name="string_search" id="string_search" placeholder="{{ __('Search') }}">
                            </div>
                       </div>
                   </div>
                 </div>
                <x-validation.error/>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_Table">
                        <div class="search_notice_result">
                            @include('backend.pages.listings.search-listing')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @can('user-listing-bulk-delete')
        <x-bulk-action.bulk-action-js :url="route('admin.listing.bulk.action')"/>
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
                $(document).on('keyup','.notice_string_search',function(){
                    let string_search = $(this).val();
                    $.ajax({
                        url:"{{ route('admin.listings.search') }}",
                        method:'GET',
                        data:{string_search:string_search},
                        success:function(res){
                            if(res.status=='nothing'){
                                $('.search_notice_result').html('<h3 class="text-center text-danger">'+"{{ __('Nothing Found') }}"+'</h3>');
                            }else{
                                $('.search_notice_result').html(res);
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
                        url:"{{ route('admin.listings.paginate').'?page='}}" + page,
                        success:function(res){
                            $('.search_notice_result').html(res);
                        }
                    });
                }

            });
        })(jQuery);
    </script>
@endsection
