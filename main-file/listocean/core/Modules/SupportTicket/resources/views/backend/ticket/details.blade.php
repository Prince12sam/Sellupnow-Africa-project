@extends('backend.admin-master')
@section('site-title')
    {{__('All Tickets')}}
@endsection
@section('style')
    <style>
        button.btn.btn-secondary.btn-sm.radius-5.fileUploads_item__file__btn {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 57%;
            height: 11%;
        }

        .supportTicket-messages-body {
            max-height: 380px;
            overflow-y: auto;
        }
        .supportTicket_single__attachment {
            display: flex;
        }
        .text-end.margin-reverse-30 {
            margin-top: -38px;
        }
        .dashboard_promo__single{
            height: max-content;
        }
        .dashboard_promo__single.style_01 {
            border: 1px solid #006769;
           background-color: #00000000;
        }
        .dashboard_promo__single.style_02 {
            border: 1px solid #006769;
            background-color: #00676912;
        }
        .admin_message_show{
            display: flex;
            flex-direction: column;
            align-items: end;
        }
        .user_message_show{
            text-align: start;
        }
    </style>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-8 col-lg-8">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('All Tickets') }}</h4>
                            <div class="mt-3">
                                <strong>#{{ $ticket_details->id }}</strong>
                                @if($ticket_details->status == 'open')
                                    <a href="javascript:void(0)" class="status_btn completed">{{ __('Open') }}</a>
                                @else
                                    <a href="javascript:void(0)" class="status_btn cancelled">{{ __('Closed') }}</a>
                                @endif
                                <a href="javascript:void(0)" class="status_btn completed">{{ $ticket_details->priority }}</a>
                                <h5 class="mt-3">{{ $ticket_details->title }}</h5>
                            </div>
                        </div>
                        <div class="dashboard__inner__header__left">
                            <span class="supportTicket_single__content__time">
                                {{ __('Last update') }}
                                {{ $ticket_details?->get_ticket_latest_message?->updated_at->diffForHumans() ?? $ticket_details->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
                <x-validation.error/>
                <div class="inbox_wrapper__body padding-20">
                    <div class="supportTicket_single__item supportTicket-messages-body">
                        @foreach($ticket_details->message as $message)
                            @if($message->type == 'admin')
                                <div class="supportTicket_single__chat dashboard_promo__single style_01 radius-10 mt-2">
                                    <div class="supportTicket_single__chat__flex admin_message_show">
                                        <div class="dashboard__header__author__thumb">
                                          @php
                                               $profile_img = get_attachment_image_by_id(auth()->user()->image,null,true);
                                          @endphp
                                            <img src="{{$profile_img['img_url']}}" alt="{{ __('admin') }}">
                                        </div>
                                        <span>{{ __('Name:') }}  {{ auth()->user()->name }}</span>
                                        <div class="supportTicket_single__chat__contents">
                                            <div class="supportTicket_single__chat__box">
                                                <p class="supportTicket_single__chat__message text_style_manege">
                                                    {!!  $message->message !!}
                                                </p>
                                                @if($message->attachment)
                                                    <a href="{{ asset('assets/uploads/ticket/chat-messages/'.$message->attachment) }}" download class="supportTicket_single__uploads">
                                                        <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('Download Attachment') }}
                                                    </a>
                                                @endif
                                            </div>
                                            <p class="supportTicket_single__chat__time mt-2 text-end">{{ $message->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="supportTicket_single__chat reply text-end dashboard_promo__single style_02 radius-10 mt-2 padding-20">
                                    <div class="supportTicket_single__chat__flex user_message_show">
                                        <div class="text-start">
                                            <div class="dashboard__header__author__thumb">
                                                @if($ticket_details->user?->image)
                                                    @php
                                                        $profile_img = get_attachment_image_by_id($ticket_details->user?->image,null,true);
                                                    @endphp
                                                    <img src="{{$profile_img['img_url']}}" alt="{{ __('user') }}">
                                                @endif
                                            </div>
                                            {{__("Name:")}} {{ $ticket_details->user?->fullname }}
                                        </div>
                                        <div class="supportTicket_single__chat__contents">
                                            <div class="supportTicket_single__chat__box">
                                                <p class="supportTicket_single__chat__message text_style_manege">
                                                    {!! $message->message !!}
                                                </p>
                                                @if($message->attachment)
                                                    <a href="{{ asset('assets/uploads/ticket/chat-messages/'.$message->attachment) }}"
                                                       download class="supportTicket_single__uploads">
                                                        <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('Download Attachment') }}
                                                    </a>
                                                @endif
                                            </div>
                                            <p class="supportTicket_single__chat__time mt-2">{{ $message->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="supportTicket_single__item">
                        <div class="supportTicket_single__chat__replyForm">
                            <form action="{{ route('admin.ticket.details',$ticket_details->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="supportTicket_single__chat__replyForm__input mt-2">
                                    <textarea name="message" id="message" class="form-message" placeholder="{{ __('Write your reply....') }}"></textarea>
                                </div>

                                <div class="form__input__single mt-3 w-25">
                                  <input type="file" class="inputFileTag form-control radius-5"  name="attachment" id="attachment">
                                </div>

                                <div class="supportTicket-single-chat-replyForm-input mt-2 ">
                                    <label for="email_notify" class="label-title">
                                        <input type="checkbox" name="email_notify" id="email_notify"> {{ __('Email Notify') }}
                                    </label>
                                </div>

                                <x-file.file-support/>

                                <div class="btn_wrapper mt-5">
                                    <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5  send_reply">{{ __('Send Reply') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="text-end margin-reverse-30">
                            @if($ticket_details->status === 'open')
                                <x-status.table.status-change :title="__('Close Ticket')" :url="route('admin.ticket.status',$ticket_details->id)"/>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <div class="dashboard__inner__header mb-3">
                    <div class="dashboard__inner__header__flex">
                        <div class="dashboard__inner__header__left">
                            <h4 class="dashboard__inner__header__title">{{ __('Ticket Details') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="inbox_sidebar__inner">
                   {!! $ticket_details->description ?? __('No Details') !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('supportticket::backend.ticket.ticket-js')
@endsection
