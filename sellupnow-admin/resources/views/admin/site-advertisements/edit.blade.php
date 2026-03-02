@extends('layouts.app')
@section('header-title', __('Edit Advertisement'))
@section('content')
<div class="container-fluid my-4">
    <div class="col-xl-9 mx-auto">
        <form method="POST" action="{{ route('admin.siteAdvertisement.update', $advertisement->id) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-header py-3"><h5 class="card-title m-0">{{ __('Edit Advertisement') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $advertisement->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', $advertisement->status) == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="0" {{ old('status', $advertisement->status) == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Type') }} <span class="text-danger">*</span></label>
                            <select name="type" id="adType" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="image" {{ old('type', $advertisement->type) === 'image' ? 'selected' : '' }}>{{ __('Image / Banner') }}</option>
                                <option value="video" {{ old('type', $advertisement->type) === 'video' ? 'selected' : '' }}>{{ __('Video File') }}</option>
                                <option value="embed_code" {{ old('type', $advertisement->type) === 'embed_code' ? 'selected' : '' }}>{{ __('Embed Code') }}</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Size') }}</label>
                            <input type="text" name="size" class="form-control" value="{{ old('size', $advertisement->size) }}" placeholder="e.g. 300x250">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Slot') }}</label>
                            <input type="text" name="slot" id="adSlot" class="form-control"
                                   list="slotSuggestions"
                                   value="{{ old('slot', $advertisement->slot) }}"
                                   placeholder="{{ __('Select or type a slot key') }}"
                                   autocomplete="off">
                            <datalist id="slotSuggestions">
                                <option value="listing_video_slot">{{ __('Listing Page Video Slot — All Listings (global)') }}</option>
                                <option value="listing_details_left">{{ __('Listing Details — Left') }}</option>
                                <option value="listing_details_right">{{ __('Listing Details — Right') }}</option>
                                <option value="listing_details_under_gallery">{{ __('Listing Details — Under Gallery') }}</option>
                                <option value="listing_details_under_related">{{ __('Listing Details — Under Related Listings') }}</option>
                                <option value="listings_under_image">{{ __('Listings — Under Image') }}</option>
                                <option value="category_top_banner">{{ __('Category Pages — Top Banner') }}</option>
                                <option value="user_profile_between_tabs">{{ __('User Profile — Between Tabs &amp; Listings') }}</option>
                                <option value="user_profile_under_header">{{ __('User Profile — Under Header') }}</option>
                                <option value="marketplace_under_badges">{{ __('Marketplace — Under App Badges (Google Play / App Store)') }}</option>
                                <option value="sellupnow:homepage_after_hero">{{ __('Homepage — After Hero Section (full-width banner strip)') }}</option>
                                <option value="sellupnow:hero_search_left">{{ __('Homepage — Search Bar Left Mini-Ad') }}</option>
                                <option value="sellupnow:hero_search_right">{{ __('Homepage — Search Bar Right Mini-Ad') }}</option>
                            </datalist>
                            <div class="form-text text-muted">{{ __('For one specific listing page, type') }} <code>listing_video_<strong>42</strong></code> {{ __('(replace 42 with the listing ID).') }}</div>
                            <div class="form-text text-info" id="slotHint"></div>

                            {{-- Per-listing scope: shown for listing-page banner slots --}}
                            <div id="slotTargetListingWrap" class="mt-2" style="display:none">
                                <label class="form-label fw-semibold small mb-1">
                                    {{ __('Scope to Listing ID') }}
                                    <span class="text-muted fw-normal">({{ __('leave blank = all listings') }})</span>
                                </label>
                                <input type="number" name="listing_id_scope" id="slotTargetListingId"
                                       class="form-control form-control-sm" min="1"
                                       placeholder="{{ __('e.g. 42') }}"
                                       value="{{ old('listing_id_scope', $currentListingId) }}"
                                       style="max-width:160px">
                                <div class="form-text">{{ __('Enter a listing ID to restrict this ad to that one listing page only.') }}</div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">{{ __('Redirect URL') }}</label>
                            <input type="url" name="redirect_url" class="form-control" value="{{ old('redirect_url', $advertisement->redirect_url) }}">
                        </div>

                        <div class="col-12 mb-3" id="wrapVideo" style="display:none">
                            <label class="form-label fw-semibold">{{ __('Video') }}</label>

                            {{-- Current video (if already saved) --}}
                            @if($advertisement->type === 'video' && !empty($imageUrl))
                            <div class="mb-3 p-2 border rounded bg-light">
                                <div class="text-muted small mb-1">{{ __('Currently saved video:') }}</div>
                                <video src="{{ $imageUrl }}" controls style="max-width:100%;max-height:140px;border-radius:6px;background:#000"></video>
                            </div>
                            @endif

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
                                <small class="text-muted d-block mt-1">{{ __('Upload mp4, webm, ogg, mov — max 100 MB. Leave blank to keep current.') }}</small>
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
                                                 style="cursor:pointer{{ $advertisement->image === $pv->video_url ? ';outline:3px solid var(--bs-primary)' : '' }}"
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
                        </div>

                        <div class="col-12 mb-3" id="wrapImage" style="display:none">
                            {{-- Current image preview --}}
                            @if($imageUrl)
                            <div class="mb-2">
                                <img src="{{ $imageUrl }}" alt="{{ $advertisement->title }}" class="img-thumbnail" style="max-height:100px">
                            </div>
                            @endif

                            {{-- ── Upload or paste URL ─────────────────────────── --}}
                            <label class="form-label fw-semibold">{{ $imageUrl ? __('Replace Image') : __('Banner Image') }}</label>
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
                            <div id="imgPanelFile">
                                <div class="dropzone-container">
                                    <label for="adImageFile" style="cursor:pointer;display:block">
                                        <img id="adImagePreview"
                                             src="{{ $imageUrl ?: 'https://placehold.co/468x60/e9ecef/6c757d?text=Click+to+replace+image' }}"
                                             class="dropzone-area img-thumbnail"
                                             style="max-height:120px;object-fit:contain;width:100%">
                                    </label>
                                    <input type="file" id="adImageFile" name="image_file"
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           class="d-none @error('image_file') is-invalid @enderror"
                                           onchange="document.getElementById('adImagePreview').src = URL.createObjectURL(this.files[0])">
                                    @error('image_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <small class="text-muted">{{ __('Click the image to browse · or drag-and-drop · jpg, png, gif, webp — max 10 MB · leave blank to keep current') }}</small>
                            </div>
                            <div id="imgPanelUrl" style="display:none">
                                <input type="text" name="image" class="form-control @error('image') is-invalid @enderror"
                                    value="{{ old('image', $advertisement->image) }}"
                                    placeholder="https://example.com/banner.jpg">
                                <div class="form-text">{{ __('Paste a direct image URL. Leave blank to keep current.') }}</div>
                                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 mb-3" id="wrapEmbedCode" style="display:none">
                            <label class="form-label fw-semibold">{{ __('Embed Code') }}</label>
                            <textarea name="embed_code" class="form-control @error('embed_code') is-invalid @enderror" rows="5">{{ old('embed_code', $advertisement->embed_code) }}</textarea>
                            @error('embed_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.siteAdvertisement.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
var slotHintsEdit = {
    'listing_video_slot': '{{ __("Global fallback — shows on ALL listing detail pages that have no per-listing video assigned.") }}',
    'listing_details_left': '{{ __("Left sidebar on the listing detail page.") }}',
    'listing_details_right': '{{ __("Right sidebar on the listing detail page.") }}',
    'listing_details_under_gallery': '{{ __("Banner shown under the listing gallery.") }}',
    'listing_details_under_related': '{{ __("Banner shown below the related/relevant listings block on the detail page.") }}',
    'listings_under_image': '{{ __("Shown under images in listing cards.") }}',
    'category_top_banner': '{{ __("Full-width banner at the top of category, sub-category and child-category pages.") }}',
    'user_profile_between_tabs': '{{ __("Banner between the profile tab bar and the listings grid on user profile pages.") }}',
    'user_profile_under_header': '{{ __("Banner under the profile header.") }}',
    'marketplace_under_badges': '{{ __("Marketplace page — shows under the Google Play and App Store badges on the marketplace section.") }}',
    'sellupnow:homepage_after_hero': '{{ __("Full-width banner strip displayed directly below the homepage hero slideshow. Recommended size: 1400×200.") }}',
    'sellupnow:hero_search_left': '{{ __("Small 140×80 ad shown to the LEFT of the homepage search bar.") }}',
    'sellupnow:hero_search_right': '{{ __("Small 140×80 ad shown to the RIGHT of the homepage search bar.") }}',
};
var listingScopeCapableSlotsEdit = ['listing_details_left','listing_details_right','listing_details_under_gallery','listing_details_under_related','listing_video_slot'];
function getSlotHintEdit(val) {
    if (slotHintsEdit[val]) return slotHintsEdit[val];
    var m = val.match(/^listing_video_(\d+)$/);
    if (m) return '{{ __("Video shown ONLY on listing detail page #") }}' + m[1] + '{{ __(".") }}';
    return '';
}
function updateListingScopeVisibility(val) {
    var showScope = listingScopeCapableSlotsEdit.indexOf(val) !== -1;
    document.getElementById('slotTargetListingWrap').style.display = showScope ? '' : 'none';
}
function toggleAdFields() {
    var type = document.getElementById('adType').value;
    document.getElementById('wrapImage').style.display     = (type === 'image')      ? '' : 'none';
    document.getElementById('wrapVideo').style.display     = (type === 'video')      ? '' : 'none';
    document.getElementById('wrapEmbedCode').style.display = (type === 'embed_code') ? '' : 'none';
}
document.getElementById('adType').addEventListener('change', toggleAdFields);
if (document.getElementById('adSlot')) {
    document.getElementById('adSlot').addEventListener('input', function() {
        document.getElementById('slotHint').textContent = getSlotHintEdit(this.value.trim());
        updateListingScopeVisibility(this.value.trim());
    });
    // show hint immediately for current saved value
    document.getElementById('slotHint').textContent = getSlotHintEdit(document.getElementById('adSlot').value.trim());
    updateListingScopeVisibility(document.getElementById('adSlot').value.trim());
}
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
        clearLibrarySelection(); // file upload takes priority — drop any library pick
    } else {
        wrap.style.display = 'none';
        vid.src = '';
    }
}

