@if($errors->any())
    <div class="alert alert_margin alert_danger alert_dismissible fade show" role="alert">
        @foreach($errors->all() as $error)
            <li class="">{{$error}}</li>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
