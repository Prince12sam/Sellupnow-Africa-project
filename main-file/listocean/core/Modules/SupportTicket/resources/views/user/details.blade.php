@extends('frontend.layout.master')
@section('site_title',__('Support Ticket Details'))
@section('style')
    <style>
        .supportTicket-messages-body {
            max-height: 400px;
            overflow-y: auto;
        }
        /* Container for the support ticket */
        .supportTicket-single {
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 20px;
        }

        /* Styling for the ticket status */
        .supportTicket-single-content-btn a {
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
        }

        /* Styling for the ticket title */
        .supportTicket-single-content-title {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        /* Styling for the ticket messages */
        .supportTicket-single-chat {
            margin-top: 20px;
        }

        /* Styling for the chat messages */
        .supportTicket-single-chat-contents {
            margin-left: 10px;
        }

        /* Styling for the reply form */
        .supportTicket-single-chat-replyForm {
            margin-top: 20px;
        }

        /* Styling for the reply form inputs */
        .supportTicket-single-chat-replyForm-input textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Styling for the file upload input */
        .supportTicket_single__attachment input[type="file"] {
            margin-top: 10px;
        }

        /* Styling for the email notify checkbox */
        .supportTicket-single-chat-replyForm-input label {
            margin-right: 10px;
        }

        /* Styling for the submit button */
        .supportTicket-single-chat-replyForm-submit .btn-profile {
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        /* Styling for the send reply button */
        .send_reply:hover {
            background-color: #0056b3;
        }

        .user-profile-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 53%;
        }

        .dashboard_table__main__priority .priorityBtn {
            display: inline-block;
            padding: 3px 15px;
            border: 1px solid var(--main-color-one);
            color: var(--main-color-one);
            border-radius: 14px;
            font-size: 14px;
            font-weight: 400;
            line-height: 18px;
            background-color: rgba(var(--main-color-one), 0.1);
            white-space: nowrap;
        }
        .dashboard_table__main__priority .priorityBtn.high {
            color: rgb(255, 102, 102);
            background-color: rgb(255, 215, 64, 0.1);
            border: 1px solid #ffde60;
        }
        .dashboard_table__main__priority .priorityBtn.urgent {
            color: rgb(255, 102, 102);
            background-color: rgb(255, 102, 102, 0.1);
            border: 1px solid rgb(204, 0, 0);
        }

        .dashboard_table__main__priority .priorityBtn.completed {
            color: rgb(0, 107, 51);
            background-color: rgba(0, 255, 137, 0.1);
            border: 1px solid rgb(22, 98, 61);
        }

        .tickets-info-bg{
            background-color: #94a3b80d;
            border-radius: 10px;
            padding: 12px;
        }

        .support_ticket_user_section_bg {
            background-color: aliceblue;
            border-radius: 10px;
            padding: 10px;
            margin: 12px;
        }
        .support_ticket_admin_section_bg {
            background-color: #dcf1ff;
            border-radius: 10px;
            padding: 10px;
            margin: 12px;
        }

    </style>
@endsection
@section('content')
    <div class="profile-setting support-tickets setting-page-with-table section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-validation.frontend-error/>
                                <x-frontend.user.responsive-icon/>
                                  <div class="support-chat">
                                        <div class="single-profile-settings">
                                            <div class="single-profile-settings-inner profile-border-top">
                                                <div class="profile-settings-wrapper">
                                                    <div class="single-profile-settings">
                                                        <div class="supportTicket-single radius-10">
                                                            <div class="supportTicket-single-item tickets-info-bg">
                                                                <div class="supportTicket-single-flex">
                                                                    <div class="supportTicket-single-content">
                                                                        <div class="d-flex align-content-end justify-content-between mb-2">
                                                                            <a class="red-global-btn" href="{{route('user.ticket')}}">{{__('All Tickets')}}</a>
                                                                        </div>
                                                                        <div class="supportTicket-single-content-header">
                                                                            <span class="supportTicket-single-content-id">{{ __('Ticket ID:') }}{{ $ticket_details->id }}</span>
                                                                            <div class="supportTicket-single-content-btn">
                                                                                <div class="dashboard_table__main__priority">  {{__('Status:') }}
                                                                                @if($ticket_details->status == 'close')
                                                                                    <a href="javascript:void(0)" class="priorityBtn urgent">{{ __('Closed') }}</a>
                                                                                    @else
                                                                                     <a href="javascript:void(0)" class="priorityBtn completed">{{ __('Open') }}</a>
                                                                               @endif
                                                                                </div>
                                                                                <div class="dashboard_table__main__priority mt-2">
                                                                                    {{__('Priority:') }}
                                                                                    @if($ticket_details->priority=='low') <a href="javascript:void(0)" class="priorityBtn pending">{{ __(ucfirst($ticket_details->priority)) }}</a>@endif
                                                                                    @if($ticket_details->priority=='high') <a href="javascript:void(0)" class="priorityBtn high">{{ __(ucfirst($ticket_details->priority)) }}</a>@endif
                                                                                    @if($ticket_details->priority=='medium') <a href="javascript:void(0)" class="priorityBtn medium">{{ __(ucfirst($ticket_details->priority)) }}</a>@endif
                                                                                    @if($ticket_details->priority=='urgent') <a href="javascript:void(0)" class="priorityBtn urgent">{{ __(ucfirst($ticket_details->priority)) }}</a>@endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <h4 class="supportTicket-single-content-title mt-2">{{__('Title:') }} {{ $ticket_details->title }}</h4>
                                                                    </div>
                                                                    <span class="supportTicket-single-content-time">
                                                                        {{ __('Last update') }}
                                                                        {{ $ticket_details?->get_ticket_latest_message?->updated_at->diffForHumans() ?? $ticket_details->updated_at->diffForHumans() }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <!--all message show -->
                                                            <div class="supportTicket-single-item supportTicket-messages-body mt-3">
                                                                @foreach($ticket_details->message as $message)
                                                                    @if($message->type == 'admin')
                                                                        <div class="supportTicket-single-chat mr-5 mx-4 support_ticket_admin_section_bg">
                                                                            <div class="supportTicket-single-chat-flex">
                                                                                <div class="supportTicket-single-chat-thumb">
                                                                                    @php
                                                                                       $admin_id = \Modules\SupportTicket\app\Models\Ticket::find($message->ticket_id);
                                                                                       $admin_info = \App\Models\Backend\Admin::find($admin_id->admin_id);
                                                                                    @endphp
                                                                                    <div class="user-profile-image">
                                                                                        @if(!empty($admin_info))
                                                                                          {!! render_image_markup_by_attachment_id($admin_info->image) !!}
                                                                                        @endif
                                                                                    </div>
                                                                                    {{__("Name:")}} {{ $admin_info->name }}
                                                                                </div>
                                                                                <div class="supportTicket-single-chat-contents">
                                                                                    <div class="supportTicket-single-chat-box">
                                                                                        <p class="supportTicket-single-chat-message text_style_manege">
                                                                                            {!! $message->message !!}
                                                                                        </p>
                                                                                        @if($message->attachment)
                                                                                            <a href="{{ asset('assets/uploads/ticket/chat-messages/'.$message->attachment) }}" class="single-refundRequest-item-uploads" download>
                                                                                                <i class="fa-solid fa-cloud-arrow-up"></i> <span class="text-warning mt-2 mb-2"> {{ __('Download Attachment') }} </span>
                                                                                            </a>
                                                                                        @endif
                                                                                    </div>
                                                                                    <p class="supportTicket-single-chat-time mt-2">{{ $message->created_at->diffForHumans() }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="supportTicket-single-chat reply support_ticket_user_section_bg text-end">
                                                                            <div class="supportTicket-single-chat-flex">
                                                                                <div class="supportTicket-single-chat-thumb">
                                                                                    <div class="user-profile-image">
                                                                                        {!! render_image_markup_by_attachment_id($ticket_details->user?->image, '')!!}
                                                                                    </div>
                                                                                    <span>{{ __('Name:') }}  {{ auth()->user()->fullname }}</span>
                                                                                </div>
                                                                                <div class="supportTicket-single-chat-contents">
                                                                                    <div class="supportTicket-single-chat-box">
                                                                                        <p class="supportTicket-single-chat-message text_style_manege">
                                                                                            {!! $message->message !!}
                                                                                        </p>
                                                                                        @if($message->attachment)
                                                                                            <a href="{{ asset('assets/uploads/ticket/chat-messages/'.$message->attachment) }}" class="single-refundRequest-item-uploads" download>
                                                                                                <i class="fa-solid fa-cloud-arrow-up"></i> <span class="text-warning mt-2 mb-2"> {{ __('Download Attachment') }} </span>
                                                                                            </a>
                                                                                        @endif
                                                                                    </div>
                                                                                    <p class="supportTicket-single-chat-time mt-2">{{ $message->created_at->diffForHumans() }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                            <!-- replay message -->
                                                            <div class="supportTicket-single-item">
                                                                <div class="supportTicket-single-chat-replyForm">
                                                                    <form action="{{ route('user.ticket.details',$ticket_details->id) }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="supportTicket-single-chat-replyForm-input">
                                                                            <textarea name="message" id="message" class="form-message" placeholder="{{ __('Write your reply....') }}"></textarea>
                                                                        </div>
                                                                        <div class="supportTicket-single-chat-replyForm-submit flex-between align-items-center mt-3">
                                                                            <div>
                                                                                <div class="supportTicket_single__attachment mt-3">
                                                                                    <input type="file" name="attachment" id="attachment" class="file_upload p-3 w-100 form-control"> <br>
                                                                                    <small class="text-secondary">{{ __('Max file size: 200MB. Only JPG, JPEG, PNG, Webp, PDF, CSV, and ZIP files are allowed.') }}</small>
                                                                                </div>
                                                                                <div class="supportTicket-single-chat-replyForm-input text-info mt-2">
                                                                                    <label for="email_notify"><input type="checkbox" name="email_notify" id="email_notify"> {{ __('Email Notify') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="btn-wrapper d-flex flex-wrap gap-2 mt-3">
                                                                                <button type="submit" class="red-global-btn send_reply">{{ __('Send Reply') }}</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                  </div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('supportticket::user.ticket-js')
@endsection
