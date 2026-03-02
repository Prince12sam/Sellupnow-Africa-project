
<!DOCTYPE html>
<html lang="{{get_user_lang()}}" dir="{{get_user_lang_direction()}}">

<head>
    {!! renderHeadStartHooks() !!}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}
    @include('frontend.layout.partials.custom-font')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/plugin.css') }}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/main-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/new-css-add.css') }}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/dynamic-style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/helpers.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/common/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/all.min.css')}}">
    @if( get_user_lang_direction() === 'rtl')
        <link rel="stylesheet" href="{{asset('assets/common/css/rtl.css')}}">
    @endif
    <link rel="canonical" href="{{canonical_url()}}" />
    @include('frontend.layout.partials.root-style')
</head>
<body class="new-style">
@if(!request()->is('admin/*'))
    @include('frontend.layout.partials.navbar')
@endif
    <!--404 page-->
    <div class="page-wraper d-flex justify-content-center align-items-center section-padding2">
        <div class="content-404">
            <div class="image">
                {!! render_image_markup_by_attachment_id(get_static_option('error_image')) !!}
            </div>
            <div class="text text-center">
                <div class="main-title">{{get_static_option('error_404_page_title')}}</div>
                <span class="mt-3 mb-3">{{get_static_option('error_404_page_subtitle')}}</span>
                <p>{{get_static_option('error_404_page_paragraph')}}</p>
                <a href="{{ route('homepage') }}" class="go-back red-btn">{{ get_static_option('error_404_page_button_text') }}</a>
            </div>
        </div>
    </div>
@if(!request()->is('admin/*'))
    @php
        $footer_variant = !is_null(get_footer_style()) ? get_footer_style() : '02';
    @endphp
    @include('frontend.layout.partials.footer-variant.footer-'.$footer_variant)
    @include('frontend.layout.partials.js.basic-markup')
@endif
    <script src="{{ asset('assets/common/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/plugin.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/main.js') }}"></script>
    <script src="{{asset('assets/frontend/js/dynamic-script.js')}}"></script>
    <script src="{{ asset('assets/common/js/toastr.min.js') }}"></script>
    <script src="{{asset('assets/backend/js/select2.min.js')}}"></script>
</body>
</html>
