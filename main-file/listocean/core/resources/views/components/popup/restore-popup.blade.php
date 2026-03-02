<a tabindex="0" class="cmnBtn btn_5 btn_bg_brown radius-5 swal_delete_button_restore {{ $class ?? '' }}">{{ $title }}</a>
<form method='post' action='{{$url}}' class="d-none">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <br>
    <button type="submit" class="cmnBtn btn_5 btn_small swal_form_submit_btn_restore d-none"></button>
</form>
