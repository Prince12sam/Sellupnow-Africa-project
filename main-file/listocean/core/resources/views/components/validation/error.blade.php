@if(session()->has('msg'))
    <div class="mt-3 alert alert_margin alert_{{session('type')}} alert_dismissible fade show mt-3 mb-2" role="alert">
        {!! purify_html(session('msg')) !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert_margin alert_danger alert_dismissible fade show mt-3 mb-2" role="alert">
        <ul style="list-style:none;">
        @foreach($errors->all() as $error)
            <li class="">{{$error}}</li>
        @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
