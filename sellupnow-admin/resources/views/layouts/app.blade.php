@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
    $generaleSetting = $generaleSetting ?? generaleSetting('setting');
    $businessModel = $businessModel ?? ($generaleSetting?->business_based_on ?? 'single');
    if (!isset($languages)) {
        try {
            $languages = \App\Models\Language::query()->get();
        } catch (\Throwable $th) {
            $languages = collect();
        }
    }
    $seederRun = $seederRun ?? false;
    $storageLink = $storageLink ?? false;
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $directory }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- App favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ $generaleSetting?->favicon ?? asset('assets/favicon.png') }}" />

    <!-- App title -->
    <title>{{ $generaleSetting?->title ?? config('app.name', 'Laravel') }}</title>

    <!-- Meta -->
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">


    <!-- Font-Awesome--Min-Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">

    <!-- sweetalert css-->
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">

    <!-- Bootstrap--Min-Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <!-- quill css -->
    <link rel="stylesheet" href="{{ asset('assets/css/quill.snow.css') }}">

    <!-- Custom--Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!--Responsive--Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

    <!-- Toastr Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Cropper Css Link --}}
    <link href="{{ asset('assets/css/cropper.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}" type="text/css">
{{-- "{{ $generaleSetting?->primary_color ?? '#EE456B' }}" "{{ $generaleSetting?->secondary_color ?? '#FEE5E8' }}"; --}}
    <style>
            /* WebKit-based browsers (Chrome, Safari, Edge) */
            ::-webkit-scrollbar {
                width: 8px;
                height: 12px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #555;
            }
    </style>


    @stack('css')

    <style>
        .app-header {
            height: 48px !important;
        }

        .app-header .app-header-content {
            height: 48px !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .app-header-logo,
        .app-header-menu,
        .app-header-mobile-menu {
            height: 48px !important;
        }

        .fixed-header .app-main {
            padding-top: 48px !important;
        }

        .app-header .app-header-content .header-text .header-title {
            font-size: 15px !important;
            line-height: 20px !important;
        }

        .app-header .app-header-content .header-text .header-subtitle {
            font-size: 10px !important;
            line-height: 14px !important;
        }

        .has-passport.fixed-header .app-header {
            top: 0 !important;
        }

        .has-passport.fixed-sidebar .app-main .app-main-outer {
            padding-top: 0 !important;
        }

        .has-passport.fixed-sidebar .app-sidebar {
            top: 48px;
            height: 100svh;
        }

        .profilePic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .cropper-container {
            width: 1100px !important;
            height: 600px !important;
        }
        .mainThumbnail{
            width: auto;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px dashed rgba(128,128,128,.30);
            display: block;
            overflow: hidden;
            cursor: pointer;
        }
        .mainThumbnail img{
            object-fit: cover;
            width: 100%;
            height: 100%;
        }
        .image-container{
            display: inline-block;
        }
        #generateAi .icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            font-weight: 900;
            background-color: white;
            mask: url("{{ asset('assets/icons-admin/intelligence_new.svg') }}") no-repeat center;
            mask-size: contain;
        }

        #generateAi {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
    </style>
</head>

