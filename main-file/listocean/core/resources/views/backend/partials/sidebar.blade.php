<div class="dashboard__left dashboard-left-content">
    <div class="dashboard__left__main">
        <div class="dashboard__left__close close-bars"><i class="fa-solid fa-times"></i></div>
        <div class="dashboard__top">
            <div class="dashboard__top__logo">
                <a href="{{route('admin.dashboard')}}">
                {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                </a>
            </div>
        </div>

        <div class="dashboard__bottom mt-5">
            <div class="dashboard__bottom__search mb-3">
                <input class="form--control  w-100" type="text" placeholder="{{ __('Search here') }}" id="search_sidebarList">
            </div>
            <ul class="dashboard__bottom__list dashboard-list">

                @can('admin-dashboard')
                    <li class="dashboard__bottom__list__item @if(request()->is('admin/dashboard')) active @endif">
                        <a href="{{route('admin.dashboard')}}"><i class="lab la-accessible-icon"></i>
                            <span class="icon_title">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                @endcan

                <!--Admin listing manage -->
                @canany(['user-listing-list', 'guest-listing-list', 'admin-listing-list', 'report-reason-list', 'listing-report-list'])
                    <li  class="dashboard__bottom__list__item has-children @if (request()->is('admin/listings/*')) active open show @endif">
                        <a href="javascript:void(0)"> <i class="las la-th-list"></i> {{ __('Listing Manage') }} </a>
                        <ul class="submenu">
                            @can('user-listing-list')
                            <li class="dashboard__bottom__list__item @if (request()->is('admin/listings/user-all-listings')) selected @endif">
                                <a href="{{ route('admin.user.all.listings') }}"> {{ __('All User Listings') }} </a>
                            </li>
                            @endcan

                            @if(!empty(get_static_option('guest_listing_allowed_disallowed')))
                               @can('guest-listing-list')
                                <li class="dashboard__bottom__list__item @if (request()->is('admin/listings/guest/all-listings')) selected @endif">
                                    <a href="{{ route('admin.guest.all.listings') }}"> {{ __('All Guest Listings') }} </a>
                                </li>
                               @endcan
                            @endif

                            @can('admin-listing-list')
                            <li class="dashboard__bottom__list__item @if (request()->is('admin/listings/all') || request()->is('admin/listings/add') || request()->is('admin/listings/admin-edit-listing/*')) selected @endif">
                                <a href="{{ route('admin.all.listings') }}"> {{ __('Admin All Listings') }} </a>
                            </li>
                           @endcan
                            @can('report-reason-list')
                            <li class="dashboard__bottom__list__item @if (request()->is('admin/listings/report/reason/all')) selected @endif">
                                <a href="{{ route('admin.report.reason.all') }}"> {{ __('Report Reason') }} </a>
                            </li>
                            @endcan
                             @can('listing-report-list')
                            <li class="dashboard__bottom__list__item @if (request()->is('admin/listings/report/all')) selected @endif">
                                <a href="{{ route('admin.listing.report.all') }}"> {{ __('Listing Reports') }} </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!--Admin advertisement manage -->
                @if(get_static_option('google_adsense_status') == 'on')
                    @canany(['advertisement-list', 'advertisement-add'])
                        <li  class="dashboard__bottom__list__item has-children @if (request()->is('admin/advertisement/*')) active open show @endif">
                            <a href="javascript:void(0)"> <i class="las la-ad"></i> {{ __('Advertisements Manage') }} </a>
                            <ul class="submenu">
                                @can('advertisement-list')
                                <li class="dashboard__bottom__list__item @if (request()->is('admin/advertisement/index')) selected @endif">
                                    <a href="{{ route('admin.advertisement') }}"> {{ __('All Advertisements') }} </a>
                                </li>
                                @endcan
                                @can('advertisement-add')
                                <li class="dashboard__bottom__list__item @if (request()->is('admin/advertisement/new')) selected @endif">
                                    <a href="{{ route('admin.advertisement.new') }}"> {{ __('Add New Advertisement') }} </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                     @endcanany
                @endif


               @canany(['user-list', 'user-deactivated-list', 'user-verify-status', 'user-add'])
                <li  class="dashboard__bottom__list__item has-children @if (request()->is('admin/user*')) active open show @endif">
                    <a href="javascript:void(0)"> <i class="las la-user-circle"></i> {{ __('User Manage') }} </a>
                    <ul class="submenu">
                        @can('user-list')
                            <li class="dashboard__bottom__list__item @if (request()->routeIs(['admin.user.all'])) selected @endif">
                                <a href="{{ route('admin.user.all') }}"> {{ __('All Users') }} </a>
                            </li>
                        @endcan
                        @can('user-deactivated-list')
                            <li class="dashboard__bottom__list__item @if (request()->routeIs(['admin.user.deactivated.all'])) selected @endif">
                                <a href="{{ route('admin.user.deactivated.all') }}"> {{ __('Deactivated Users') }} </a>
                            </li>
                           <li class="dashboard__bottom__list__item @if (request()->routeIs(['admin.user.restore'])) selected @endif">
                                <a href="{{ route('admin.user.restore') }}"> {{ __('Trash List') }} </a>
                            </li>
                        @endcan
                        @can('user-verify-status')
                            <li class="dashboard__bottom__list__item @if (request()->routeIs(['admin.user.verification.request'])) selected @endif">
                                <a href="{{ route('admin.user.verification.request') }}">
                                    {{ __('Identity Verify Requests') }} </a>
                            </li>
                        @endcan
                        @can('user-add')
                        <li class="dashboard__bottom__list__item @if (request()->routeIs(['admin.user.add'])) selected @endif">
                            <a href="{{ route('admin.user.add') }}">
                                {{ __('Add New User') }} </a>
                        </li>
                        @endcan
                    </ul>
                </li>
               @endcanany

               @canany(['category-list', 'category-add'])
                <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/category/*')) active open @endif">
                    <a href="javascript:void(0)"><i class="las la-th-list"></i>
                        <span class="icon_title">{{ __('Categories') }}</span>
                    </a>
                    <ul class="submenu" style="@if(request()->is('admin/category/*')) display:block; @endif">
                        @can('category-list')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/category/index')) selected @endif">
                            <a href="{{ route('admin.category') }}">{{ __('All Category') }}</a>
                        </li>
                        @endcan
                       @can('category-add')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/category/add-new-category')) selected @endif">
                            <a href="{{ route('admin.category.new') }}">{{ __('Add New Category') }}</a>
                        </li>
                        @endcan
                    </ul>
                </li>
               @endcanany

              @canany(['subcategory-list', 'subcategory-add'])
                <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/subcategory/*')) active open @endif">
                    <a href="javascript:void(0)"><i class="las la-th-list"></i>
                        <span class="icon_title">{{ __('Subcategories') }}</span>
                    </a>
                    <ul class="submenu" style="@if(request()->is('admin/subcategory/*')) display:block; @endif">
                        @can('subcategory-list')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/subcategory/index')) selected @endif">
                            <a href="{{ route('admin.subcategory') }}">{{ __('All Subcategories') }}</a>
                        </li>
                        @endcan
                        @can('subcategory-add')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/subcategory/add-new-subcategory')) selected @endif">
                            <a href="{{ route('admin.subcategory.new') }}">{{ __('Add New Subcategory') }}</a>
                        </li>
                       @endcan
                    </ul>
                  </li>
                @endcanany

                    <!-- Child Categories Manage -->
                    @canany(['child-category-list', 'child-category-add'])
                        <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/child-category/*')) active open @endif">
                            <a href="javascript:void(0)">
                                <i class="las la-th-list"></i>
                                <span class="icon_title">{{ __('Child Categories') }}</span>
                            </a>
                            <ul class="submenu" style="@if(request()->is('admin/child-category/*')) display:block; @endif">
                                @can('child-category-list')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/child-category/index')) selected @endif">
                                        <a href="{{ route('admin.child.category') }}">{{ __('All Child Categories') }}</a>
                                    </li>
                                @endcan
                                @can('child-category-add')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/child-category/add-new-child-category')) selected @endif">
                                        <a href="{{ route('admin.child.category.new') }}">{{ __('Add New Child Category') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    <!-- Pages Manage -->
                    @canany(['dynamic-page-list', 'dynamic-page-add'])
                        <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/dynamic-page*')) active open @endif">
                            <a href="javascript:void(0)">
                                <i class="las la-paste"></i>
                                <span class="icon_title">{{ __('Pages') }}</span>
                            </a>
                            <ul class="submenu" style="@if(request()->is('admin/dynamic-page/*')) display:block; @endif">
                                @can('dynamic-page-list')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/dynamic-page/all')) selected @endif">
                                        <a href="{{ route('admin.page') }}">{{ __('All Pages') }}</a>
                                    </li>
                                @endcan
                                @can('dynamic-page-add')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/dynamic-page/new')) selected @endif">
                                        <a href="{{ route('admin.page.new') }}">{{ __('Add New Page') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany


                    @include('backend.partials.module-list')


                @can('notifications-list')
                    <li class="dashboard__bottom__list__item @if(request()->is('admin/notification/*')) active @endif">
                        <a href="{{ route('admin.notification.all') }}"><i class="las la-bell"></i>{{ __('All Notification') }}</a>
                    </li>
                @endcan

                @can('notice-list')
                <li class="dashboard__bottom__list__item @if(request()->is('admin/notice/*')) active @endif">
                    <a href="{{ route('admin.all.notice') }}"><i class="las la-bell"></i>{{ __('Notice Settings') }}</a>
                </li>
                @endcan

              @can('google-map-settings')
                <li class="dashboard__bottom__list__item @if(request()->is('admin/map-settings/*')) active @endif">
                    <a href="{{ route('admin.map.settings.page') }}"><i class="las la-map"></i>{{ __('Google Map Settings') }}</a>
                </li>
               @endcan

                    <!-- Appearance Settings -->
                    @canany([
                        'navbar-global-variant', 'footer-global-variant', 'color-settings', 'typography-settings',
                        'typography-single-settings', 'font-add-settings', 'custom-font-delete', 'custom-font-status-change',
                        'widgets-list', 'widgets-add', 'widgets-delete', 'menu-list', 'menu-add', 'menu-edit', 'menu-delete',
                        'form-builder-list', 'form-builder-edit', 'form-builder-delete', 'form-builder-bulk.delete',
                        'media-upload', 'media-upload-delete', '404-page-settings', 'maintains-page-settings'
                    ])
                        <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/appearance-settings/*')) active open @endif">
                            <a href="javascript:void(0)">
                                <i class="las la-cogs"></i>
                                <span class="icon_title">{{ __('Appearance Settings') }}</span>
                            </a>
                            <ul class="submenu" style="@if(request()->is('admin/appearance-settings/*')) display:block; @endif">
                                @can('form-builder-list')
                                    <li class="dashboard__bottom__list__item @if (request()->is('admin/appearance-settings/form/*')) selected @endif">
                                        <a href="{{ route('admin.form') }}"> {{ __('Form Builder') }} </a>
                                    </li>
                                @endcan
                                @can('widgets-list')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/widgets')) selected @endif">
                                        <a href="{{ route('admin.widgets') }}">{{ __('Widget Builder') }}</a>
                                    </li>
                                @endcan
                                @can('menu-list')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/menu')) selected @endif">
                                        <a href="{{ route('admin.menu') }}">{{ __('Menu Manage') }}</a>
                                    </li>
                                @endcan
                                @can('navbar-global-variant')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/global-variant-navbar')) selected @endif">
                                        <a href="{{ route('admin.general.global.variant.navbar') }}">{{ __('Navbar Global Variant') }}</a>
                                    </li>
                                @endcan
                                @can('footer-global-variant')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/global-variant-footer')) selected @endif">
                                        <a href="{{ route('admin.general.global.variant.footer') }}">{{ __('Footer Global Variant') }}</a>
                                    </li>
                                @endcan
                                @can('color-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/color-settings')) selected @endif">
                                        <a href="{{ route('admin.general.color.settings') }}">{{ __('Color Settings') }}</a>
                                    </li>
                                @endcan
                                @can('typography-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/typography-settings')) selected @endif">
                                        <a href="{{ route('admin.general.typography.settings') }}">{{ __('Typography Settings') }}</a>
                                    </li>
                                @endcan
                                @can('media-upload')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/media-upload/page')) selected @endif">
                                        <a href="{{ route('admin.upload.media.images.page') }}">{{ __('Media Images Manage') }}</a>
                                    </li>
                                @endcan
                                @can('404-page-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/404-page-manage')) selected @endif">
                                        <a href="{{ route('admin.404.page.settings') }}">{{ __('404 Page Manage') }}</a>
                                    </li>
                                @endcan
                                @can('maintains-page-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/appearance-settings/maintains-page')) selected @endif">
                                        <a href="{{ route('admin.maintains.page.settings') }}">{{ __('Maintain Page Manage') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany([
                            'login-register-page-settings', 'listing-create-page-settings', 'listing-details-page-settings',
                            'listing-guest-page-settings', 'user-public-profile-page-settings'
                        ])
                        <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/page-settings/*')) active open @endif">
                            <a href="javascript:void(0)">
                                <i class="las la-envelope"></i>
                                <span class="icon_title">{{ __('Page Settings') }}</span>
                            </a>
                            <ul class="submenu" style="@if(request()->is('admin/page-settings/*')) display:block; @endif">
                                @can('login-register-page-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/page-settings/register-page')) selected @endif">
                                        <a href="{{ route('admin.login.register.page.settings') }}">{{ __('Login & Register Page') }}</a>
                                    </li>
                                @endcan
                                @can('listing-create-page-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/page-settings/listing-create-page/settings')) selected @endif">
                                        <a href="{{ route('admin.listing.create.settings') }}">{{ __('Listing Create Page Settings') }}</a>
                                    </li>
                                @endcan
                                @can('listing-details-page-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/page-settings/listing-details-page/settings')) selected @endif">
                                        <a href="{{ route('admin.listing.details.settings') }}">{{ __('Listing Details Page Settings') }}</a>
                                    </li>
                                @endcan
                                @can('listing-guest-page-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/page-settings/guest-listing/settings')) selected @endif">
                                        <a href="{{ route('admin.listing.guest.settings') }}">{{ __('Guest Listing Settings') }}</a>
                                    </li>
                                @endcan
                                @can('user-public-profile-page-settings')
                                    <li class="dashboard__bottom__list__item @if(request()->is('admin/page-settings/user-public-profile/settings')) selected @endif">
                                        <a href="{{ route('admin.user.public.profile.settings') }}">{{ __('User Public Profile Settings') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany(['smtp-settings'])
                    <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/email-settings/*')) active open @endif">
                    <a href="javascript:void(0)"><i class="las la-envelope"></i>
                        <span class="icon_title">{{ __('Email Settings') }}</span>
                    </a>
                        <ul class="submenu" style="@if(request()->is('admin/email-settings/*')) display:block; @endif">
                            <li class="dashboard__bottom__list__item @if(request()->is('admin/email-settings/smtp')) selected @endif">
                                <a href="{{ route('admin.email.smtp.settings') }}">{{ __('SMTP Settings') }}</a>
                            </li>
                            <li class="dashboard__bottom__list__item @if(request()->is('admin/email-settings/all-email-templates')) selected @endif">
                                <a href="{{ route('admin.email.template.all') }}">{{ __('All Email Templates') }}</a>
                            </li>
                        </ul>
                    </li>
                    @endcanany

                 @canany(['reading-settings', 'site-identity-settings', 'basic-settings', 'seo-settings', 'scripts-settings', 'custom-css-settings',
                          'custom-js-settings', 'sitemap-settings', 'gdpr-settings', 'license-setting', 'software-update-setting', 'cache-settings', 'database-upgrade-setting'
                          ])
                <li class="dashboard__bottom__list__item has-children @if(request()->is('admin/general-settings/*')) active open @endif">
                    <a href="javascript:void(0)"><i class="las la-cog"></i>
                        <span class="icon_title">{{ __('General Settings') }}</span>
                    </a>
                    <ul class="submenu" style="@if(request()->is('admin/general-settings/*')) display:block; @endif">
                        @can('reading-settings')
                            <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/reading')) selected @endif">
                                <a href="{{ route('admin.general.reading') }}">{{ __('Reading') }}</a>
                            </li>
                        @endcan
                       @can('site-identity-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/site-identity')) selected @endif">
                            <a href="{{ route('admin.general.site.identity') }}">{{ __('Site Identity') }}</a>
                        </li>
                        @endcan
                        @can('basic-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/basic-settings')) selected @endif">
                            <a href="{{ route('admin.general.basic.settings') }}">{{ __('Basic Settings') }}</a>
                        </li>
                       @endcan
                       @can('seo-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/seo-settings')) selected @endif">
                            <a href="{{ route('admin.general.seo.settings') }}">{{ __('SEO Settings') }}</a>
                        </li>
                       @endcan
                      @can('scripts-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/scripts')) selected @endif">
                            <a href="{{ route('admin.general.scripts.settings') }}">{{ __('Third Party Scripts') }}</a>
                        </li>
                      @endcan
                      @can('custom-css-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/custom-css')) selected @endif">
                            <a href="{{ route('admin.general.custom.css') }}">{{ __('Custom CSS') }}</a>
                        </li>
                      @endcan
                       @can('custom-js-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/custom-js')) selected @endif">
                            <a href="{{ route('admin.general.custom.js') }}">{{ __('Custom JS') }}</a>
                        </li>
                      @endcan
                      @can('sitemap-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/sitemap-settings')) selected @endif">
                            <a href="{{ route('admin.general.sitemap.settings') }}">{{ __('Sitemap Settings') }}</a>
                        </li>
                      @endcan
                     @can('gdpr-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/gdpr-settings')) selected @endif">
                            <a href="{{ route('admin.general.gdpr.settings') }}">{{ __('Gdpr Settings') }}</a>
                        </li>
                     @endcan
                      @can('license-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/license-setting')) selected @endif">
                            <a href="{{ route('admin.general.license.settings') }}">{{ __('Licence Settings') }}</a>
                        </li>
                       @endcan
                      @can('software-update-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/software-update-setting')) selected @endif">
                            <a href="{{ route('admin.general.software.update.settings') }}">{{ __('Check Update') }}</a>
                        </li>
                     @endcan
                     @can('cache-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/cache-settings')) selected @endif">
                            <a href="{{ route('admin.general.cache.settings') }}">{{ __('Cache Settings') }}</a>
                        </li>
                     @endcan
                      @can('database-upgrade-settings')
                        <li class="dashboard__bottom__list__item @if(request()->is('admin/general-settings/database-upgrade')) selected @endif">
                            <a href="{{ route('admin.general.database.upgrade') }}">{{ __('Database Upgrade') }}</a>
                        </li>
                     @endcan
                    </ul>
                </li>
               @endcanany

                @can('languages-list')
                    <li class="dashboard__bottom__list__item @if(request()->is('admin/languages/*') || request()->is('admin/languages')) active @endif">
                        <a href="{{ route('admin.languages') }}"><i class="las la-language"></i> <span class="icon_title">{{ __('Languages') }}</span></a>
                    </li>
                @endcan

                <li class="dashboard__bottom__list__item">
                    <a href="{{ route('admin.logout') }}"> <i class="las la-sign-out-alt"></i> <span class="icon_title">{{ __('Log Out') }}</span></a>
                </li>
            </ul>
        </div>
    </div>
</div>







