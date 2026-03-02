<a tabindex="0" class="cmnBtn btn_5 btn_bg_warning btnIcon radius-5 swal_status_change">
    <i class="las la-pen" style="font-size: 12px!important;">{{ __('All Approve') }}</i>
</a>
<form method='post' action='{{$url}}' class="d-none">
<input type='hidden' name='_token' value='{{csrf_token()}}'>
<br>
<button type="submit" class="swal_form_submit_btn d-none"></button>
 </form>
