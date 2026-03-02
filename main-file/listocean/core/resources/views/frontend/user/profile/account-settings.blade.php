@extends('frontend.layout.master')
@section('site-title')
    {{__('Account Settings')}}
@endsection
@section('style')
    <x-media.css/>
    <style>
        .img-wrap {
            width: 111px;
        }

        .input-form {
            position: relative;
        }

        .id-upload-btn {
            cursor: pointer;
            border: 1px solid #ccc;
            padding: 8px 12px;
            display: inline-block;
        }

        .id-upload-btn i {
            margin-right: 5px;
        }

        .file-name {
            display: inline-block;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Style for hiding the file input */
        input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        label.d-block.file-name {
            max-width: fit-content !important;
        }

        .single-input {
            display: grid;
        }
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
        }

    /* check box css stat    */
        .checkbox-group {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        /* Style the label */
        .checkbox-group label {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            cursor: pointer;
        }

        /* Style the custom checkbox */
        .checkbox-group input[type="checkbox"] {
            display: none;
        }

        .checkbox-group input[type="checkbox"] + span {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-radius: 4px;
            margin-right: 8px;
            cursor: pointer;
            position: relative; /* Add relative positioning */
        }

        /* Style the custom checkbox when checked */
        .checkbox-group input[type="checkbox"]:checked + span {
            background-color: var(--color-6)!important;;
            border-color: var(--color-6)!important;;
        }

        /* Style the checkmark inside the custom checkbox */
        .checkbox-group input[type="checkbox"] + span::after {
            content: "\2713";
            font-size: 14px;
            color: #fff;
            display: none;
            position: absolute; /* Position the checkmark */
            top: 50%; /* Center vertically */
            left: 50%; /* Center horizontally */
            transform: translate(-50%, -50%); /* Center the checkmark */
        }

        /* Show the checkmark inside the custom checkbox when checked */
        .checkbox-group input[type="checkbox"]:checked + span::after {
            display: inline-block;
        }
    /*    end */

        .select2-container {
            z-index: 9999999;
        }

        .profile-setting.dark-mode-enabled .down-body-wraper,
        .profile-setting.dark-mode-enabled .main-body,
        .profile-setting.dark-mode-enabled .tab-content-wraper,
        .profile-setting.dark-mode-enabled .account-info {
            background: #111827 !important;
            color: #e5e7eb !important;
        }

        .profile-setting.dark-mode-enabled .head4,
        .profile-setting.dark-mode-enabled .title,
        .profile-setting.dark-mode-enabled label,
        .profile-setting.dark-mode-enabled p,
        .profile-setting.dark-mode-enabled span,
        .profile-setting.dark-mode-enabled h3,
        .profile-setting.dark-mode-enabled h4 {
            color: #e5e7eb !important;
        }

        .profile-setting.dark-mode-enabled .setting-tab .nav-link {
            color: #cbd5e1 !important;
        }

        .profile-setting.dark-mode-enabled .setting-tab .nav-link.active {
            color: #ffffff !important;
            border-color: #374151 !important;
            background: #1f2937 !important;
        }

        .profile-setting.dark-mode-enabled input,
        .profile-setting.dark-mode-enabled textarea,
        .profile-setting.dark-mode-enabled .select2-container--default .select2-selection--single {
            background: #1f2937 !important;
            color: #e5e7eb !important;
            border-color: #374151 !important;
        }

        .appearance-toggle-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
        }

        .profile-setting.dark-mode-enabled .appearance-toggle-wrap {
            border-color: #374151;
            background: #1f2937;
        }

    </style>
