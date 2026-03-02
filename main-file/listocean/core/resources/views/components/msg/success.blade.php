@if(session()->has('msg'))
    <div class="mt-3 alert alert_margin alert_{{session('type')}} alert_dismissible fade show" role="alert">
        {!! purify_html(session('msg')) !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
