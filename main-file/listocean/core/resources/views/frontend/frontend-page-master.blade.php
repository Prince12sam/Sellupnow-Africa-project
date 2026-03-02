@include('frontend.layout.partials.header')
@include('frontend.layout.partials.navbar')

@if (!empty($page_post) && isset($page_post->breadcrumb_status) && $page_post->breadcrumb_status == 'on')
    <div class="@if(Request::is('about') || Request::is('listings')) container-1920 plr1 @else container-1440 @endif">
      <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap breadcrumb-nav-part">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item66"><a href="#">{{ $page_post->title ?? '' }} @yield('inner-title')</a></li>
            </ol>
       </nav>
    </div>
@endif

@include('frontend.layout.partials.flash-sale-widget-slot', ['slot' => 'before_content'])

@yield('content')

@include('frontend.layout.partials.flash-sale-widget-slot', ['slot' => 'after_content'])

@include('frontend.layout.partials.footer')

@include('frontend.layout.partials.js.basic-markup')
@include('frontend.layout.partials.ai-chat-widget')

@stack('scripts')

