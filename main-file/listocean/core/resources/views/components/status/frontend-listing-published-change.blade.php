<a tabindex="0" class="cmn-btn1 btnIcon swal_status_change d-flex">
    <button class="published_unpublished @if($published === 1) published @endif"> </button>
</a>
<form method='post' action='{{$url}}' class="d-none">
    <input type='hidden' name='_token' value='{{csrf_token()}}'>
    <br>
    <button type="submit" class="swal_form_submit_btn d-none"></button>
</form>
