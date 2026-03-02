<a tabindex="0" class="cmnBtn btn_5 btn_bg_danger radius-5 btnIcon {{ $class ?? 'swal_status_change'}}">{{ $title ?? '' }} <i class="las la-pen-square"></i> </a>
<form method='post' action='{{$url}}' class="d-none">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <input type='hidden' name='cancel_or_decline_order' value="{{$value ?? ''}}">
    <br>
    <button type="submit" class="cmnBtn btn_5 btn_small swal_form_submit_btn d-none"></button>
</form>
