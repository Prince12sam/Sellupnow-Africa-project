<a tabindex="0" class="cmnBtn btn_5 btn_bg_danger radius-5 swal_delete_button">{{ $title ?? '' }}</a>
<form method='post' action='{{$url}}' class="d-none">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <br>
    <button type="submit" class="cmnBtn btn_5 btn_small swal_form_submit_btn d-none"></button>
</form>
