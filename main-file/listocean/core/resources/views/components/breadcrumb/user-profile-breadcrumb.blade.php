
<div class="row mb-24">
    <div class="col-sm-12">
        <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
            <h4 class="breadcrumb-contents-title"> {{ $title }} </h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('homepage') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{$routeName}}">{{ __($innerTitle) }} </a></li>
                @if(isset($subInnerTitle) && $subInnerTitle)
                  <li class="breadcrumb-item"><a href="{{$subRouteName}}">{{ $subInnerTitle ?? '' }} </a></li>
                @endif
                @if(isset($chidInnerTitle) && !empty($chidInnerTitle))
                  <li class="breadcrumb-item"><a href="#">{{ $chidInnerTitle ?? '' }} </a></li>
                @endif
            </ol>
        </nav>
    </div>
</div>
