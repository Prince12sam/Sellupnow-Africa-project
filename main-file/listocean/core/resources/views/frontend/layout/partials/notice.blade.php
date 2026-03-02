@php
    $notice = \App\Models\Backend\Notice::where('status', 1)->where('expire_date', '>', now())->latest()->where('notice_for', 1)->first();
 @endphp
@if($notice)
    <div class="notice_main_section">
        <div class="col-12">
            <div class="alert
         @if($notice->notice_type === 1) alert-danger
         @elseif($notice->notice_type === 2) alert-warning
         @elseif($notice->notice_type === 3) alert-success
         @elseif($notice->notice_type === 4) alert-info
         @endif d-flex  notice_for_frontend m-0 justify-content-center">
                <p> <strong class="text-dark">{{ $notice->title }}</strong>
                    <strong>{{ $notice->description }} </strong>
                </p>
            </div>
        </div>
    </div>
@endif
