@extends('frontend.layout.master')
@section('site_title')
    {{ __('My Dashboard') }}
@endsection
@section('content')
    <div class="profile-setting my-account section-padding2">
        <div class="container-1920 plr1">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                      @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>
                                @if(moduleExists('Membership'))
                                    @if(membershipModuleExistsAndEnable('Membership'))
                                        @include('membership::frontend.user.membership.user-dashboard-membership-message')
                                    @endif
                                @endif
                                <!--Seller Part-->
                                <div class="seller-part">
                                    <div class="seller-part-inner style-01 box-shadow1">
                                        <div class="seller-details">
                                            <div class="seller-details-wraper">
                                                <div class="seller-img">
                                                    @if(!empty($user->image))
                                                        {!! render_image_markup_by_attachment_id($user->image) !!}
                                                    @else
                                                        <img src="{{ asset('assets/frontend/img/static/user-no-image.webp') }}" alt="No Image">
                                                    @endif
                                                </div>
                                                <div class="seller-name">
                                                    <div class="name">
                                                        <span>{{ $user->fullname }}</span>
                                                        <x-badge.user-verified-badge :user="$user"/>
                                                    </div>
                                                    <div class="member-listing">
                                                        <span class="listing">{{ $user_ads_posted }} {{ __('listing') }} </span>
                                                        <span class="dot"></span> {{ __('Member since') }} {{ optional($user->created_at)->format('Y') }}
                                                    </div>
                                                    <div class="seller-ratings mt-3">
                                                        @if($averageRating >=1)
                                                            <a href="javascript:void(0)" class="author_tag__review__star"> {!! ratting_star(round($averageRating, 1)) !!} </a>
                                                            <a href="javascript:void(0)" class="author_tag__review__para">  ({{ $user_review_count }}) </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="edit-btn">
                                                <a href="{{ route('user.profile') }}">{{ __('Edit Profile') }}</a>
                                            </div>
                                        </div>
                                        <div class="devider"></div>

                                        <div class="seller-contact">
                                            @if(!empty($user->address) || !empty($user->country_id) || !empty($user->state_id) || !empty($user->city_id))
                                                <div class="locations">
                                                    <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.49967 7.16667C5.49967 7.82971 5.76307 8.4656 6.23191 8.93444C6.70075 9.40328 7.33663 9.66667 7.99967 9.66667C8.66272 9.66667 9.2986 9.40328 9.76744 8.93444C10.2363 8.4656 10.4997 7.82971 10.4997 7.16667C10.4997 6.50363 10.2363 5.86774 9.76744 5.3989C9.2986 4.93006 8.66272 4.66667 7.99967 4.66667C7.33663 4.66667 6.70075 4.93006 6.23191 5.3989C5.76307 5.86774 5.49967 6.50363 5.49967 7.16667Z" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M12.7138 11.8808L9.17801 15.4167C8.8655 15.7289 8.44183 15.9042 8.00009 15.9042C7.55836 15.9042 7.13469 15.7289 6.82218 15.4167L3.28551 11.8808C2.3532 10.9485 1.71829 9.76058 1.46108 8.46739C1.20388 7.17419 1.33592 5.83376 1.84051 4.61561C2.34511 3.39745 3.19959 2.35628 4.29591 1.62376C5.39223 0.891229 6.68115 0.500244 7.99968 0.500244C9.31821 0.500244 10.6071 0.891229 11.7034 1.62376C12.7998 2.35628 13.6542 3.39745 14.1588 4.61561C14.6634 5.83376 14.7955 7.17419 14.5383 8.46739C14.2811 9.76058 13.6462 10.9485 12.7138 11.8808Z" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <span>{!! userProfileLocation($user) !!}</span>
                                                </div>
                                            @endif

                                            <div class="emails">
                                                <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.5 2.83335C0.5 2.39133 0.675595 1.9674 0.988155 1.65484C1.30072 1.34228 1.72464 1.16669 2.16667 1.16669H13.8333C14.2754 1.16669 14.6993 1.34228 15.0118 1.65484C15.3244 1.9674 15.5 2.39133 15.5 2.83335M0.5 2.83335V11.1667C0.5 11.6087 0.675595 12.0326 0.988155 12.3452C1.30072 12.6578 1.72464 12.8334 2.16667 12.8334H13.8333C14.2754 12.8334 14.6993 12.6578 15.0118 12.3452C15.3244 12.0326 15.5 11.6087 15.5 11.1667V2.83335M0.5 2.83335L8 7.83335L15.5 2.83335" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg> <span>{{ $user->email }}</span>
                                            </div>
                                            <div class="phones">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.16667 1.33331H5.5L7.16667 5.49998L5.08333 6.74998C5.9758 8.55959 7.44039 10.0242 9.25 10.9166L10.5 8.83331L14.6667 10.5V13.8333C14.6667 14.2753 14.4911 14.6993 14.1785 15.0118C13.866 15.3244 13.442 15.5 13 15.5C9.74939 15.3024 6.68346 13.9221 4.38069 11.6193C2.07792 9.31652 0.697541 6.25059 0.5 2.99998C0.5 2.55795 0.675595 2.13403 0.988155 1.82147C1.30072 1.50891 1.72464 1.33331 2.16667 1.33331Z" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span> {{ $user->phone }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Add Listing States-->
                                <div class="all-list-state mt-20">
                                    <div class="row g-3">
                                        <div class="col-md-3 col-md-3 col-6">
                                            <div class="list-state ad-posted">
                                                <h4 class="list-head">{{ $user_ads_posted }}</h4>
                                                <p class="post-state">{{ __('Ads Posted') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-md-3 col-6">
                                            <div class="list-state active-posted">
                                                <h4 class="list-head">{{ $user_active_listings }}</h4>
                                                <p class="post-state">{{ __('Active Listing') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-md-3 col-6">
                                            <div class="list-state deactivated-posted">
                                                <h4 class="list-head">{{  $user_deactivated_ads }}</h4>
                                                <p class="post-state">{{ __('Deactive Ads') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-md-3 col-6">
                                            <div class="list-state favorit-posted">
                                                <h4 class="list-head">{{ $user_favorite_ads }}</h4>
                                                <p class="post-state">{{ __('Favorite Ads') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--All Reviews-->
                                <div class="all-reviews box-shadow1 mt-20">
                                    <h4 class="dis-title">{{ __('All Reviews') }}</h4>
                                    <div class="review-tab-btn">
                                        <button class="review-recived me-4 active" data-target="#review-recived">{{ __('Reviews Received') }}</button>
                                        <button class="review-given" data-target="#review-given">{{ __('Reviews Given') }}</button>
                                    </div>
                                    <div class="review-wraper mt-20 active" id="review-recived">
                                        @if($user->reviews)
                                            @php
                                                $review_type = 'received';
                                            @endphp
                                            <x-user.user-reviews :reviews="$user->reviews" :user="$user" :reviewtype="$review_type"/>
                                        @endif
                                    </div>

                                    <div class="review-wraper mt-20" id="review-given">
                                        @if($user_given_reviews)
                                            @php
                                                $review_type = 'given';
                                            @endphp
                                            <x-user.user-reviews :reviews="$user_given_reviews" :user="$user" :reviewtype="$review_type"/>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
@endsection
