@extends('layouts.app')
@section('header-title', __('Add Advertisement'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-9 mx-auto">
        <form method="POST" action="{{ route('admin.siteAdvertisement.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Add Advertisement') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Type') }} <span class="text-danger">*</span></label>
                            <select name="type" id="adType" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>{{ __('Image / Banner') }}</option>
                                <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>{{ __('Video File') }}</option>
                                <option value="embed_code" {{ old('type') === 'embed_code' ? 'selected' : '' }}>{{ __('Embed Code') }}</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Size') }}</label>
                            <input type="text" name="size" class="form-control" value="{{ old('size') }}" placeholder="e.g. 300x250">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-semibold">{{ __('Placement Slot') }}</label>
                            <input type="hidden" name="slot" id="adSlot" value="{{ old('slot') }}">
                            <select id="adSlotSelect" class="form-select" onchange="onSlotChange(this.value)">
                                <option value="">— {{ __('No slot (manual)') }} —</option>
                                <optgroup label="🏠 {{ __('Homepage') }}">
                                    <option value="sellupnow:homepage_after_hero">{{ __('After Hero — Full Strip') }}</option>
                                    <option value="sellupnow:hero_search_left">{{ __('Search Bar — Left Ad') }}</option>
                                    <option value="sellupnow:hero_search_right">{{ __('Search Bar — Right Ad') }}</option>
                                </optgroup>
                                <optgroup label="📋 {{ __('Listing Pages') }}">
                                    <option value="listing_video_slot">{{ __('Video — Global Fallback') }}</option>
                                    <option value="listing_details_left">{{ __('Detail Page — Left') }}</option>
                                    <option value="listing_details_right">{{ __('Detail Page — Right') }}</option>
                                    <option value="listing_details_under_gallery">{{ __('Detail Page — Under Gallery') }}</option>
                                    <option value="listing_details_under_related">{{ __('Detail Page — Under Related Listings') }}</option>
                                    <option value="listings_under_image">{{ __('Listing Card — Under Image') }}</option>
                                    <option value="__custom_listing__">{{ __('Specific Listing (enter ID…)') }}</option>
                                </optgroup>
                                <optgroup label="🗂️ {{ __('Category Pages') }}">
                                    <option value="category_top_banner">{{ __('Category — Top Banner') }}</option>
                                </optgroup>
                                <optgroup label="👤 {{ __('User Profile') }}">
                                    <option value="user_profile_between_tabs">{{ __('Profile — Between Tabs &amp; Listings') }}</option>
                                    <option value="user_profile_under_header">{{ __('Profile — Under Header') }}</option>
                                </optgroup>
                                <optgroup label="🏪 {{ __('Marketplace') }}">
                                    <option value="marketplace_under_badges">{{ __('Under App Badges') }}</option>
                                </optgroup>
                            </select>

                            {{-- Per-listing ID input, shown only when "Specific Listing" selected --}}
                            <div id="slotListingIdWrap" class="input-group mt-2" style="display:none; max-width:260px;">
                                <span class="input-group-text text-muted small">listing_video_</span>
                                <input type="number" id="slotListingId" class="form-control" min="1"
                                       placeholder="{{ __('Listing ID') }}"
                                       oninput="document.getElementById('adSlot').value = this.value ? 'listing_video_'+this.value : ''">
                            </div>

                            {{-- Description panel --}}
                            <div id="slotDesc" class="mt-2 px-3 py-2 rounded border-start border-primary border-3 bg-light small text-secondary" style="display:none;"></div>

                            {{-- Per-listing scope: optional, shown for listing-page slots --}}
                            <div id="slotTargetListingWrap" class="mt-2" style="display:none">
                                <label class="form-label fw-semibold small mb-1">
                                    {{ __('Scope to Listing ID') }}
                                    <span class="text-muted fw-normal">({{ __('leave blank = all listings') }})</span>
                                </label>
                                <input type="number" name="listing_id_scope" id="slotTargetListingId"
                                       class="form-control form-control-sm" min="1"
                                       placeholder="{{ __('e.g. 42') }}"
                                       value="{{ old('listing_id_scope') }}"
                                       style="max-width:160px">
                                <div class="form-text">{{ __('Enter a listing ID to restrict this ad to that one listing page only.') }}</div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3" id="wrapRedirectUrl">
                            <label class="form-label fw-semibold">{{ __('Redirect URL') }}</label>
                            <input type="url" name="redirect_url" class="form-control" value="{{ old('redirect_url') }}">
                        </div>

                        <div class="col-12 mb-3" id="wrapVideo" style="display:none">
                            <label class="form-label fw-semibold">{{ __('Video') }}</label>
                            {{-- Source toggle tabs --}}
                            <ul class="nav nav-tabs mb-3" id="videoSourceTabs">
                                <li class="nav-item">
                                    <button class="nav-link active" type="button" id="tabBtnUpload" onclick="switchVideoTab('upload')">
                                        <i class="fa-solid fa-upload me-1"></i>{{ __('Upload New') }}
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button" id="tabBtnLibrary" onclick="switchVideoTab('library')">
                                        <i class="fa-solid fa-photo-film me-1"></i>{{ __('Choose from Library') }}
                                        <span class="badge bg-secondary ms-1">{{ count($promoVideos) }}</span>
                                    </button>
                                </li>
                            </ul>

                            {{-- Upload panel --}}
                            <div id="videoPanelUpload">
                                <input type="file" id="adVideoFile" name="video_file"
                                       accept="video/mp4,video/webm,video/ogg,video/quicktime,video/avi"
                                       class="form-control @error('video_file') is-invalid @enderror"
                                       onchange="previewAdVideo(this)">
                                @error('video_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                <div id="adVideoPreviewWrap" style="display:none;margin-top:10px">
                                    <video id="adVideoPreview" controls style="max-width:100%;max-height:200px;border-radius:6px"></video>
                                </div>
                                <small class="text-muted d-block mt-1">{{ __('Upload mp4, webm, ogg, mov — max 100 MB. When assigned to the Listing Video Slot, this is the ONLY video shown on listing detail pages.') }}</small>
                            </div>

                            {{-- Library panel --}}
                            <div id="videoPanelLibrary" style="display:none">
                                <input type="hidden" name="video_from_library" id="videoFromLibrary" value="">

                                {{-- Selected preview --}}
                                <div id="librarySelectedWrap" class="mb-3 p-3 border rounded bg-light" style="display:none">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <video id="librarySelectedPreview" src="" controls style="max-height:120px;max-width:240px;border-radius:6px;background:#000"></video>
                                        <div>
                                            <div class="fw-semibold mb-1" id="librarySelectedCaption">—</div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearLibrarySelection()">
                                                <i class="fa-solid fa-xmark me-1"></i>{{ __('Clear selection') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if(count($promoVideos))
                                    <div class="mb-2">
                                        <input type="text" id="librarySearch" class="form-control form-control-sm"
                                               placeholder="{{ __('Search by caption…') }}"
                                               oninput="filterLibrary(this.value)">
                                    </div>
                                    <div id="libraryGrid" class="row g-2" style="max-height:400px;overflow-y:auto">
                                        @foreach($promoVideos as $pv)
                                        <div class="col-6 col-md-3 library-card"
                                             data-url="{{ $pv->video_url }}"
                                             data-caption="{{ strtolower($pv->caption ?? '') }}">
                                            <div class="card h-100 library-card-inner"
                                                 style="cursor:pointer"
                                                 onclick="selectLibraryVideo('{{ $pv->video_url }}', '{{ addslashes($pv->caption ?? 'Promo #'.$pv->id) }}', this)">
                                                <div style="height:90px;background:#000;border-radius:4px 4px 0 0;overflow:hidden;position:relative">
                                                    @if($pv->thumbnail_url)
                                                        <img src="{{ $pv->thumbnail_url }}" style="width:100%;height:100%;object-fit:cover">
                                                    @else
                                                        <div style="display:flex;align-items:center;justify-content:center;height:100%">
                                                            <i class="fa-solid fa-film fa-2x text-white opacity-50"></i>
                                                        </div>
                                                    @endif
                                                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.25)">
                                                        <i class="fa-solid fa-circle-play fa-2x text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="card-body p-2">
                                                    <small class="text-truncate d-block text-muted">{{ $pv->caption ?: 'Promo #'.$pv->id }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="fa-solid fa-video-slash fa-2x mb-2 d-block"></i>
                                        {{ __('No approved promo videos in the library yet.') }}<br>
                                        <a href="{{ route('admin.promoVideoAds.index') }}" class="small" target="_blank">{{ __('Go to Promo Video Ads →') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>{{-- /wrapVideo --}}

                        <div class="col-12 mb-3" id="wrapImage" style="display:none">
                            {{-- ── Upload or paste URL ─────────────────────────── --}}
                            <label class="form-label fw-semibold">{{ __('Banner Image') }}</label>
                            <div class="d-flex gap-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="_img_src" id="imgSrcFile" value="file" checked>
                                    <label class="form-check-label" for="imgSrcFile">{{ __('Upload from computer') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="_img_src" id="imgSrcUrl" value="url">
                                    <label class="form-check-label" for="imgSrcUrl">{{ __('Paste URL') }}</label>
                                </div>
                            </div>

                            {{-- File upload --}}
                            <div id="imgPanelFile">
                                <div class="dropzone-container">
                                    <label for="adImageFile" style="cursor:pointer;display:block">
                                        <img id="adImagePreview"
                                             src="https://placehold.co/468x60/e9ecef/6c757d?text=Click+to+pick+banner+image"
                                             class="dropzone-area img-thumbnail"
                                             style="max-height:120px;object-fit:contain;width:100%">
                                    </label>
                                    <input type="file" id="adImageFile" name="image_file"
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           class="d-none @error('image_file') is-invalid @enderror"
                                           onchange="document.getElementById('adImagePreview').src = URL.createObjectURL(this.files[0])">
                                    @error('image_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <small class="text-muted">{{ __('Click the image area to browse · or drag-and-drop · jpg, png, gif, webp — max 10 MB') }}</small>
                            </div>

                            {{-- URL paste --}}
                            <div id="imgPanelUrl" style="display:none">
                                <input type="text" name="image" class="form-control @error('image') is-invalid @enderror"
                                    value="{{ old('image') }}"
                                    placeholder="https://example.com/banner.jpg">
                                <div class="form-text">{{ __('Paste a direct image URL.') }}</div>
                                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3" id="wrapEmbedCode" style="display:none">
                            <label class="form-label fw-semibold">{{ __('Embed Code') }}</label>
                            <textarea name="embed_code" class="form-control @error('embed_code') is-invalid @enderror" rows="5">{{ old('embed_code') }}</textarea>
                            @error('embed_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.siteAdvertisement.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
var slotHints = {
    '': '',
    'listing_video_slot': '{{ __("Global fallback video — displays on every listing detail page that has no per-listing video assigned.") }}',
    'listing_details_left': '{{ __("Left sidebar on the listing detail page.") }}',
    'listing_details_right': '{{ __("Right sidebar on the listing detail page.") }}',
    'listing_details_under_gallery': '{{ __("Banner shown directly under the listing image gallery.") }}',
    'listing_details_under_related': '{{ __("Banner shown below the related/relevant listings block on the detail page.") }}',
    'listings_under_image': '{{ __("Shown under images in listing cards on search/browse pages.") }}',
    'category_top_banner': '{{ __("Full-width banner at the top of category, sub-category and child-category pages.") }}',
    'user_profile_between_tabs': '{{ __("Banner between the profile tab bar and the listings grid on user profile pages.") }}',
    'user_profile_under_header': '{{ __("Banner strip under the profile cover/header area.") }}',
    'marketplace_under_badges': '{{ __("Shown under the Google Play and App Store badges on the marketplace page.") }}',
    'sellupnow:homepage_after_hero': '{{ __("Full-width strip directly below the homepage hero slider. Recommended image size: 1400 × 200 px.") }}',
    'sellupnow:hero_search_left': '{{ __("Small banner to the LEFT of the homepage search bar. Recommended image size: 200 × 90 px.") }}',
    'sellupnow:hero_search_right': '{{ __("Small banner to the RIGHT of the homepage search bar. Recommended image size: 200 × 90 px.") }}',
};

var listingScopeCapableSlots = ['listing_details_left','listing_details_right','listing_details_under_gallery','listing_details_under_related','listing_video_slot'];

function onSlotChange(val) {
    var isCustom = (val === '__custom_listing__');
    document.getElementById('slotListingIdWrap').style.display = isCustom ? '' : 'none';
    if (!isCustom) {
        document.getElementById('adSlot').value = val;
        document.getElementById('slotListingId') && (document.getElementById('slotListingId').value = '');
    } else {
        document.getElementById('adSlot').value = '';
    }
    var desc = document.getElementById('slotDesc');
    var hint = (!isCustom && slotHints[val]) ? slotHints[val] : (isCustom ? '{{ __("Enter the listing ID below — the ad will show only on that specific listing detail page.") }}' : '');
    if (hint) { desc.textContent = hint; desc.style.display = ''; }
    else { desc.style.display = 'none'; }
    // Show listing scope field only for listing-page-type slots
    var showScope = listingScopeCapableSlots.indexOf(val) !== -1;
    document.getElementById('slotTargetListingWrap').style.display = showScope ? '' : 'none';
    if (!showScope) {
        var scopeInput = document.getElementById('slotTargetListingId');
        if (scopeInput) scopeInput.value = '';
    }
}

// Restore select state from old() value on page load
(function() {
    var saved = document.getElementById('adSlot').value;
    if (!saved) return;
    var sel = document.getElementById('adSlotSelect');
    var m = saved.match(/^listing_video_(\d+)$/);
    if (m) {
        sel.value = '__custom_listing__';
        document.getElementById('slotListingIdWrap').style.display = '';
        document.getElementById('slotListingId').value = m[1];
        var desc = document.getElementById('slotDesc');
        desc.textContent = '{{ __("Enter the listing ID below — the ad will show only on that specific listing detail page.") }}';
        desc.style.display = '';
    } else {
        sel.value = saved;
        onSlotChange(saved);
    }
})();

function toggleAdFields() {
    var type = document.getElementById('adType').value;
    document.getElementById('wrapImage').style.display     = (type === 'image')      ? '' : 'none';
    document.getElementById('wrapVideo').style.display     = (type === 'video')      ? '' : 'none';
    document.getElementById('wrapEmbedCode').style.display = (type === 'embed_code') ? '' : 'none';
}
document.getElementById('adType').addEventListener('change', toggleAdFields);
toggleAdFields();

document.querySelectorAll('input[name="_img_src"]').forEach(function(r) {
    r.addEventListener('change', function() {
        document.getElementById('imgPanelFile').style.display = this.value === 'file' ? '' : 'none';
        document.getElementById('imgPanelUrl').style.display  = this.value === 'url'  ? '' : 'none';
    });
});

function previewAdVideo(input) {
    var wrap = document.getElementById('adVideoPreviewWrap');
    var vid  = document.getElementById('adVideoPreview');
    if (input.files && input.files[0]) {
        vid.src = URL.createObjectURL(input.files[0]);
        wrap.style.display = '';
        // Clear any library selection when a file is chosen
        clearLibrarySelection();
    } else {
        wrap.style.display = 'none';
        vid.src = '';
    }
}

function switchVideoTab(tab) {
    var isUpload  = tab === 'upload';
    document.getElementById('videoPanelUpload').style.display  = isUpload ? '' : 'none';
    document.getElementById('videoPanelLibrary').style.display = isUpload ? 'none' : '';
    document.getElementById('tabBtnUpload').classList.toggle('active', isUpload);
    document.getElementById('tabBtnLibrary').classList.toggle('active', !isUpload);
    // Reset opposing input
    if (isUpload) {
        clearLibrarySelection();
    } else {
        var fileInput = document.getElementById('adVideoFile');
        if (fileInput) fileInput.value = '';
        document.getElementById('adVideoPreviewWrap').style.display = 'none';
    }
}

function selectLibraryVideo(url, caption, cardEl) {
    document.getElementById('videoFromLibrary').value = url;
    var preview = document.getElementById('librarySelectedPreview');
    preview.src = url;
    document.getElementById('librarySelectedCaption').textContent = caption;
    document.getElementById('librarySelectedWrap').style.display = '';
    // Highlight selected card, de-highlight others
    document.querySelectorAll('.library-card-inner').forEach(function(c) {
        c.classList.remove('border-primary', 'border-2');
        c.style.outline = '';
    });
    if (cardEl) {
        cardEl.style.outline = '3px solid var(--bs-primary)';
    }
}

function clearLibrarySelection() {
    document.getElementById('videoFromLibrary').value = '';
    var preview = document.getElementById('librarySelectedPreview');
    if (preview) { preview.src = ''; }
    var wrap = document.getElementById('librarySelectedWrap');
    if (wrap) wrap.style.display = 'none';
    document.querySelectorAll('.library-card-inner').forEach(function(c) {
        c.style.outline = '';
    });
}

function filterLibrary(text) {
    var q = text.toLowerCase();
    document.querySelectorAll('#libraryGrid .library-card').forEach(function(el) {
        var cap = el.getAttribute('data-caption') || '';
        el.style.display = cap.includes(q) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
