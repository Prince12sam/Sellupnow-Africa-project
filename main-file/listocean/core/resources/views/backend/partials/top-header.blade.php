<div class="dashboard__header single_border_bottom">
    <div class="row gx-4 align-items-center justify-content-between">
       <!--left sidebar open close start -->
        <div class="col-sm-2">
            <div class="dashboard__header__left">
                <div class="dashboard__header__left__inner">
                    <span class="dashboard__sidebarIcon__mobile sidebar-icon d-lg-none"></span>
                </div>
            </div>
        </div>
        <!--end -->

        <div class="col-sm-4">
            <div class="dashboard__header__right">
                <div class="dashboard__header__right__flex">
                    <!--search global search start -->
                     @include('backend.partials.global-search')
                    <!-- end -->
                    <!--Dark and light mode start -->
                    <div class="dashboard__header__right__item">
                        <span  class="dark_mode_check dashboard__header__notification__icon @if(get_static_option('site_admin_dark_mode') == 'on') lightMode @else nightMode  @endif" id="mode_change">
                            <i class="material-symbols-outlined"></i>
                        </span>
                        <input type="hidden" value="{{get_static_option('site_admin_dark_mode') ?? 'lightMode' }}" id="darkModeValue">
                    </div>
                    <!-- end -->
                    <!--Notifications start -->
                    @include('backend.partials.notifications')
                    <!--end -->
                    <!--Admin profile start -->
                    <div class="dashboard__header__right__item">
                        <div class="dashboard__header__author">
                            <a href="javascript:void(0)" class="dashboard__header__author__flex flex-btn">
                                <div class="dashboard__header__author__thumb">
                                    @if(!empty(auth()->guard('admin')->user()->image))
                                        {!! render_image_markup_by_attachment_id(auth()->guard('admin')->user()->image,'avatar user-thumb') !!}
                                    @else
                                        <x-image.user-no-image/>
                                    @endif
                                </div>
                            </a>
                            <div class="dashboard__header__author__wrapper">
                                <div class="dashboard__header__author__wrapper__list">
                                    <a href="{{route('admin.profile.update')}}" class="dashboard__header__author__wrapper__list__item">{{ __('Edit Profile') }}</a>
                                    <a href="{{route('admin.profile.password.change')}}" class="dashboard__header__author__wrapper__list__item">{{ __('Password Change') }}</a>
                                    <a href="{{ route('admin.logout') }}" class="dashboard__header__author__wrapper__list__item">{{ __('Log Out') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end -->

                    <div class="dashboard__header__right__item">
                     <a class="cmnBtn btn_5 radius-5 @if(get_static_option('site_admin_dark_mode') == 'off') btn_bg_blue @else btn-dark  @endif" target="_blank" href="{{url('/')}}">
                            <i class="fas fa-external-link-alt mr-1"></i>   {{__('View Site')}}
                          </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
