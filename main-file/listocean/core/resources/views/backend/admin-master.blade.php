<body>
<!-- preloader area start -->
@if(!empty(get_static_option('admin_loader_animation')))
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="loader_bars">
                <span></span>
            </div>
        </div>
    </div>
@endif
<!-- preloader area end -->
@include('backend/partials/header')
@include('backend/partials/sidebar')
<div class="dashboard__right">
    @include('backend/partials/top-header')
    <div class="dashboard__body posPadding">
        <div class="dashboard__inner">
            <div class="dashboard__inner__item">
                <div class="dashboard__inner__item__flex">
                    <div class="dashboard__inner__item__left bodyItemPadding">
                        <div class="body-overlay"></div>
                        <div class="dashboard__area">
                            <div class="container-fluid p-0">
                                <div class="dashboard__contents__wrapper">
                                     @yield('content')
                                </div>
                            </div>



                            <footer style="margin-top: 70px">
                                <div class="dashboard__card bg__white padding-20 radius-10">
                                    <div class="footer-area footer-wrap">
                                        {!! render_footer_copyright_text() !!}
                                        <p class="version">V-{{get_static_option('site_script_version')}}</p>
                                    </div>
                                </div>
                            </footer>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('backend/partials/footer')
</body>
</html>