@endsection
@section('content')
<div id="accountProfileThemeRoot" class="profile-setting setting-page verify-identity section-padding2">
      <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-validation.frontend-error />
                                <x-frontend.user.responsive-icon/>
                                <div class="setting-btn-part">
                                    <div class="setting-tab nav nav-tabs" id="setting-tabbuttons">
                                        <a href="javascript:void(0)" class="nav-link active" data-bs-toggle="tab" data-bs-target="#identity-verification">{{ __('Identity Verification') }}</a>
                                        <a href="javascript:void(0)" class="nav-link" data-bs-toggle="tab" data-bs-target="#change-password">{{ __('Change Password') }}</a>
                                        @if(moduleExists('Membership'))
                                            @if(membershipModuleExistsAndEnable('Membership'))
                                                @php
                                                    $user_membership = \Modules\Membership\app\Models\UserMembership::where('user_id', \Illuminate\Support\Facades\Auth::guard('web')->user()->id)->first();
                                                @endphp
                                                @if($user_membership->business_hour === 1)
                                                      <a href="javascript:void(0)" class="nav-link" data-bs-toggle="tab" data-bs-target="#business-hours">{{ __('Business Hours') }}</a>
                                                @endif
                                            @endif
                                        @endif
                                        <a href="javascript:void(0)" class="nav-link" data-bs-toggle="tab" data-bs-target="#appearance-settings">{{ __('Appearance') }}</a>
                                        <a href="javascript:void(0)" class="nav-link" data-bs-toggle="tab" data-bs-target="#deactivate-delete-account">{{ __('Deactivate/Delete Account') }}</a>
                                    </div>
                                    <div class="setting-tab-content tab-content">
                                        <div class="tab-pane fade" id="change-password">
                                            <div class="tab-content-wraper box-shadow1 change-password-part">
                                                <h3 class="head4">{{ __('Change Password') }}</h3>
                                                <p class="dashboard_accountSettings__para mb-24">{{ __('Last changed') }}
                                                    @if(Auth::guard('web')->user()->password_changed_at)
                                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', Auth::guard('web')->user()->password_changed_at)->diffForHumans() }}
                                                    @endif
                                                </p>
                                                <form action="{{route('user.account.settings')}}" method="post">
                                                    @csrf
                                                    <div class="input-wraper">
                                                        <label for="#current-password">{{ __('Current Password') }}</label>
                                                        <input type="password" name="current_password" id="current_password" autocomplete="current-password" placeholder="{{ __('Current Password') }}">
                                                    </div>
                                                    <div class="input-wraper mt-3">
                                                        <label for="#new-password">{{ __('New Password') }}</label>
                                                        <input type="password" name="new_password" id="new_password" autocomplete="new-password" placeholder="{{ __('New Password') }}">
                                                    </div>
                                                    <div class="input-wraper mt-3">
                                                        <label for="#re-new-password">{{ __('Re-Enter New Password') }}</label>
                                                        <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" placeholder="{{ __('Re-Enter New Password') }}">
                                                    </div>
                                                    <div class="save-change-btn mt-3">
                                                        <button type="submit" class="red-btn">{{ __('Save Changes') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>


                                        <div class="tab-pane fade show active" id="identity-verification">
                                            <!--verify step 01 -->
                                            @if(!is_null($user_verify_info) && $user_verify_info->status === 1)
                                                <!--Verify your identity done -->
                                                <div class="tab-content-wraper identity-varified identity-verification mt-4 box-shadow1">
                                                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="28" cy="28" r="28" fill="#22C55E"/>
                                                        <path d="M24.5903 36.2106C24.1101 36.2106 23.6538 36.0185 23.3177 35.6823L16.5223 28.8869C15.8259 28.1906 15.8259 27.038 16.5223 26.3417C17.2186 25.6453 18.3712 25.6453 19.0675 26.3417L24.5903 31.8644L36.9325 19.5223C37.6288 18.8259 38.7814 18.8259 39.4777 19.5223C40.1741 20.2186 40.1741 21.3712 39.4777 22.0675L25.8629 35.6823C25.5268 36.0185 25.0705 36.2106 24.5903 36.2106Z" fill="white"/>
                                                    </svg>
                                                    <div class="text-part">
                                                        <h3 class="head4">{{ __('Your identity is verified') }}</h3>
                                                        <p>{{ __('Your identity has been verified by our team.') }}</p>
                                                    </div>
                                                </div>
                                            @elseif(!is_null($user_verify_info) && $user_verify_info->status === 0)
                                                <!-- Pending verification -->
                                                <div class="tab-content-wraper identity-verification mt-4 box-shadow1">
                                                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="28" cy="28" r="28" fill="#FFCC00"/>
                                                        <path d="M28 14V32" stroke="white" stroke-width="4" stroke-linecap="round"/>
                                                        <path d="M28 40H28.01" stroke="white" stroke-width="4" stroke-linecap="round"/>
                                                    </svg>
                                                    <div class="text-part">
                                                        <h3 class="head4">{{ __('Your identity verification is pending') }}</h3>
                                                        <p>{{ __('Your identity verification is under review. We will notify you once the review is complete.') }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <!--Verify first step -->
                                                @if($user_verify_info && $user_verify_info->status === 2)
                                                    <div class="tab-content-wraper box-shadow1 identity-verification mb-3">
                                                        <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="28" cy="28" r="28" fill="red"/>
                                                            <path d="M28 14V32" stroke="white" stroke-width="4" stroke-linecap="round"/>
                                                            <path d="M28 40H28.01" stroke="white" stroke-width="4" stroke-linecap="round"/>
                                                        </svg>
                                                            <span class="text-danger mt-2 mb-2">{{ __('Your account identity verification has been declined. Please resubmit your information.') }}</span>
                                                    </div>
                                                @endif

                                                <div class="tab-content-wraper box-shadow1 identity-verification">
                                                    <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15.5 6.33335H17.8333M15.5 11H17.8333M6.16667 15.6667H17.8333M1.5 5.16669C1.5 4.23843 1.86875 3.34819 2.52513 2.69181C3.1815 2.03544 4.07174 1.66669 5 1.66669H19C19.9283 1.66669 20.8185 2.03544 21.4749 2.69181C22.1313 3.34819 22.5 4.23843 22.5 5.16669V16.8334C22.5 17.7616 22.1313 18.6519 21.4749 19.3082C20.8185 19.9646 19.9283 20.3334 19 20.3334H5C4.07174 20.3334 3.1815 19.9646 2.52513 19.3082C1.86875 18.6519 1.5 17.7616 1.5 16.8334V5.16669ZM6.16667 8.66669C6.16667 9.28553 6.4125 9.87902 6.85008 10.3166C7.28767 10.7542 7.88116 11 8.5 11C9.11884 11 9.71233 10.7542 10.1499 10.3166C10.5875 9.87902 10.8333 9.28553 10.8333 8.66669C10.8333 8.04785 10.5875 7.45436 10.1499 7.01677C9.71233 6.57919 9.11884 6.33335 8.5 6.33335C7.88116 6.33335 7.28767 6.57919 6.85008 7.01677C6.4125 7.45436 6.16667 8.04785 6.16667 8.66669Z" stroke="#F76631" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <div class="text-part">
                                                        <h3 class="head4">{{ __('Verify your identity') }}</h3>
                                                        <p>{{ __('We require you to verify your identity to keep the platform safe') }}</p>
                                                        <a href="javascript:void(0)" class="red-btn"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#identifyVerifyModal">{{ __('Verify Identity') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="tab-pane fade" id="appearance-settings">
                                            <div class="tab-content-wraper box-shadow1">
                                                <h3 class="head4">{{ __('Appearance') }}</h3>
                                                <p class="dashboard_accountSettings__para mb-24">{{ __('Set your account profile theme preference.') }}</p>
                                                <div class="appearance-toggle-wrap">
                                                    <div>
                                                        <strong>{{ __('Dark Mode') }}</strong>
                                                        <p class="mb-0">{{ __('Use a darker color scheme on your profile account pages.') }}</p>
                                                    </div>
                                                    <div class="form-check form-switch m-0">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="accountDarkModeToggle">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!--business hours  -->
                                        @include('frontend.user.profile.business-hours')

                                        <div class="tab-pane fade" id="deactivate-delete-account">
                                            <div class="tab-content-wraper box-shadow1 business-hours">
                                                <div class="account-info">
                                                    <h4 class="title"> {{ __('Deactivate/Delete account') }} </h4>
                                                    <p class="text-danger mt-2">
                                                        @if(!empty($user_account_info))
                                                            @if($user_account_info->status === 0)
                                                                {{ __('Currently your account is deactivated. You can activate from here.') }}
                                                            @elseif($user_account_info->status === 1)
                                                                {{ __('Your account has been deleted') }}
                                                            @endif
                                                        @else
                                                            {{ __('You can deactivate your account temporarily or Delete permanently') }}
                                                        @endif
                                                    </p>
                                                </div>

                                                <div class="d-flex gap-3">
                                                    <div class="save-change-btn mt-3">
                                                        @if(empty($user_account_info))
                                                            <a href="javascript:void(0)" class="red-btn"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#deactivateAccount">{{ __('Deactivate') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div class="save-change-btn mt-3">
                                                        <a href="javascript:void(0)" class="btn btn-danger"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#deleteAccount">
                                                            {{ __('Delete') }}
                                                        </a>
                                                    </div>

                                                    <div class="save-change-btn mt-3">
                                                        @if(!empty($user_account_info))
                                                            @if($user_account_info->status === 0)
                                                                <a href="{{route('user.account.deactive.cancel',$user_account_info->user_id)}}" class="success-btn">
                                                                    {{__('Activate Your Account')}}</a>
                                                            @elseif($user_account_info->status === 1)
                                                                <a href="javascript:void(0)" class="danger-btn">{{__('Already Delete Account')}}</a>
                                                            @endif
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
                 </div>
             </div>
      </div>
</div>

    <!-- Identity Verify Modal -->
    <div class="modal fade" id="identifyVerifyModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Verify identity') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="custom-form">
                        <form action="{{route('user.profile.verify')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-form">
                                        <label>{{ __('Select Identification Type') }} <span class="text-danger">*</span></label>
                                        <div class="checkbox-group">
                                            <input type="hidden" name="identification_type" value="{{ $user_verify_info?->identification_type }}">
                                            <label>
                                                <input type="checkbox" value="national">
                                                <span></span>
                                                {{ __('National ID') }}
                                            </label>
                                            <label>
                                                <input type="checkbox" value="passport">
                                                <span></span>
                                                {{ __('Passport') }}
                                            </label>
                                            <label>
                                                <input type="checkbox" value="driving">
                                                <span></span>
                                                {{ __('Driving License') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="single-flex-input">

                                        <div class="single-input">
                                            <label class="label-title">{{ __('Select Your Country') }}  <span class="text-danger">*</span></label>
                                            <select name="country_id" id="country_id" class="select2_activation">
                                                <option value="">{{ __('Select Country') }}</option>
                                                @foreach($all_countries = \Modules\CountryManage\app\Models\Country::all_countries() as $country)
                                                    <option value="{{ $country->id }}" @if(!empty($user_verify_info) && $country->id == $user_verify_info->country_id) selected @endif>{{ $country->country }}</option>
                                                @endforeach
                                            </select>
                                            <span class="country_info"></span>
                                        </div>

                                        <div class="single-input">
                                            <label class="label-title">{{ __('Select Your State') }}  <span class="text-danger">*</span></label>
                                            <select name="state_id" id="state_id" class="select2_activation">
                                                <option value="">{{ __('Select State') }}</option>
                                                @foreach($all_states = \Modules\CountryManage\app\Models\State::all_states() as $state)
                                                    <option value="{{ $state->id }}" @if(!empty($user_verify_info) && $state->id == $user_verify_info->state_id) selected @endif>{{ $state->state }}</option>
                                                @endforeach
                                            </select>
                                            <span class="country_info"></span>
                                        </div>
                                        <div class="single-input">
                                            <label class="label-title">{{ __('Select Your City') }}  <span class="text-danger">*</span></label>
                                            <select name="city_id" id="city_id" class="select2_activation">
                                                <option value="">{{ __('Select City') }}</option>
                                                @foreach($all_cities = \Modules\CountryManage\app\Models\City::all_cities() as $city)
                                                    <option value="{{ $city->id }}" @if(!empty($user_verify_info) && $city->id == $user_verify_info->city_id) selected @endif>{{ $city->city }}</option>
                                                @endforeach
                                            </select>
                                            <span class="country_info"></span>
                                        </div>
                                    </div>
                               </div>
                                <div class="col-12 mt-3">
                                    <div class="input-form">
                                        <label class="d-block" for="national-id-number">{{ __('Zip Code') }} <span class="text-danger">*</span> </label>
                                        <input class="form-control w-100" type="text" name="zip_code" id="zip_code" value="{{  $user_verify_info?->zip_code }}">
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="input-form">
                                        <label class="d-block" for="address">{{ __('Address') }} <span class="text-danger">*</span> </label>
                                        <input class="form-control w-100" type="text" name="address" id="address" value="{{  $user_verify_info?->address }}">
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="input-form">
                                        <label class="d-block" for="identification_number">{{ __('National NID/Passport/Driving License Number') }} <span class="text-danger">*</span> </label>
                                        <input class="form-control w-100" type="text" name="identification_number" id="identification_number" value="{{ $user_verify_info?->identification_number }}">
                                    </div>
                                </div>
                                <div class="col-6 mb-3 mt-3">
                                    <div class="input-form">
                                        <div class="id-front">
                                            <label class="d-block file-name" for="id-front">{{__('Upload Front Part')}} <span class="text-danger">*</span> </label>
                                            <label for="id-front" class="id-upload-btn">
                                                <i class="las la-arrow-alt-circle-up fs-5"></i>{{__('Upload Front Part')}}
                                            </label>
                                            <input class="w-100" name="front_document" id="id-front" type="file" value="{{$user_verify_info->front_document ?? ''}}">
                                        </div>
                                        <div class="file-preview mt-2">
                                            <img id="front-preview" src="#" alt="Front Part Preview" style="display:none; max-width:100%; max-height:200px;" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 mb-3 mt-3">
                                    <div class="input-form">
                                        <div class="id-back">
                                            <label class="d-block file-name" for="id-back">{{__('Upload Back Part')}} <span class="text-danger">*</span> </label>
                                            <label for="id-back" class="id-upload-btn">
                                                <i class="las la-arrow-alt-circle-up fs-5"></i>{{__('Upload Back Part')}}
                                            </label>
                                            <input class="w-100 file-name" name="back_document" id="id-back" type="file" value="{{$user_verify_info->back_document ?? ''}}">
                                        </div>
                                        <div class="file-preview mt-2">
                                            <img id="back-preview" src="#" alt="Back Part Preview" style="display:none; max-width:100%; max-height:200px;" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <!-- Deactivate Account Modal -->
    <div class="modal fade" id="deactivateAccount" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Deactivate Account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="custom-form">
                        <form action="{{route('user.account.deactive')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-form">
                                        <label for="CurrentPassword" class="label_title__postition">{{ __('Deactivation reason') }} <span class="text-danger">*</span> </label>
                                        <div class="input-form-select radius-10">
                                            <select class="select2_activation" name="reason" id="reason2">
                                                <option value="For Vacation">{{__('For Vacation')}}</option>
                                                <option value="Personal Reasons">{{__('Personal Reasons')}}</option>
                                                <option value="Others">{{__('Others')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="input-form">
                                        <label for="newPassword" class="label_title__postition">{{ __('Describe') }} <span class="text-danger">*</span> </label>
                                        <textarea class="form-control radius-10"  name="description" id="description" cols="30" rows="4" placeholder="{{ __('e.g. explain why you are deactivating') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Deactivate Now') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccount" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Delete account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="custom-form">
                        <form action="{{ route('user.account.delete') }}" method="post">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="dashboard_accountDelete">
                                        <p class="dashboard_accountDelete__reason">{{ __('Account deletion is permanent') }}</p>
                                        <p class="dashboard_accountDelete__reason">{{ __('We remove all your data') }}</p>
                                        <p class="dashboard_accountDelete__reason">{{ __('You can’t log in to this account anymore') }}</p>
                                        <p class="dashboard_accountDelete__reason">{{ __('Any services that are currently on progress will be suspended') }}</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-form">
                                        <label for="CurrentPassword" class="label_title__postition">{{ __('Delete reason') }} <span class="text-danger">*</span> </label>
                                        <div class="input-form-select radius-10">
                                            <select class="select2_activation" name="reason" id="reason">
                                                <option value="For Vacation">{{__('For Vacation')}}</option>
                                                <option value="Personal Reasons">{{__('Personal Reasons')}}</option>
                                                <option value="Others">{{__('Others')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-form">
                                        <label for="newPassword" class="label_title__postition">{{ __('Describe') }} <span class="text-danger">*</span> </label>
                                        <textarea class="form-control radius-10"  name="description" id="description" cols="30" rows="4"  placeholder="{{ __('e.g. explain why you are deactivating') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Delete Now') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <x-media.markup :type="'web'"/>
@endsection
@section('scripts')
  @include('frontend.user.profile.account-settings-js')
   <x-media.js :type="'web'"/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){

                // Set default checkbox
                $('[value="{{ $user_verify_info?->identification_type }}"]').prop('checked', true);
                $(document).on('change', '.checkbox-group input[type="checkbox"]', function () {
                    if ($('[value="national_id"]').is(':checked')) {
                        $('.checkbox-group input[type="checkbox"]').not('[value="national_id"]').prop('checked', false);
                    }
                    let checkedCheckboxes = $('.checkbox-group input[type="checkbox"]:checked');
                    if (checkedCheckboxes.length > 1) {
                        checkedCheckboxes.not(this).prop('checked', false);
                    }
                    $('input[name="identification_type"]').val('');
                    let value = $(this).val();
                    $('input[name="identification_type"]').val(value);
                });

                     @php
                      $front_document  = $user_verify_info?->front_document;
                      $back_document =  $user_verify_info?->back_document;
                     @endphp

              $(document).on('change', 'input[type="file"]', function () {
                    let fileName = $(this).val().split('\\').pop();
                    let input = this;
                    if (input.files && input.files[0]) {
                        let fileSize = input.files[0].size;
                        let maxSize = 10 * 1024 * 1024;
                        if (fileSize <= maxSize) {
                            let fileType = input.files[0].type;
                            if (fileType === 'application/pdf' || /^(image\/(jpg|jpeg|png|webp))$/.test(fileType)) {
                                $(input).siblings('.file-name').text(fileName);
                                if (/^image\/(jpg|jpeg|png|webp)$/.test(fileType)) {
                                    let reader = new FileReader();
                                    reader.onload = function(e) {
                                        // Update the preview for both front and back parts
                                        $(input).closest('.input-form').find('.file-preview img').attr('src', e.target.result).show();
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                } else if (fileType === 'application/pdf') {
                                    // Clear any existing image preview
                                    $(input).closest('.input-form').find('.file-preview img').attr('src', '').hide();
                                }
                            } else {
                                let error_message_for_file = '{{__('Unsupported file type. Please select a PDF, JPG, PNG, JPEG, or WEBP file.')}}'
                                alert(error_message_for_file);
                                $(input).val('');
                                $(input).siblings('.file-name').text('');
                                // Clear the image preview
                                $(input).closest('.input-form').find('.file-preview img').attr('src', '').hide();
                            }
                        } else {
                            // File size exceeds the maximum limit
                            let error_message_for_file = '{{__('File size exceeds the maximum limit of 10 MB.')}}'
                            alert(error_message_for_file);
                            $(input).val('');
                            $(input).siblings('.file-name').text('');
                            // Clear the image preview
                            $(input).closest('.input-form').find('.file-preview img').attr('src', '').hide();
                        }
                    }
                });


                // Check if old values exist and set them automatically
                @if (!empty($front_document))
                // Set the file name for the front document
                $('input[name="front_document"]').siblings('.file-name').text('{{ basename($front_document) }}');

                // Display the image preview for the front document
                let frontImageSrc = '{{ asset('assets/uploads/verification/' . $front_document) }}';
                $('input[name="front_document"]').closest('.input-form').find('.file-preview img').attr('src', frontImageSrc).show();
                @endif

                @if (!empty($back_document))
                // Set the file name for the back document
                $('input[name="back_document"]').siblings('.file-name').text('{{ basename($back_document) }}');

                // Display the image preview for the back document
                let backImageSrc = '{{ asset('assets/uploads/verification/' . $back_document) }}';
                $('input[name="back_document"]').closest('.input-form').find('.file-preview img').attr('src', backImageSrc).show();
                @endif

                // modal close
                $('.close').on('click', function (){
                    $('#media_upload_modal').modal('hide');
                });

                const darkModeKey = 'sellupnow_user_dark_mode';
                const profileThemeRoot = $('#accountProfileThemeRoot');
                const darkModeToggle = $('#accountDarkModeToggle');

                const applyDarkModeState = (isDarkMode) => {
                    profileThemeRoot.toggleClass('dark-mode-enabled', !!isDarkMode);
                    darkModeToggle.prop('checked', !!isDarkMode);
                };

                const savedDarkMode = localStorage.getItem(darkModeKey) === '1';
                applyDarkModeState(savedDarkMode);

                darkModeToggle.on('change', function () {
                    const enabled = $(this).is(':checked');
                    localStorage.setItem(darkModeKey, enabled ? '1' : '0');
                    applyDarkModeState(enabled);
                });

                $('#reason').select2({
                    dropdownParent: $('#deleteAccount')
                });
                $('#reason2').select2({
                    dropdownParent: $('#deactivateAccount')
                });

                $('.hours_modal_hide').on('click', function (){
                    $('#customize-business-hour').modal('hide');
                });

            });
        })(jQuery);
    </script>
@endsection

