@extends('backend.admin-master')
@section('site-title')
    {{__('Maintain Page Settings')}}
@endsection
@section('style')
    <x-media.css/>
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-6 col-lg-6 mt-0">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('Maintain Page Settings')}}</h2>
                <x-validation.error/>
                <form action="{{route('admin.maintains.page.update.settings')}}" method="POST" class="validateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="title" class="form__input__single__label">{{ __('Title') }}</label>
                            <input type="text" class="form__control radius-5" name="maintain_page_title" id="maintain_page_title" value="{{get_static_option('maintain_page_title')}}" placeholder="{{__('Title')}}">
                        </div>
                        <div class="form__input__single">
                            <label for="maintain_page_description" class="form__input__single__label">{{ __('Description') }}</label>
                            <textarea class="form__control" name="maintain_page_description" id="maintain_page_description">{{get_static_option('maintain_page_description')}}</textarea>
                        </div>
                        <div class="form__input__single">
                            <label for="maintenance_duration" class="form__input__single__label">{{__('Maintenance Duration')}}</label>
                           <input type="date" class="form-control" id="maintenance_duration" value="{{get_static_option('maintenance_duration')}}" name="maintenance_duration" placeholder="{{__('Maintenance Duration')}}">
                        </div>

                        <div class="form__input__single">
                            <label for="og_meta_image" class="form__input__single__label">{{__('Logo')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap">
                                    @php
                                        $maintenance_image = get_attachment_image_by_id(get_static_option('maintain_page_logo'),null,true);
                                        $image_btn_label = __('Upload Image');
                                    @endphp
                                    @if (!empty($maintenance_image))
                                        <div class="attachment-preview">
                                            <div class="thumbnail">
                                                <div class="centered">
                                                    <img class="avatar user-thumb" src="{{$maintenance_image['img_url']}}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" id="maintain_page_logo" name="maintain_page_logo" value="{{get_static_option('maintain_page_logo')}}">
                                <button type="button" class="cmnBtn btn_5 btn_bg_secondary radius-5 media_upload_form_btn"
                                        data-btntitle="{{__('Select Image')}}"
                                        data-modaltitle="{{__('Upload Image')}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">{{__('Change Image')}}
                                </button>
                            </div>
                            <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png, Recommended image size 150x150')}}</small>
                        </div>


                        <!-- background img-->
                        <div class="form__input__single">
                            <label for="og_meta_image" class="form__input__single__label">{{__('Background Image')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap">
                                    @php
                                        $maintain_page_background_image = get_attachment_image_by_id(get_static_option('maintain_page_background_image'),null,true);
                                        $maintain_page_background_image_btn_label = __('Upload Image');
                                    @endphp
                                    @if (!empty($maintenance_image))
                                        <div class="attachment-preview">
                                            <div class="thumbnail">
                                                <div class="centered">
                                                    <img class="avatar user-thumb" src="{{$maintain_page_background_image['img_url']}}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" id="maintain_page_background_image" name="maintain_page_background_image" value="{{get_static_option('maintain_page_background_image')}}">
                                <button type="button" class="cmnBtn btn_5 btn_bg_secondary radius-5 media_upload_form_btn"
                                        data-btntitle="{{__('Select Image')}}"
                                        data-modaltitle="{{__('Upload Image')}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">
                                    {{__('Change Image')}}
                                </button>
                            </div>
                            <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png, Recommended image size 1920x600')}}</small>
                        </div>



                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('scripts')
    <x-media.js/>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                $("#maintenance_duration").flatpickr({
                    dateFormat: "Y-m-d",
                });
            });
        })(jQuery)
    </script>
@endsection
