<a tabindex="0" class="cmn-btn1 cmn-btn-danger btnIcon swal_delete_button">{{ $title ?? '' }} <i class="las la-trash-alt"></i></a>
<form method='post' action='{{$url}}' class="d-none">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <br>
    <button type="submit" class="cmnBtn btn_5 btn_small swal_form_submit_btn d-none"></button>
</form>
