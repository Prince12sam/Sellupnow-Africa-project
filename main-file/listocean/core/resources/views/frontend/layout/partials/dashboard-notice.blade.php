@php
    $notice = \App\Models\Backend\Notice::where('status', 1)->where('expire_date', '>', now())->latest()->where('notice_for', 2)->first();
 @endphp
@if($notice)
    <div class="notice_main_section d-flex m-0 justify-content-center text-center align-items-center">
        <div class="col-xxl-8 col-lg-12 col-md-12 col-sm-12">
            <div class="alert @if($notice->notice_type === 1) alert-danger
                 @elseif($notice->notice_type === 2) alert-warning
                 @elseif($notice->notice_type === 3) alert-success
                 @elseif($notice->notice_type === 4) alert-info
                 @endif">
                <p> <strong class="text-dark">{{ $notice->title }}</strong>
                    <strong>{{ $notice->description }} </strong>
                </p>
            </div>
        </div>
    </div>
@endif
