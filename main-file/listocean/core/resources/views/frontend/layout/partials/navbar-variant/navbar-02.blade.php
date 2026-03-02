<header class="header-style-01">
    <nav class="navbar navbar-area headerBg3 navbar-expand-lg  plr">
        <div class="container-fluid container-two nav-container">
            <div class="responsive-mobile-menu">
                <div class="logo-wrapper">
                    <a href="{{ route('homepage') }}" class="logo">
                        {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                    </a>
                </div>
                @include('frontend.layout.partials.navbar-variant.mobile-responsive-icon')
            </div>
            <div class="NavWrapper">
                <!-- Main Menu -->
                <div class="collapse navbar-collapse" id="bizcoxx_main_menu">
                    <ul class="navbar-nav">
                        @php
                            $menuMarkup = render_frontend_menu($primary_menu);
                            $menuMarkup = preg_replace('/(href="[^"]*\/)membership(")/i', '$1explore$2', $menuMarkup);
                            $menuMarkup = preg_replace('/>\s*Membership\s*</i', '>Trending Videos<', $menuMarkup);
                        @endphp
                        {!! $menuMarkup !!}
                    </ul>
                </div>
            </div>
            <!-- Menu Right -->
            <div class="nav-right-content">
                @include('frontend.layout.partials.navbar-variant.user-menu')
            </div>
        </div>
    </nav>
</header>