function switchVideoTab(tab) {
    var uploadPanel  = document.getElementById('videoPanelUpload');
    var libraryPanel = document.getElementById('videoPanelLibrary');
    var btnUpload    = document.getElementById('tabBtnUpload');
    var btnLibrary   = document.getElementById('tabBtnLibrary');
    if (tab === 'upload') {
        uploadPanel.style.display  = '';
        libraryPanel.style.display = 'none';
        btnUpload.classList.add('active');
        btnLibrary.classList.remove('active');
        // clear library selection when switching away
        clearLibrarySelection();
    } else {
        uploadPanel.style.display  = 'none';
        libraryPanel.style.display = '';
        btnUpload.classList.remove('active');
        btnLibrary.classList.add('active');
        // clear file input when switching away
        var fi = document.getElementById('adVideoFile');
        if (fi) { fi.value = ''; }
        var wrap = document.getElementById('adVideoPreviewWrap');
        if (wrap) { wrap.style.display = 'none'; }
    }
}

function selectLibraryVideo(url, caption, cardEl) {
    document.getElementById('videoFromLibrary').value = url;
    var preview = document.getElementById('librarySelectedPreview');
    preview.src = url;
    document.getElementById('librarySelectedCaption').textContent = caption;
    document.getElementById('librarySelectedWrap').style.display = '';
    // highlight selected card
    document.querySelectorAll('.library-card-inner').forEach(function(c) {
        c.style.outline = '';
    });
    if (cardEl) { cardEl.style.outline = '3px solid var(--bs-primary)'; }
}

function clearLibrarySelection() {
    document.getElementById('videoFromLibrary').value = '';
    var preview = document.getElementById('librarySelectedPreview');
    if (preview) { preview.src = ''; }
    var wrap = document.getElementById('librarySelectedWrap');
    if (wrap) { wrap.style.display = 'none'; }
    document.querySelectorAll('.library-card-inner').forEach(function(c) {
        c.style.outline = '';
    });
}

function filterLibrary(text) {
    var q = text.toLowerCase().trim();
    document.querySelectorAll('#libraryGrid .library-card').forEach(function(card) {
        var caption = (card.dataset.caption || '').toLowerCase();
        card.style.display = (!q || caption.includes(q)) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
