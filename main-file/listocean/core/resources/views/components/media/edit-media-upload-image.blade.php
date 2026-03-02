@php
    $size = $size ?? '1920x1280';
@endphp
<div class="form-group">
    <label for="og_meta_image">{{ $label ?? __('Image')}}</label>
    <div class="media-upload-btn-wrapper mb-2">
        <div class="img-wrap">
            {!! render_attachment_preview_for_admin($value ?? '') !!}
        </div>
        <input type="hidden" id="{{$name ?? 'image'}}" name="{{$name ?? 'image'}}"
               value="{{$value ?? ''}}">
        <button type="button" class="cmnBtn btn_5 btn_bg_info radius-5 media_upload_form_btn"
                data-btntitle="{{__('Select Image')}}"
                data-modaltitle="{{__('Upload Image')}}"
                data-bs-toggle="modal"
                data-bs-target="#media_upload_modal">
            {{'Change Image'}}
        </button>
    </div>
    <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png')}}</small><br>
    <small class="form-text text-muted">{{__('allowed image size :') . $size  }}</small>
</div>