<body class="loading">
    <div class="app-container body-tabs-shadow fixed-sidebar fixed-header" id="appContent">
        <div class="app-header">
            <div class="app-header-logo"></div>
            <div class="app-header-mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header-menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header-content">
                <!-- Header-left-Section -->
                <div class="app-header-left">
                    <div class="header-pane ">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Header-Text-Section -->
                    <div class="header-text">
                        <h4 class="mb-0 header-title">
                            @yield('header-title') <a href="/" target="_blank" class="btn btn-primary btn-sm ms-3">Website</a>
                        </h4>
                        <p class="mb-0 header-subtitle">
                            @yield('header-subtitle')
                        </p>
                    </div>
                </div>
                <!-- End-Header-Left-section -->

                <!-- Header-Right-Section -->
                <div class="app-header-right">

                    @auth
                    @endauth

                    <!-- search bar -->
                    <div class="searchingBox">
                        <div class="d-flex position-relative">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search Menu"
                                autocomplete="off">
                            <span class="searchIcon"><i class="fa fa-search"></i></span>
                        </div>
                        <ul class="search-list" style="display: none"></ul>
                    </div>
                    <div class="badgeButtonBox me-1 me-md-3">
                        <div id="searchBtn" class="notificationIcon">
                            <button type="button" class="emailBadge">
                                <img src="{{ asset('assets/icons-admin/search.svg') }}" alt="search"
                                    loading="lazy" />
                            </button>
                        </div>
                    </div>

                    <!-- Theme dark and light -->
                    <div class="badgeButtonBox me-1 me-md-3">
                        <div class="notificationIcon" onclick="switchTheme()">
                            <button type="button" class="emailBadge">
                                <img class="lightModeIcon" src="{{ asset('assets/icons-admin/moon.svg') }}"
                                    alt="bell" loading="lazy" />
                                <img class="darkModeIcon" src="{{ asset('assets/icons-admin/sun.svg') }}"
                                    alt="bell" loading="lazy" />
                            </button>
                        </div>
                    </div>

                    @hasPermission('admin.dashboard.notification')
                        <!-- Notification Section -->
                        <div class="badgeButtonBox me-1 me-md-3">
                            <div class="notificationIcon">
                                <button type="button" class="emailBadge dropdown-toggle position-relative"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ asset('assets/icons-admin/notification.svg') }}" alt="bell" loading="lazy" />
                                    <span class="position-absolute notificationCount" id="totalNotify"></span>
                                </button>
                                <div class="dropdown-menu p-0 emailNotificationSection">
                                    <div class="dropdown-item emailNotification">
                                        <div class="emailHeader">
                                            <h6 class="massTitle">
                                                {{ __('Notifications') }}
                                            </h6>
                                            <a href="{{ route('admin.notification.readAll') }}"
                                                class="text-dark">
                                                {{ __('Marks all as read') }}
                                            </a>
                                        </div>
                                        <div class="message-section" id="notifications">

                                        </div>
                                        <div class="emailFooter">
                                            <a href="{{ route('admin.notification.show') }}"
                                                class="massPera text-dark">
                                                {{ __('View All Notifications') }}
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endhasPermission

                    <!-- Language Dropdown -->
                    <div class="user-profile-box dropdown mx-3">
                        <div class="nav-profile-box dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                                $selectedLang = null;
                                foreach ($languages as $lang) {
                                    if ($lang->name == app()->getLocale()) {
                                        $selectedLang = $lang;
                                        break;
                                    }
                                }
                            @endphp
                            <div class="lang">
                                <img src="{{ asset('assets/icons-admin/Language.svg') }}" alt="icon"
                                    loading="lazy" />
                                <span>{{ ucfirst($selectedLang ? $selectedLang->title : __('English')) }}</span>
                                <i class="fa-solid fa-angle-down dropIcon"></i>
                            </div>
                        </div>

                        <div class="dropdown-menu profile-item">
                            @foreach ($languages as $lang)
                                @if (\Illuminate\Support\Facades\Route::has('change.language'))
                                    <a href="{{ route('change.language', 'language=' . $lang->name) }}"
                                        class="dropdown-item {{ $lang->name == app()->getLocale() ? 'language-active' : '' }}">
                                        <i class="fa fa-language mr-3"></i>
                                        {{ __($lang->title) }}
                                    </a>
                                @else
                                    <span class="dropdown-item {{ $lang->name == app()->getLocale() ? 'language-active' : '' }}">
                                        <i class="fa fa-language mr-3"></i>
                                        {{ __($lang->title) }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="user-profile-box user-profile dropdown">
                        <div class="nav-profile-box dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-info">
                                <span class="name">{{ ucfirst(Str::limit(auth()->user()?->name, 20)) }}</span>
                                <span class="role">{{ ucfirst(auth()->user()?->getRoleNames()?->first()) }}</span>
                            </div>
                            <div class="profile-image">
                                <img class="profilePic"
                                    src="{{ auth()->user()?->thumbnail ?? asset('assets/icons/user.svg') }}"
                                    alt="profile" loading="lazy" />
                            </div>
                        </div>

                        <div class="dropdown-menu profile-item ">
                            @hasPermission('admin.profile.index')
                                <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                                    <img src="{{ asset('assets/icons-admin/user-circle.svg') }}" alt="user"
                                        loading="lazy" />
                                    {{ __('Profile') }}
                                </a>
                            @endhasPermission

                            @hasPermission('admin.generale-setting.index')
                                <a href="{{ route('admin.generale-setting.index') }}" class="dropdown-item">
                                    <img src="{{ asset('assets/icons-admin/settings.svg') }}" alt="setting"
                                        loading="lazy" />
                                    {{ __('Settings') }}
                                </a>
                            @endhasPermission

                            @hasPermission('admin.profile.change-password')
                                <a href="{{ route('admin.profile.change-password') }}" class="dropdown-item">
                                    <img src="{{ asset('assets/icons-admin/role-permission.svg') }}" alt="key"
                                        loading="lazy" />
                                    {{ __('Change Password') }}
                                </a>
                            @endhasPermission

                            <button class="dropdown-item cursor-pointer logout text-danger">
                                <img src="{{ asset('assets/icons-admin/log-out.svg') }}" alt="key"
                                    loading="lazy" />
                                {{ __('Logout') }}
                            </button>
                        </div>
                    </div>
                </div>
                <!-- End-Header-Right-Section -->

            </div>
        </div>
        <div class="app-main">

            @include('layouts.sidebar')

            <!-- ****Body-Section***** -->

            <div class="app-main-outer">
                <!-- ****End-Body-Section**** -->
                <div class="app-main-inner">
                    <div class="container-fluid">
                        <!-- seeder run -->
                        @if ($seederRun)
                            <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                role="alert" style="padding: 10px">
                                <strong><i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                        data-placement="bottom"
                                        title='If you do not run this seeder, you will not be able to use the system.'></i>
                                    Seeder dose not run.</strong> Please run <code class="text-danger">php artisan
                                    migrate:fresh
                                    --seed</code> or <a href="{{ route('seeder.run.index') }}"
                                    class="btn btn-sm common-btn"> Click
                                    here</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                                    id="closeAlert"></button>
                            </div>
                        @endif

                        <!-- storage link -->
                        @if ($storageLink)
                            <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                role="alert" style="padding: 10px">
                                <strong><i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                        data-placement="bottom"
                                        title='If you can not install storage link, then image not found.'></i>
                                    Storage link dose not exist or image not found then</strong> please run <code
                                    class="text-danger">php artisan
                                    storage:link</code> or <a href="{{ route('storage.install.index') }}"
                                    class="btn btn-sm common-btn">
                                    Click here</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                                    id="closeAlert"></button>
                            </div>
                        @endif

                        <!-- subscription warning -->
                        @if (false)
                            @if (!request()->current_subscription)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        You currently do not have an active subscription. Please purchase a plan to
                                        start selling.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Choose Plan
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif

                            @if (request()->current_subscription &&
                                    !request()->subscription_expired &&
                                    request()->current_subscription->remaining_sales === 0)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        You have reached the maximum number of sales for your current subscription.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Renew Subscription
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif

                            @if (request()->subscription_about_to_expire)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        Your subscription will expire in {{ request()->subscription_time_left }}.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Renew Subscription
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif

                            @if (request()->subscription_expired)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        Your subscription has expired.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Renew Subscription
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif
                        @endif

                        <!-- Main Content -->
                        @yield('content')
                        <!-- End Main Content -->

                    </div>
                </div>
                <!-- Footer-Section -->

                @if (!$generaleSetting || $generaleSetting?->show_footer)
                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            <div class="app-footer-inner">
                                <div>
                                    © {{ date('Y') }} {{ $generaleSetting?->footer_text }}
                                </div>
                                <div class="d-none d-sm-block">
                                    <i class="bi bi-telephone"></i>
                                    <span>
                                        {{ $generaleSetting?->footer_phone ?? '0123456789' }}
                                    </span>
                                </div>
                                <div class="d-none d-sm-block">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>
                                        {{ $generaleSetting?->email ?? 'example@gmail.com' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            @include('layouts.cropModal')
            @include('layouts.galleryModal')
        </div>
    </div>

    <!-- Logout Form -->
    <form action="{{ route('admin.logout') }}" method="POST" id="logoutForm">
        @csrf
    </form>

    <script src="{{ asset('assets/scripts/jquery-3.6.3.min.js') }}"></script>
    <!-- Bootstrap-Min-Bundil-Link -->
    <script src="{{ asset('assets/scripts/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/script.js') }}"></script>
    <!-- Main-Script-Js-Link -->
    <script src="{{ asset('assets/scripts/main.js') }}"></script>

    <!-- Full-Screen-Js-Link -->
    <script src="{{ asset('assets/scripts/full-screen.js') }}"></script>

    <!--select2 -->
    <script src="{{ asset('assets/scripts/select2.min.js') }}"></script>

    <!-- sweetalert js-->
    <script src="{{ asset('assets/scripts/sweetalert2.min.js') }}"></script>

    <!-- quill  editor-->
    <script src="{{ asset('assets/scripts/quill.js') }}"></script>

    <script src="{{ asset('assets/scripts/jQuery.print.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/toastr.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/jquery.timepicker.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.js') }}"></script>

    {{-- Cropper-Js-Link --}}
    <script src="{{ asset('assets/scripts/cropper.min.js') }}"></script>

    <!-- Pusher-Js-Link -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var themeColor = "{{ $generaleSetting?->primary_color ?? '#EE456B' }}";
            var themeHoverColor = "{{ $generaleSetting?->secondary_color ?? '#FEE5E8' }}";
            document.documentElement.style.setProperty('--theme-color', themeColor);
            document.documentElement.style.setProperty('--theme-hover-bg', themeHoverColor);

            // manage menu active svg color
            var svgImages = document.querySelectorAll(".menu.active .menu-icon");
            changeSvgImageColor(svgImages, themeColor);

            var selectedSvgImage;
            var svgColor;
            selectedSvgImage = document.querySelectorAll(".btn-outline-primary img");
            if (selectedSvgImage.length) {
                svgColor = themeColor;
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            selectedSvgImage = document.querySelectorAll(".btn-primary img");
            if (selectedSvgImage.length) {
                svgColor = "#ffffff";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            selectedSvgImage = document.querySelectorAll(".btn-outline-info img");
            if (selectedSvgImage.length) {
                svgColor = "#0ea5e9";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            selectedSvgImage = document.querySelectorAll(".btn-outline-warning img");
            if (selectedSvgImage.length) {
                svgColor = "#f97316";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            selectedSvgImage = document.querySelectorAll(".btn-danger img");
            if (selectedSvgImage.length) {
                svgColor = "#ffffff";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            selectedSvgImage = document.querySelectorAll(".btn-outline-danger img");
            if (selectedSvgImage.length) {
                svgColor = "#ef4444";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            selectedSvgImage = document.querySelectorAll(".btn-outline-success img");
            if (selectedSvgImage.length) {
                svgColor = "#059669";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            selectedSvgImage = document.querySelectorAll(".svg-bg img");
            if (selectedSvgImage.length) {
                svgColor = themeColor;
                changeSvgImageColor(selectedSvgImage, svgColor);
            }
        });

        function changeSvgImageColor(svgImages, svgColor) {
            svgImages.forEach(function(svgImage) {
                var svgPath = svgImage.getAttribute("src");
                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var svgContent = xhr.responseText;

                        svgContent = svgContent.replace(/stroke=".*?"/g, `stroke="${svgColor}"`);
                        svgContent = svgContent.replace(/fill=".*?"/g, `fill="${svgColor}"`);
                        svgImage.src = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(svgContent);
                    }
                };
                xhr.open("GET", svgPath, true);
                xhr.send();
            });
        }


        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".menu-icon").forEach(function(img) {
                img.dataset.originalSrc = img.getAttribute("src");
            });
        });

        $(document).on("mouseenter", ".menu", function() {
            var svgImages = this.querySelectorAll(".menu-icon");
            var themeColor = "{{ $generaleSetting?->primary_color ?? '#EE456B' }}";
            changeSvgImageColor(svgImages, themeColor);
        });

        $(document).on("mouseleave", ".vertical-nav-menu li .menu", function() {
            var imgs = this.querySelectorAll(".menu-icon");
            var themeColor = "{{ $generaleSetting?->primary_color ?? '#EE456B' }}";
            if (this.getAttribute("aria-expanded") === "true") {
                changeSvgImageColor(imgs, themeColor, "#25314C");
            } else {
                changeSvgImageColor(imgs, "#25314C", themeColor);
            }

            var svgImages = document.querySelectorAll(".menu.active .menu-icon");
            changeSvgImageColor(svgImages, themeColor);
        });
        $(document).on("click", ".menu", function() {
            var svgImages = this.querySelectorAll(".menu-icon");
            if (this.getAttribute("aria-expanded") === "true") {
                var themeColor = "{{ $generaleSetting?->primary_color ?? '#EE456B' }}";
                changeSvgImageColor(svgImages, themeColor);
            } else {
                var defaultColor = "#5f677b";
                changeSvgImageColor(svgImages, defaultColor);
            }
        });

        // Fetch Admin Notifications
        const fetchAdminNotifications = () => {
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.dashboard.notification') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#totalNotify').text(response.data.total)
                    $('#notifications').empty()
                    $.each(response.data.notifications, function(key, value) {
                        var id = value.id;
                        var link = "{{ route('admin.notification.read', ':id') }}";
                        link = link.replace(':id', id);
                        $('#notifications').append(
                            `<a href="${link}" class="item d-flex gap-2 align-items-center">
                            <div class="iconBox ${value.type == 'danger' ? 'cardIcon' : 'pdfIcon'}">
                                <i class="bi ${value.icon}"></i>
                            </div>
                            <div class="notification w-100 ${!value.is_read ? 'unread' : ''}">
                                <div class="userName">
                                    <p class="massTitle">${value.title} </p>
                                    <span class="time">${value.time}</span>
                                </div>
                                <div>
                                    <p class="description">${value.content}</p>
                                </div>
                            </div>
                        </a>`
                        );
                    })
                },
                error: function(e) {
                        $('#notifications').empty()
                        $('#totalNotify').text(0)
                        if (e.status == 401 || e.status == 403) {
                            $("#notifications").text("No notifications available");
                        } else {
                            $("#notifications").text("Unable to load notifications right now.");
                        }
                }
            });
        }

    </script>

    <!-- Pusher Scripts -->
    <script>
        const pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
        const pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
        let pusher = null;
        let channel = null;

        if (pusherKey) {
            pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
            });
            channel = pusher.subscribe('notification-channel');
        }
    </script>

    <!-- Show Notifications Using Pusher JS -->

    @hasPermission('admin.dashboard.notification')
        <script>
            if (channel) {
                channel.bind('admin-product-request', function(data) {
                    var message = data.message;
                    if (message.startsWith('"') && message.endsWith('"')) {
                        message = message.slice(1, -1);
                    }
                    toastr.success(message)
                    fetchAdminNotifications()
                });

                channel.bind('support-ticket-event', function(data) {
                    var message = data.message;
                    if (message.startsWith('"') && message.endsWith('"')) {
                        message = message.slice(1, -1);
                    }
                    toastr.success(message)
                    fetchAdminNotifications()
                });
            }

            fetchAdminNotifications()
        </script>
    @endhasPermission

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>

    @stack('scripts')

    @if (session('success'))
        <script>
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            })
        </script>
    @endif

    @if (session('demoMode'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "{{ session('demoMode') }}",
            });
        </script>
    @endif

    @if (session('alertError'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                html: `{{ session('alertError')['message'] }} <br><br> {{ isset(session('alertError')['message2']) ? session('alertError')['message2'] : '' }}`,
            });
        </script>
    @endif

    <Script>
        document.addEventListener("DOMContentLoaded", function() {
            var root = document.documentElement;

            // Get the value of --theme-color
            var themeColor = getComputedStyle(root).getPropertyValue("--theme-color");

            $(".deleteConfirm").on("click", function(e) {
                e.preventDefault();
                const url = $(this).attr("href");
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: '{{ __('You will not be able to revert this!') }}',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: themeColor,
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, delete it!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            $(".logout").on("click", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __('Are you sure you want to log out?') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: themeColor,
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, Logout!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("logoutForm").submit();
                    }
                });
            });

            // form submit loader
            $('form').on('submit', function() {
                var submitButton = $(this).find('button[type="submit"]');

                submitButton.prop('disabled', true);
                submitButton.removeClass('px-5');

                submitButton.html(`<div class="d-flex align-items-center gap-1">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>Loading...</span>
                </div>`)
            });
        });
    </Script>

    <script>
        // Intentionally disabled: `/update/last/seen` endpoint is not available in this build.
    </script>


    {{-- Shop chat JS removed (no shop panel) --}}



    <script>
        // show active menu
        $(document).ready(function() {
            let activeMenuItem = $('.vertical-nav-menu .menu.active');

            if (activeMenuItem.length) {
                activeMenuItem.closest('.collapse').addClass('show');

                let sidebar = $('.scrollbar-sidebar');

                let offsetTop = activeMenuItem.offset().top;
                let sidebarTop = sidebar.offset().top;
                let scrollPosition = sidebar.scrollTop() + (offsetTop - sidebarTop) - 80;

                sidebar.animate({
                    scrollTop: scrollPosition
                }, 500);
            }
        });
    </script>

    <script>
        let cropper;
        let activeInput = null;
        let activePreview = null;

        document.addEventListener('change', function(e) {
            const input = e.target.closest(
                '[data-crop="true"]');

            if (!input || !input.matches('input[type="file"]')) return;

            const file = input.files[0];
            if (!file) return;

            activeInput = input;
            const width = parseInt(activeInput.dataset.width) || 500;
            const height = parseInt(activeInput.dataset.height) || 500;
            const filename = activeInput.dataset.filename || 'cropped';
            const calculatedAspectRatio = width / height;
            activePreview = document.getElementById(input.dataset.preview);

            const reader = new FileReader();
            reader.onload = function(event) {
                const image = document.getElementById('imageToCrop');
                image.src = event.target.result;

                const modal = new bootstrap.Modal(document.getElementById('cropperModal'));
                modal.show();

                if (cropper) cropper.destroy();
                cropper = new Cropper(image, {
                    aspectRatio: calculatedAspectRatio,
                    viewMode: 1,
                    autoCropArea: 1,
                });
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('cropAndSave').addEventListener('click', function() {
            if (!cropper || !activeInput || !activePreview) return;

            const width = parseInt(activeInput.dataset.width) || 500;
            const height = parseInt(activeInput.dataset.height) || 500;
            const filename = activeInput.dataset.filename || 'cropped';

            cropper.getCroppedCanvas({
                width: width,
                height: height
            }).toBlob(function(blob) {
                const croppedFile = new File([blob], `${filename}.webp`, {
                    type: 'image/webp'
                });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                activeInput.files = dataTransfer.files;

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (activePreview) {
                        activePreview.src = e.target.result;
                    } else {
                        console.error("activePreview is unexpectedly null inside reader.onload!");
                    }
                    bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
                    cropper.destroy();
                    cropper = null;
                    activeInput = null;
                    activePreview = null;
                };
                reader.readAsDataURL(croppedFile);
            });
        });
    </script>
    <script>
        document.addEventListener('click', function(e) {
            const dropzone = e.target.closest('.dropzone-area');
            if (!dropzone) return;

            const container = dropzone.closest('.dropzone-container');
            if (!container) return;

            const fileInput = container.querySelector('input[type="file"][data-crop="true"]');
            if (fileInput && !fileInput.disabled) {
                e.preventDefault();
                fileInput.click();
            }
        }, {
            passive: false
        });

        document.addEventListener('dragover', function(e) {
            const dropzone = e.target.closest('.dropzone-area');
            if (!dropzone) return;
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        document.addEventListener('dragleave', function(e) {
            const dropzone = e.target.closest('.dropzone-area');
            if (!dropzone) return;
            dropzone.classList.remove('dragover');
        });

        document.addEventListener('drop', function(e) {
            const dropzone = e.target.closest('.dropzone-area');
            if (!dropzone) return;
            e.preventDefault();
            dropzone.classList.remove('dragover');

            const container = dropzone.closest('.dropzone-container');
            if (!container) return;

            const fileInput = container.querySelector('input[type="file"][data-crop="true"]');
            if (!fileInput) return;

            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                fileInput.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            }
        });
    </script>

  <script>
    {!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/stand-alone-button.js')) !!}
  </script>


  <script src="{{ asset('assets/scripts/galleryFilemanager.js') }}"></script>


</body>

</html>
