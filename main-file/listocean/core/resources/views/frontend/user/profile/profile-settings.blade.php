@extends('frontend.layout.master')
@section('site_title')
    {{ __('Profile Settings') }}
@endsection
@section('style')
    <x-media.css/>
    <style>
        .accountWrapper .userProfile .recentImg img {
            border-radius: 12px;
            height: 77px;
            width: 88px;
            animation-duration: auto;
        }
        .input-form{
            flex: 1;
        }
        .select2-container {
            z-index: 999;
        }

    </style>
@endsection
@section('content')
    <div class="profile-setting profile-pages section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper justify-content-center">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-validation.frontend-error />
                                <x-frontend.user.responsive-icon/>
                                <form id="update_profile_form" method="post">
                                    @csrf

                                    <div class="userProfile mb-24">
                                        <div class="seller-details-wraper">
                                            <div class="media-upload-btn-wrapper d-flex align-items-center gap-3">
                                                <div class="seller-details-wraper">
                                                    <div class="img-wrap seller-img p-0">
                                                        @if(!empty(Auth::guard('web')->user()->image))
                                                            {!! render_image_markup_by_attachment_id(Auth::guard('web')->user()->image,'','thumb') !!}
                                                        @else
                                                            <img src="{{ asset('assets/frontend/img/static/user-no-image.webp') }}" alt="No Image">
                                                        @endif
                                                    </div>
                                                </div>

                                                   <input type="hidden" id="image" name="image" value="{{Auth::guard('web')->user()->image}}">
                                                   <button type="button" class="btn media_upload_form_btn"
                                                           data-btntitle="{{__('Select Image')}}"
                                                           data-modaltitle="{{__('Upload Image')}}"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#media_upload_modal">
                                                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                           <path d="M17.5 12.5V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H4.16667C3.72464 17.5 3.30072 17.3244 2.98816 17.0118C2.67559 16.6993 2.5 16.2754 2.5 15.8333V12.5" stroke="#1E293B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                           <path d="M14.1673 6.66667L10.0007 2.5L5.83398 6.66667" stroke="#1E293B" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                           <path d="M10 2.5V12.5" stroke="#1E293B" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                                                       </svg>
                                                       {{__('Upload Photo')}}
                                                   </button>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-form-wraper">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div class="input-form">
                                                <label for="title">{{ __('First Name') }} <span class="text-danger">*</span> </label>
                                                <input id="first_name" value="{{ Auth::guard('web')->user()->first_name ?? '' }}" class="w-100 input-field">
                                            </div>

                                            <div class="input-form">
                                                <label for="title">{{ __('Last Name') }} <span class="text-danger">*</span> </label>
                                                <input id="last_name" value="{{ Auth::guard('web')->user()->last_name ?? '' }}" class="w-100 input-field">
                                            </div>
                                        </div>

                                        <div class="input-form">
                                            <label for="title">{{ __('Your Email') }} <span class="text-danger">*</span> </label>
                                            <input id="email" value="{{ Auth::guard('web')->user()->email ?? '' }}" class="w-100 input-field">
                                        </div>

                                        <div class="input-form">
                                            <label for="title">{{ __('Your Phone') }} <span class="text-danger">*</span> </label>
                                            <input id="phone" type="tel" value="{{ Auth::guard('web')->user()->phone ?? '' }}" class="w-100 input-field">
                                        </div>

                                        <div class="input-form">
                                            <x-form.country-dropdown :title="__('Select Your Country')" :id="'country_id'" :required="true"/>
                                        </div>

                                        <div class="d-flex justify-content-between gap-3">
                                            <div class="input-form">
                                                <x-form.state-dropdown :title="__('Select Your State')" :id="'state_id'" :required="true"/>
                                            </div>
                                            <div class="input-form">
                                                <x-form.city-dropdown :title="__('Select Your City')" :id="'city_id'" :required="false"/>
                                            </div>
                                        </div>

                                        <div class="input-form">
                                            <label for="address">{{ __('Address') }}</label>
                                            <textarea id="address" name="address" class="w-100 input-field" rows="2" style="resize:vertical;">{{ Auth::guard('web')->user()->address ?? '' }}</textarea>
                                        </div>

                                        <div class="input-form">
                                            <label for="about">{{ __('About / Bio') }}</label>
                                            <textarea id="about" name="about" class="w-100 input-field" rows="3" style="resize:vertical;">{{ Auth::guard('web')->user()->about ?? '' }}</textarea>
                                        </div>

                                        <div class="d-flex justify-content-between gap-3">
                                            <div class="input-form">
                                                <label>{{ __('Notifications') }}</label>
                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                               id="is_notifications_allowed"
                                                               {{ (Auth::guard('web')->user()->is_notifications_allowed ?? 1) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_notifications_allowed">{{ __('Enable notifications') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-form">
                                                <label>{{ __('Show Contact Info') }}</label>
                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                               id="is_contact_info_visible"
                                                               {{ (Auth::guard('web')->user()->is_contact_info_visible ?? 0) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_contact_info_visible">{{ __('Visible to other users') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="btn-wrapper mt-3">
                                        <button type="submit" id="user_profile_info_update" class="red-btn"> {{ __('Save changes') }} </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 @include('frontend.user.profile.edit-profile-info-modal')
    <x-media.markup :type="'web'"/>
@endsection
@section('scripts')
   @include('frontend.user.profile.profile-bg-js')
    <script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
    <x-media.js :type="'web'"/>
    @include('frontend.user.profile.profile-js')
    @if(session('success'))
        <script>
            toastr.success('{{ session('success') }}', 'Success');
        </script>
    @endif
@endsection
