<div class="compare-profile-and-identity">
    <div class="row g-4 gy-5">
        <div class="col-lg-6">
            <div class="user-profile userProfileDetails">
                <div class="userProfileDetails__header">
                    <h5 class="userProfileDetails__title">{{__('User Profile Info')}}</h5>
                    <input type="hidden" id="user_id_for_verified_status" value="{{ $user_details->id }}">
                </div>
                <div class="userDetails__wrapper userProfile__details mt-3">
                    <div class="userProfile__details__thumb mb-3"  style="height: 100px;width: 70px">
                        @if(!empty($user_details->image))
                           {!! render_image_markup_by_attachment_id($user_details->image, '', 'thumb') !!}
                         @else
                            <x-image.user-no-image/>
                         @endif
                    </div>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Full Name:') }}</strong> {{ $user_details->first_name.' '.$user_details->last_name }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Username:') }}</strong> {{ $user_details->username ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Email:') }}</strong> {{ $user_details->email ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Phone:') }}</strong> {{ $user_details->phone ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('Country:') }}</strong> {{ optional($user_details->user_country)->country ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('State:') }}</strong> {{ optional($user_details->user_state)->state ?? '' }}</p>
                    <p class="userDetails__wrapper__item"><strong>{{ __('City:') }}</strong> {{ optional($user_details->user_city)->city ?? '' }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="user-identity userProfileDetails">
                <div class="userProfileDetails__header">
                    <h5 class="userProfileDetails__title">{{__('User Identity Info')}}</h5>
                </div>
                <div class="userDetails__wrapper userProfile__details mt-3 ">
                    @if(!empty($user_identity_details))
                        <div class="userProfile__details__thumb mb-3 d-flex gap-3">
                            <a class="radius-5" href="{{ asset('assets/uploads/verification/'.$user_identity_details->front_document) }}" target="_blank">
                                @if(pathinfo($user_identity_details->front_document, PATHINFO_EXTENSION) === 'pdf')
                                    <a class="btn btn-info radius-5" href="{{ asset('assets/uploads/verification/'.$user_identity_details->front_document) }}" target="_blank">
                                        {{ __('View PDF Document') }}
                                    </a>
                                @else
                                    <div class="document-preview">
                                        <img src="{{ asset('assets/uploads/verification/'.$user_identity_details->front_document) }}" alt="front-document">
                                    </div>
                                @endif
                            </a>
                            <a class="radius-5" href="{{ asset('assets/uploads/verification/'.$user_identity_details->back_document) }}" target="_blank">
                                @if( pathinfo($user_identity_details->back_document, PATHINFO_EXTENSION) === 'pdf')
                                    <a class="btn btn-info radius-5" href="{{ asset('assets/uploads/verification/'.$user_identity_details->back_document) }}" target="_blank">
                                        {{ __('View PDF Document') }}
                                    </a>
                                @else
                                    <div class="document-preview">
                                        <img src="{{ asset('assets/uploads/verification/'.$user_identity_details->back_document) }}" alt="back-document">
                                    </div>
                                @endif
                            </a>
                        </div>
                               @php
                                   if (!empty($user_identity_details->verify_by)) {
                                      $verify_by_name = \App\Models\Backend\Admin::find($user_identity_details->verify_by);
                                  }else{
                                       $verify_by_name = '';
                                  }
                               @endphp
                        <p class="userDetails__wrapper__item"><strong>{{ __('Verify by:') }}</strong> {{ $verify_by_name->name ?? '' }}</p>
                        <p class="userDetails__wrapper__item"><strong>{{ __('Zip Code:') }}</strong> {{ $user_identity_details->zip_code ?? '' }}</p>
                        <p class="userDetails__wrapper__item"><strong>{{ __('Address:') }}</strong> {{ $user_identity_details->address ?? '' }}</p>
                          @php
                              $request_country = \Modules\CountryManage\app\Models\Country::where('id', $user_identity_details->country_id)->first();
                              $request_state = \Modules\CountryManage\app\Models\State::where('id', $user_identity_details->state_id)->first();
                              $request_city = \Modules\CountryManage\app\Models\City::where('id', $user_identity_details->city_id)->first();
                          @endphp
                        <p class="userDetails__wrapper__item"><strong>{{ __('Country:') }}</strong> {{ $request_country->country }} </p>
                        <p class="userDetails__wrapper__item"><strong>{{ __('State:') }}</strong> {{ $request_state->state }}</p>
                        <p class="userDetails__wrapper__item"><strong>{{ __('City:') }}</strong> {{ $request_city->city }} </p>
                    @else
                        <div class="userProfileDetails__noInfo">
                            <h6 class="userProfileDetails__noInfo__title">{{ __('No Information') }}</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
