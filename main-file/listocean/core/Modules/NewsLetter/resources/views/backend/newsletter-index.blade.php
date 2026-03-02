@extends('backend.admin-master')
@section('site-title')
    {{__('All Newsletter')}}
@endsection
@section('style')
    <x-datatable.css/>
    <x-summernote.css/>
    <style>
        .select2-container {
            z-index: 0;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-9 col-lg-9">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <x-validation.error/>
                <div class="dashboard__inner__header">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Newsletter Subscriber') }}</h4>
                            @can('newsletter-bulk-delete')
                                <x-bulk-action.bulk-action/>
                            @endcan
                        </div>
                        <div class="dashboard__inner__header__right">
                            <div class="btn-wrapper">
                                <button class="cmnBtn btn_5 btn_bg_blue radius-5" data-bs-toggle="modal" data-bs-target="#new_subscribe_model">{{ __("Add New Subscribe") }}</button>
                            </div>
                            <div class="d-flex text-right w-100 mt-3">
                                <input class="form__control blog_string_search" name="string_search" placeholder="{{ __('Search') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tableStyle_three mt-4">
                    <div class="table_wrapper custom_dataTable">
                        <table class="dataTablesExample">
                            <thead>
                            @can('newsletter-bulk-delete')
                                <th class="no-sort">
                                    <div class="mark-all-checkbox">
                                        <input type="checkbox" class="all-checkbox">
                                    </div>
                                </th>
                            @endcan
                            <th>{{__('ID')}}</th>
                            <th>{{__('Email')}}</th>
                            <th>{{__('Verified')}}</th>
                            <th>{{__('Action')}}</th>
                            </thead>
                            <tbody>
                            @foreach($all_subscriber as $data)
                                <tr>
                                    @can('newsletter-bulk-delete')
                                        <td>
                                            <div class="bulk-checkbox-wrapper">
                                                <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]" value="{{$data->id}}">
                                            </div>
                                        </td>
                                    @endcan
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->email}}
                                        @if($data->verified == 'yes')
                                            <i class="fas fa-check-circle"></i>
                                        @endif
                                    </td>

                                    <td>
                                        @if($data->verified == 'yes')
                                           <span class="text-success">{{ __('Verified') }}</span>
                                        @else
                                            <span class="text-danger">{{ __('unverified') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @can('newsletter-delete')
                                            <x-popup.delete-popup :url="route('admin.newsletter.delete',$data->id)"/>
                                        @endcan
                                        <a class="btn btn-lg btn-primary btn-sm mb-2 me-2 send_mail_modal_btn" href="#"
                                           data-bs-toggle="modal"
                                           data-bs-target="#send_mail_to_subscriber_modal"
                                           data-email="{{$data->email}}">
                                            <i class="ti-email"></i>
                                        </a>
                                        @can('newsletter-newsletter-verify-mail-send')
                                            @if(is_null($data->verified))
                                                <form class="mb-2 me-2" action="{{route('admin.newsletter.verify.mail.send')}}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$data->id}}">
                                                    <button class="btn btn-sm btn-secondary" type="submit">
                                                        {{ __("Send Verify Mail") }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Add New Subscriber')}}</h2>
                <form action="{{route('admin.newsletter.new.add')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="email">{{__('Email')}}</label>
                        <input type="text" class="form-control"  id="email" name="email" placeholder="{{__('Email')}}">
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button id="submit" type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="new_subscribe_model" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content custom__form">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Add New Subscriber')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <form action="{{route('admin.newsletter.new.add')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="email">{{__('Email')}}</label>
                            <input type="text" class="form-control"  id="email" name="email" placeholder="{{__('Email')}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button id="submit" type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="send_mail_to_subscriber_modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content custom__form">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Send Mail To Subscriber')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <form action="{{route('admin.newsletter.single.mail')}}" id="send_mail_to_subscriber_edit_modal_form"  method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="d-none form-group">
                            <label for="email">{{__('Email')}}</label>
                            <input type="email" class="form-control"  id="email" name="email" placeholder="{{__('Email')}}">
                        </div>
                        <div class="form-group">
                            <label for="edit_icon">{{__('Subject')}}</label>
                            <input type="text" class="form-control"  id="subject" name="subject" placeholder="{{__('Subject')}}">
                        </div>
                        <div class="form-group">
                            <label for="message">{{__('Message')}}</label>
                            <input type="hidden" name="message" >
                            <div class="summernote"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button id="submit" type="submit" class="btn btn-primary">{{__('Send Mail')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-datatable.js/>
    <x-summernote.js/>
    <x-bulk-action.bulk-action-js :url="route('admin.newsletter.bulk.action')" />
    <script>
        (function ($){
            "use strict";
            $(document).ready(function () {
                <x-btn.submit />
                $(document).on('click','.send_mail_modal_btn',function(){
                    var el = $(this);
                    var email = el.data('email');
                    var form = $('#send_mail_to_subscriber_edit_modal_form');
                    form.find('#email').val(email);
                });

                $(document).on('click', '.swal_delete_button', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '{{ __('Are you sure?') }}',
                        text: '{{ __('You would not be able to revert this item!') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).next().find('.swal_form_submit_btn').trigger('click');
                        }
                    });
                });

                $('.summernote').summernote({
                    height: 300,
                    codemirror: {
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function(contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
            });
        })(jQuery)
    </script>
@endsection
