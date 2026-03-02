@extends('backend.admin-master')
@section('site-title')
    {{__('Edit Profile')}}
@endsection
@section('style')
   <link rel="stylesheet" href="{{asset('assets/backend/css/select2.min.css')}}">
   <x-media.css/>
@endsection
@section('content')
    <div class="dashboard__body">
        <div class="dashboard__inner">
         <div class="row g-4 mt-0">
            <div class="col-xxl-6 col-lg-6 mt-3">
                <div class="dashboard__card bg__white padding-20 radius-10">
                    <div class="dashboard__card__header">
                        <h4 class="dashboard__card__header__title">{{ __('Edit Profile') }}</h4>
                    </div>
                    <x-validation.error/>
                    <div class="dashboard__card__inner mt-4">
                        <div class="form__input">
                            <form action="{{route('admin.profile.update')}}" method="POST" class="validateForm" enctype="multipart/form-data">
                                @csrf
                                    <div class="form__input__flex">
                                        <div class="form__input__single">
                                            <label for="text" class="form__input__single__label">{{ __('Username') }}</label>
                                            <input type="text" id="text" class="form__control radius-5" readonly value="{{auth()->user()->username}}">
                                        </div>
                                        <div class="form__input__single">
                                            <label for="name" class="form__input__single__label">{{ __('Name') }}<span>*</span></label>
                                            <input type="text" id="name" name="name" value="{{auth()->user()->name}}" class="form__control radius-5" placeholder="{{ __('Name') }}">
                                        </div>
                                        <div class="form__input__single">
                                            <label for="email" class="form__input__single__label">{{ __('Email') }}<span>*</span></label>
                                            <input type="email" id="email" name="email" value="{{auth()->user()->email}}" class="form__control radius-5" placeholder="{{ __('Email') }}">
                                        </div>

                                        <div class="form__input__single">
                                            <label for="about" class="form__input__single__label">{{ __('About') }}</label>
                                            <textarea type="text" id="about" name="about" value="{{auth()->user()->about}}"  class="form__control radius-5" cols="100" rows="3">  {{ auth()->user()->about }} </textarea>
                                        </div>

                                    </div>

                                <div class="form__input__single">
                                    @php $image_upload_btn_label = __('Upload Image'); @endphp
                                    <div class="media-upload-btn-wrapper">
                                        <div class="img-wrap">
                                            @php
                                                $profile_img = get_attachment_image_by_id(auth()->user()->image,null,true);
                                            @endphp
                                            @if (!empty($profile_img))
                                                <div class="attachment-preview">
                                                    <div class="thumbnail">
                                                        <div class="centered">
                                                            <img class="avatar user-thumb" src="{{$profile_img['img_url']}}" alt="{{auth()->user()->name}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                @php $image_upload_btn_label = __('Change Image'); @endphp
                                            @endif
                                        </div>
                                        <input type="hidden" name="image" value="{{auth()->user()->image}}">
                                        <button type="button" class="cmnBtn btn_5 btn_bg_secondary radius-5 media_upload_form_btn"
                                                data-btntitle="{{__('Select Profile Picture')}}"
                                                data-modaltitle="{{__('Upload Profile Picture')}}"
                                                data-imgid="{{auth()->user()->image}}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#media_upload_modal">
                                            {{__($image_upload_btn_label)}}
                                        </button>
                                    </div>
                                    <small class="info-text">{{__('Recommended Image Size 100x100. Only Accept: jpg,png,jpeg,webp. Size less than 2MB')}}</small>
                                </div>
                                    <div class="btn_wrapper mt-4">
                                        <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Save changes') }}</button>
                                    </div>
                                </form>
                            </div>
                       </div>
                    </div>
                </div>
             </div>
         </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
    <x-media.js/>
@endsection
