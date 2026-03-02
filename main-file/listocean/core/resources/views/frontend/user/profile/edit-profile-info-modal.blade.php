<!-- Modal -->
<div class="modal fade" id="userProfileEditModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('Background Image') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_profile_form" method="post">
                @csrf
                <input type="hidden" name="profile_bg_image_request" id="profile_bg_image_request" value="1">
                <div class="modal-body p-3">
                    <div class="popup-contents-form custom-form profile-border-top">
                    <div class="error_msg_container"></div>
                    <div class="media-upload-btn-wrapper">
                        <div class="single-flex-input">
                            <div class="img-wrap">
                                {!! render_image_markup_by_attachment_id(Auth::guard('web')->user()->profile_background) !!}
                            </div>
                            <input type="hidden" id="profile_background" name="profile_background"
                                   value="{{Auth::guard('web')->user()->profile_background}}">
                            <button type="button" class="btn btn-info media_upload_form_btn"
                                    data-btntitle="{{__('Select Image')}}"
                                    data-modaltitle="{{__('Upload Background Image')}}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#media_upload_modal">
                                {{__('Upload Image')}}
                            </button>
                         </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-wrapper d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="cmn-btn1 popup-modal"> {{ __('Update') }} </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
