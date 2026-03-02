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
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/jquery.ihavecookies.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/all.min.css')}}">
    @if(get_user_lang_direction() == 'rtl')
        <link rel="stylesheet" href="{{asset('assets/common/css/rtl.css')}}">
    @endif

    <link rel="canonical" href="{{canonical_url()}}" />
    @include('frontend.layout.partials.root-style')

    <!-- page css -->
    @yield('style')

    @if(request()->routeIs('homepage'))
        <title>{{get_static_option('site_title')}} - {{get_static_option('site_tag_line')}}</title>
        {!! render_site_meta() !!}
    @elseif( request()->routeIs('frontend.dynamic.page') && $page_type === 'page' )
        {!! render_site_title(optional($page_post)->title ) !!}
        {!! render_site_meta() !!}
    @else
        @yield('page-meta-data')
    @endif
    {!! renderHeadEndHooks() !!}
    @if(!empty(get_static_option('site_third_party_tracking_code')))
        {!! get_static_option('site_third_party_tracking_code') !!}
    @endif
</head>
<body class="new-style">
{!! renderBodyStartHooks() !!}
@include('frontend.layout.partials.notice')
