<div class="row mt-3">
    <div class="col-xxl-12 col-lg-12">
        <div class="collapse_wrapper dashboard__card style_one bg__white padding-20 radius-10">
            <div class="collapse_wrapper__header">
                <h5 class="collapse_wrapper__header__title">{{ __('Meta Section') }}</h5>
            </div>
            <div class="tab_wrapper style_seven">
                <!--Tab Button  -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab8" role="tablist">
                        <a class="nav-link active" id="nav-21-tab"
                           data-bs-toggle="tab"
                           href="#blog_meta"
                           role="tab"
                           aria-controls="nav-21"
                           aria-selected="true">{{$sidebarHeading}}</a>
                        <a class="nav-link" id="nav-22-tab"
                           data-bs-toggle="tab"
                           href="#facebook_meta"
                           role="tab"
                           aria-controls="nav-22"
                           aria-selected="false">{{ __('Facebook Meta') }}</a>
                        <a class="nav-link" id="nav-23-tab"
                           data-bs-toggle="tab"
                           href="#twitter_meta"
                           role="tab"
                           aria-controls="nav-23"
                           aria-selected="false">{{ __('Twitter Meta') }}</a>
                    </div>
                </nav>
                <!--End-of Tab Button  -->
                <!-- Tab Contents -->
                <div class="tab-content" id="nav-tabContent8">
                    <div class="tab-pane fade show active" id="blog_meta" role="tabpanel" aria-labelledby="nav-21-tab">
                        <div class="form__input__flex mt-3">
                            <div class="form__input__single">
                                <label for="meta_title" class="form__input__single__label">{{__('Meta Title')}}</label>
                                <input type="text" class="form__control" name="meta_title" id="meta_title" placeholder="{{ __('Title') }}">
                            </div>
                            <div class="form__input__single">
                                <label for="meta_tags" class="form__input__single__label">{{__('Meta Tags')}}</label>
                                <input type="text" class="form__control" name="meta_tags" id="meta_tags" data-role="tagsinput" placeholder="{{ __('Tag') }}">
                            </div>
                            <div class="form__input__single">
                                <label for="meta_description" class="form__input__single__label">{{__('Meta Description')}}</label>
                                <textarea class="form__control" name="meta_description"  cols="20" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="facebook_meta" role="tabpanel" aria-labelledby="nav-22-tab">
                        <div class="form__input__single">
                            <label for="title" class="form__input__single__label">{{__('Facebook Meta Title')}}</label>
                            <input type="text" class="form__control" data-role="tagsinput" name="facebook_meta_tags">
                        </div>
                        <div class="row">
                            <div class="form__input__single col-md-12">
                                <label for="title" class="form__input__single__label">{{__('Facebook Meta Description')}}</label>
                                <textarea name="facebook_meta_description"  class="form__control max-height-140"  cols="20"  rows="4"></textarea>
                            </div>
                        </div>
                        <div class="form__input__single">
                            <label for="image" class="form__input__single__label">{{__('Facebook Meta Image')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap"></div>
                                <input type="hidden" name="facebook_meta_image">
                                <button type="button"
                                        class="cmnBtn btn_5 btn_bg_blue radius-5 media_upload_form_btn"
                                        data-btntitle="{{__('Select Image')}}"
                                        data-modaltitle="{{__('Upload Image')}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">
                                    {{__('Upload Image')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="twitter_meta" role="tabpanel" aria-labelledby="nav-22-tab">
                        <div class="form__input__single">
                            <label for="title" class="form__input__single__label">{{__('Twitter Meta Title')}}</label>
                            <input type="text" class="form__control" data-role="tagsinput"  name="twitter_meta_tags">
                        </div>
                        <div class="row">
                            <div class="form__input__single col-md-12">
                                <label for="title">{{__('Twitter Meta Description')}}</label>
                                <textarea name="twitter_meta_description" class="form__control max-height-140" cols="20" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="form__input__single">
                            <label for="image" class="form__input__single__label">{{__('Twitter Meta Image')}}</label>
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap"></div>
                                <input type="hidden" name="twitter_meta_image">
                                <button type="button"
                                        class="cmnBtn btn_5 btn_bg_blue radius-5 media_upload_form_btn"
                                        data-btntitle="{{__('Select Image')}}"
                                        data-modaltitle="{{__('Upload Image')}}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">
                                    {{__('Upload Image')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
