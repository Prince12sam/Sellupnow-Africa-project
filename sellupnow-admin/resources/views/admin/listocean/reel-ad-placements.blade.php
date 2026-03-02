@extends('layouts.app')

@section('header-title', __('Reel Ad Placements'))
@section('header-subtitle', __('Target specific reels with an overlay advertisement (Listocean customer web)'))

@section('content')
<div class="page-title">
    <div class="d-flex gap-2 align-items-center">
        <i class="fa-solid fa-rectangle-ad"></i> {{ __('Reel Ad Placements') }}
    </div>
</div>

{{-- ── FORM CARD ──────────────────────────────────────────────────────── --}}
<div class="card mt-3">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-2">
            @if($editing)
                <span class="badge bg-warning text-dark"><i class="fa-solid fa-pen-to-square me-1"></i>{{ __('Editing Placement #') . (int) $editing->id }}</span>
            @else
                <span class="badge bg-primary"><i class="fa-solid fa-plus me-1"></i>{{ __('New Placement') }}</span>
            @endif
        </div>
        @if($editing)
            <a href="{{ route('admin.reelAdPlacement.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-xmark me-1"></i>{{ __('Cancel Edit') }}
            </a>
        @endif
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $editing
                  ? route('admin.reelAdPlacement.update', ['id' => (int) $editing->id])
                  : route('admin.reelAdPlacement.store') }}">
            @csrf

            {{-- ── How it works info box ───────────────────────────────────── --}}
            <div class="alert alert-info border-0 py-2 px-3 small mb-4 d-flex gap-2 align-items-start">
                <i class="fa-solid fa-circle-info mt-1 flex-shrink-0"></i>
                <div>
                    <strong>How multiple ads on one video work:</strong>
                    Each row you create is one ad assignment. If you assign <strong>Slot A</strong> and <strong>Slot B</strong> to the same reel ID, both ads will automatically rotate every <strong>5 seconds</strong> while the video plays — exactly like Facebook Reels. You can add as many rows as you need for the same reel.
                    <br>
                    <strong>Placement guide:</strong>
                    <em>Reel Feed Slots</em> show on <code>/reels</code> (the scrollable feed) &bull;
                    <em>Watch Page</em> shows on <code>/reels/{id}</code> (single video view).
                </div>
            </div>

            {{-- ── Section 1: Target Reel ───────────────────────────────────── --}}
            <div class="mb-4">
                <p class="fw-semibold text-muted small text-uppercase mb-2 border-bottom pb-1">
                    <i class="fa-solid fa-film me-1"></i>{{ __('1. Target Reel') }}
                </p>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">{{ __('Reel Type') }}</label>
                        @php
                            $reelTypeLabels = [
                                'listing'  => 'Listing Video',
                                'ad_video' => 'Promo Ad Video',
                            ];
                            $reelTypeIcons = [
                                'listing'  => 'fa-tag',
                                'ad_video' => 'fa-bullhorn',
                            ];
                            $reelTypeHelp = [
                                'listing'  => '<strong>Listing Video</strong> — only works if the listing itself has a video URL (admin-uploaded via the listing form). Enter the <strong>Listing ID</strong>. <a href="' . route('admin.listingModeration.index') . '" target="_blank">Browse listings →</a><br><span class="text-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i>If the video was uploaded by the <em>user</em> via the Video Upload form, use <strong>Promo Ad Video</strong> type instead and enter the <strong>Ad Video ID</strong>.</span>',
                                'ad_video' => '<strong>Promo Ad Video</strong> — for user-uploaded videos (from the Video Upload form) <em>and</em> sponsored promos. Enter the <strong>Ad Video ID</strong> (found in <a href="' . route('admin.promoVideoAds.index') . '" target="_blank">Promo Videos →</a>).',
                            ];
                        @endphp
                        <select name="reel_type" id="reel_type" class="form-select">
                            @foreach($allowedReelTypes as $t)
                                <option value="{{ $t }}"
                                    data-help="{{ $reelTypeHelp[$t] ?? '' }}"
                                    {{ old('reel_type', $editing->reel_type ?? 'listing') === $t ? 'selected' : '' }}>
                                    {{ $reelTypeLabels[$t] ?? $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold" for="reel_id">{{ __('Reel ID') }}</label>
                        <input type="number" min="1" name="reel_id" id="reel_id"
                               class="form-control @error('reel_id') is-invalid @enderror"
                               value="{{ old('reel_id', $editing->reel_id ?? '') }}" required
                               placeholder="{{ __('e.g. 4') }}">
                        @error('reel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted small mt-1">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Use <strong>Promo Ad Video</strong> type for user-uploaded videos.
                            The Ad Video ID is shown in the <a href="{{ route('admin.promoVideoAds.index') }}" target="_blank">Promo Videos</a> list.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('Placement Slot') }}</label>
                        @php
                            $placementLabels = [
                                'bottom_overlay'       => 'Reel Feed — Ad Slot A  (rotates with Slot B)',
                                'bottom_overlay_2'     => 'Reel Feed — Ad Slot B  (rotates with Slot A)',
                                'listing_detail_video' => 'Watch Page — Video Section  (/reels/ID)',
                            ];
                            $placementIcons = [
                                'bottom_overlay'       => 'fa-layer-group',
                                'bottom_overlay_2'     => 'fa-layer-group',
                                'listing_detail_video' => 'fa-play-circle',
                            ];
                            $placementColors = [
                                'bottom_overlay'       => 'text-primary',
                                'bottom_overlay_2'     => 'text-info',
                                'listing_detail_video' => 'text-success',
                            ];
                        @endphp
                        <select name="placement" id="placement" class="form-select">
                            @foreach($allowedPlacements as $pl)
                                <option value="{{ $pl }}"
                                    {{ old('placement', $editing->placement ?? 'bottom_overlay') === $pl ? 'selected' : '' }}>
                                    {{ $placementLabels[$pl] ?? $pl }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dynamic helper text driven by reel_type selection --}}
                    <div class="col-12">
                        <div id="reelTypeHelp" class="text-muted small ps-1"></div>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Advertisement ─────────────────────────────────── --}}
            <div class="mb-4">
                <p class="fw-semibold text-muted small text-uppercase mb-2 border-bottom pb-1">
                    <i class="fa-solid fa-image me-1"></i>{{ __('2. Advertisement') }}
                </p>

                @if($ads->isEmpty())
                    <div class="alert alert-warning d-flex align-items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation fa-lg flex-shrink-0"></i>
                        <div>
                            {{ __('No advertisements found.') }}
                            <a href="{{ route('admin.siteAdvertisement.create') }}" class="alert-link ms-1">
                                {{ __('Create one first') }} →
                            </a>
                        </div>
                    </div>
                    <input type="hidden" name="advertisement_id" value="">
                @else
                    <div class="row g-3 align-items-start">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="advertisement_id">
                                {{ __('Choose Advertisement') }}
                                <span class="badge bg-secondary ms-1">{{ $ads->count() }} {{ __('available') }}</span>
                            </label>
                            <select name="advertisement_id" id="advertisement_id" class="form-select form-select-lg" required>
                                <option value="" disabled {{ old('advertisement_id', $editing->advertisement_id ?? '') === '' ? 'selected' : '' }}>
                                    — {{ __('Select an advertisement') }} —
                                </option>
                                @foreach($ads as $ad)
                                    @php $isActive = (int) ($ad->status ?? 0) === 1; @endphp
                                    <option value="{{ (int) $ad->id }}"
                                        {{ (int) old('advertisement_id', $editing->advertisement_id ?? 0) === (int) $ad->id ? 'selected' : '' }}>
                                        #{{ (int) $ad->id }} — {{ $ad->title ?: '(no title)' }}
                                        [{{ strtoupper($ad->type ?? '?') }}]{{ $isActive ? '' : ' ✗ Disabled' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('Only active ads will display on the frontend.') }}</div>
                        </div>

                        {{-- Live Ad Preview Panel --}}
                        <div class="col-md-6">
                            <div id="adPreviewPanel" class="border rounded p-0 bg-light overflow-hidden" style="display:none; min-height: 120px;">
                                <div class="d-flex align-items-stretch" style="min-height:120px;">
                                    <div id="adPreviewThumb" class="flex-shrink-0 bg-secondary d-flex align-items-center justify-content-center"
                                         style="width:130px; min-height:120px; background:#e9ecef;">
                                        <i class="fa-solid fa-image fa-2x text-muted"></i>
                                    </div>
                                    <div class="p-3 flex-grow-1 overflow-hidden">
                                        <div class="d-flex gap-2 mb-1 flex-wrap">
                                            <span id="adPreviewType" class="badge bg-primary"></span>
                                            <span id="adPreviewStatus" class="badge"></span>
                                            <span id="adPreviewSize" class="badge bg-light text-dark border small"></span>
                                        </div>
                                        <div id="adPreviewTitle" class="fw-semibold text-truncate mb-1" style="max-width:260px;"></div>
                                        <div id="adPreviewDesc" class="small text-muted mb-2" style="max-width:260px; white-space:pre-wrap; word-break:break-word; max-height:40px; overflow:hidden;"></div>
                                        <div id="adPreviewUrl" class="small text-break"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="adPreviewEmpty" class="border rounded p-3 bg-light text-center text-muted small" style="min-height:120px; display:flex; align-items:center; justify-content:center;">
                                <span><i class="fa-solid fa-arrow-pointer me-1"></i>{{ __('Select an ad above to preview it here') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── Section 3: Schedule ──────────────────────────────────────── --}}
            <div class="mb-4">
                <p class="fw-semibold text-muted small text-uppercase mb-2 border-bottom pb-1">
                    <i class="fa-solid fa-calendar-days me-1"></i>{{ __('3. Schedule') }}
                </p>

                <div class="row g-3 align-items-start">
                    {{-- Start At --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="start_at">{{ __('Start Date & Time') }}</label>
                        <input type="datetime-local" name="start_at" id="start_at"
                               class="form-control"
                               value="{{ old('start_at', !empty($editing->start_at)
                                   ? \Carbon\Carbon::parse($editing->start_at)->format('Y-m-d\TH:i')
                                   : '') }}">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="start_no_limit"
                                   {{ old('start_at', $editing->start_at ?? null) === null && !$editing ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="start_no_limit">
                                {{ __('No start restriction (run immediately)') }}
                            </label>
                        </div>
                    </div>

                    {{-- End At --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="end_at">{{ __('End Date & Time') }}</label>
                        <input type="datetime-local" name="end_at" id="end_at"
                               class="form-control"
                               value="{{ old('end_at', !empty($editing->end_at)
                                   ? \Carbon\Carbon::parse($editing->end_at)->format('Y-m-d\TH:i')
                                   : '') }}">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="end_no_limit"
                                   {{ old('end_at', $editing->end_at ?? null) === null && !$editing ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="end_no_limit">
                                {{ __('No end date (run indefinitely)') }}
                            </label>
                        </div>
                    </div>

                    {{-- Quick Duration --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('Quick Duration') }}</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-dur" data-days="7">7 days</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-dur" data-days="14">14 days</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-dur" data-days="30">30 days</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-dur" data-days="60">60 days</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-dur" data-days="90">90 days</button>
                        </div>
                        <div class="form-text">{{ __('Sets end date = start date + N days. Start date defaults to now if blank.') }}</div>
                    </div>
                </div>
            </div>

            {{-- ── Section 4: Status + Submit ──────────────────────────────── --}}
            <div class="d-flex align-items-center gap-4 pt-2 border-top">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                           name="status" id="rap_status" value="1"
                           {{ old('status', (int) ($editing->status ?? 1)) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="rap_status">
                        {{ __('Placement Enabled') }}
                    </label>
                </div>

                <div class="ms-auto d-flex gap-2">
                    @if($editing)
                        <a href="{{ route('admin.reelAdPlacement.index') }}" class="btn btn-outline-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-warning text-white"
                                {{ $ads->isEmpty() ? 'disabled' : '' }}>
                            <i class="fa-solid fa-floppy-disk me-1"></i>{{ __('Save Changes') }}
                        </button>
                    @else
                        <button type="submit" class="btn btn-primary"
                                {{ $ads->isEmpty() ? 'disabled' : '' }}>
                            <i class="fa-solid fa-plus me-1"></i>{{ __('Create Placement') }}
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── PLACEMENTS TABLE ───────────────────────────────────────────────── --}}
<div class="card mt-4">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0"><i class="fa-solid fa-list me-2"></i>{{ __('Existing Placements') }}</h5>
        <span class="badge bg-secondary">{{ $placements->total() }} {{ __('total') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:48px;">{{ __('ID') }}</th>
                        <th>{{ __('Target Reel') }}</th>
                        <th>{{ __('Slot') }}</th>
                        <th>{{ __('Advertisement') }}</th>
                        <th>{{ __('Schedule') }}</th>
                        <th style="width:90px;">{{ __('Status') }}</th>
                        <th class="text-end pe-3" style="width:120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($placements as $p)
                    @php
                        $rid    = (int) ($p->reel_id ?? 0);
                        $rtype  = (string) ($p->reel_type ?? '');
                        $label  = $rtype === 'listing'
                            ? ($listingTitlesById[$rid] ?? null)
                            : ($rtype === 'ad_video' ? ($promoCaptionsById[$rid] ?? null) : null);

                        $ad = $adsById[(int) ($p->advertisement_id ?? 0)] ?? null;

                        // Schedule status computation
                        $now = \Carbon\Carbon::now();
                        $startAt = !empty($p->start_at) ? \Carbon\Carbon::parse($p->start_at) : null;
                        $endAt   = !empty($p->end_at)   ? \Carbon\Carbon::parse($p->end_at)   : null;
                        $enabled = (int) ($p->status ?? 0) === 1;

                        if (!$enabled) {
                            $schedBadge = ['bg-secondary', 'fa-pause', 'Disabled'];
                        } elseif ($endAt && $endAt->isPast()) {
                            $schedBadge = ['bg-danger', 'fa-hourglass-end', 'Expired'];
                        } elseif ($startAt && $startAt->isFuture()) {
                            $schedBadge = ['bg-info text-dark', 'fa-clock', 'Scheduled'];
                        } elseif (!$startAt && !$endAt) {
                            $schedBadge = ['bg-success', 'fa-infinity', 'Always On'];
                        } else {
                            $schedBadge = ['bg-success', 'fa-circle-play', 'Active'];
                        }

                        $placementShort = [
                            'bottom_overlay'       => 'Feed — Slot A',
                            'bottom_overlay_2'     => 'Feed — Slot B',
                            'listing_detail_video' => 'Watch Page',
                        ][$p->placement ?? ''] ?? ($p->placement ?? '—');

                        $placementColor = [
                            'bottom_overlay'       => 'bg-primary',
                            'bottom_overlay_2'     => 'bg-info text-dark',
                            'listing_detail_video' => 'bg-success',
                        ][$p->placement ?? ''] ?? 'bg-secondary';
                    @endphp
                    <tr>
                        <td class="ps-3 text-muted small">#{{ (int) $p->id }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border small text-uppercase">{{ $rtype }}</span>
                                <span class="fw-semibold">#{{ $rid }}</span>
                            </div>
                            @if($label)
                                <div class="small text-muted text-truncate mt-1" style="max-width:280px;" title="{{ $label }}">{{ $label }}</div>
                            @endif
                        </td>

                        <td>
                            <span class="badge {{ $placementColor }}">{{ $placementShort }}</span>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($ad && !empty($ad->image_url))
                                    <img src="{{ $ad->image_url }}" alt=""
                                         class="rounded border flex-shrink-0"
                                         style="width:48px; height:36px; object-fit:cover;"
                                         onerror="this.style.display='none'">
                                @else
                                    <div class="rounded border bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:48px; height:36px;">
                                        <i class="fa-regular fa-image text-muted small"></i>
                                    </div>
                                @endif
                                <div class="overflow-hidden">
                                    <div class="fw-semibold text-truncate" style="max-width:200px;">
                                        #{{ (int) ($p->advertisement_id ?? 0) }}{{ $ad ? ' — ' . $ad->title : '' }}
                                    </div>
                                    @if($ad)
                                        <div class="small text-muted">
                                            {{ strtoupper($ad->type ?? '') }}
                                            @if(!empty($ad->redirect_url))
                                                · <a href="{{ $ad->redirect_url }}" target="_blank" class="text-muted text-truncate" style="max-width:140px; display:inline-block; vertical-align:bottom;">{{ $ad->redirect_url }}</a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="small">
                            <span class="badge {{ $schedBadge[0] }} mb-1">
                                <i class="fa-solid {{ $schedBadge[1] }} me-1"></i>{{ __($schedBadge[2]) }}
                            </span>
                            <div class="text-muted" style="line-height:1.4;">
                                <div>
                                    <i class="fa-regular fa-calendar-check text-muted" style="width:14px;"></i>
                                    {{ $startAt ? $startAt->format('d M Y, H:i') : __('Any time') }}
                                </div>
                                <div>
                                    <i class="fa-regular fa-calendar-xmark text-muted" style="width:14px;"></i>
                                    {{ $endAt ? $endAt->format('d M Y, H:i') : __('No end') }}
                                </div>
                            </div>
                        </td>

                        <td>
                            @if($enabled)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="fa-solid fa-check me-1"></i>{{ __('On') }}
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <i class="fa-solid fa-xmark me-1"></i>{{ __('Off') }}
                                </span>
                            @endif
                        </td>

                        <td class="text-end pe-3">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.reelAdPlacement.index', ['edit' => (int) $p->id]) }}"
                                   class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.reelAdPlacement.destroy', ['id' => (int) $p->id]) }}"
                                      class="d-inline" id="deletePlacementForm{{ (int) $p->id }}">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeletePlacement({{ (int) $p->id }})"
                                            title="{{ __('Delete') }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-rectangle-ad fa-2x mb-2 d-block opacity-25"></i>
                            {{ __('No placements configured yet. Use the form above to create one.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($placements->hasPages())
            <div class="d-flex justify-content-end p-3 border-top">
                {{ $placements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── Ad preview data passed from controller ──
    const adData = @json($adPreviewData ?? []);

    // ── Reel type helper text ────────────────────────────────────────────
    const reelTypeSelect = document.getElementById('reel_type');
    const reelHelpEl     = document.getElementById('reelTypeHelp');

    function updateReelHelp() {
        if (!reelTypeSelect || !reelHelpEl) return;
        const selected = reelTypeSelect.options[reelTypeSelect.selectedIndex];
        reelHelpEl.innerHTML = selected ? (selected.dataset.help || '') : '';
    }

    if (reelTypeSelect) {
        reelTypeSelect.addEventListener('change', updateReelHelp);
        updateReelHelp();
    }

    // ── Ad preview panel ─────────────────────────────────────────────────
    const adSelect      = document.getElementById('advertisement_id');
    const previewPanel  = document.getElementById('adPreviewPanel');
    const previewEmpty  = document.getElementById('adPreviewEmpty');
    const previewThumb  = document.getElementById('adPreviewThumb');
    const previewType   = document.getElementById('adPreviewType');
    const previewStatus = document.getElementById('adPreviewStatus');
    const previewSize   = document.getElementById('adPreviewSize');
    const previewTitle  = document.getElementById('adPreviewTitle');
    const previewDesc   = document.getElementById('adPreviewDesc');
    const previewUrl    = document.getElementById('adPreviewUrl');

    function updateAdPreview() {
        if (!adSelect) return;
        const id  = parseInt(adSelect.value, 10);
        const ad  = id ? adData[id] : null;

        if (!ad) {
            if (previewPanel) previewPanel.style.display = 'none';
            if (previewEmpty) previewEmpty.style.display = '';
            return;
        }

        if (previewEmpty) previewEmpty.style.display = 'none';
        if (previewPanel) previewPanel.style.display = '';

        // Thumbnail
        if (previewThumb) {
            if (ad.image_url) {
                previewThumb.innerHTML =
                    `<img src="${ad.image_url}" alt=""
                          style="width:130px; min-height:120px; object-fit:cover; display:block;"
                          onerror="this.parentElement.innerHTML='<i class=\\'fa-solid fa-image fa-2x text-muted\\'></i>'">`;
            } else {
                previewThumb.innerHTML = '<i class="fa-solid fa-image fa-2x text-muted"></i>';
            }
        }

        if (previewType)   previewType.textContent  = (ad.type || '').toUpperCase();
        if (previewSize)   previewSize.textContent  = ad.size  || '';
        if (previewSize)   previewSize.style.display = ad.size ? '' : 'none';
        if (previewTitle)  previewTitle.textContent = ad.title || '(no title)';
        if (previewDesc)   previewDesc.textContent  = ad.description || '';
        if (previewDesc)   previewDesc.style.display = ad.description ? '' : 'none';

        if (previewStatus) {
            previewStatus.textContent = ad.status === 1 ? '✓ Active' : '✗ Disabled';
            previewStatus.className   = 'badge ' + (ad.status === 1 ? 'bg-success' : 'bg-secondary');
        }

        if (previewUrl) {
            if (ad.redirect_url) {
                previewUrl.innerHTML = `<a href="${ad.redirect_url}" target="_blank" rel="noopener"
                    class="text-muted small text-break">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>${ad.redirect_url}</a>`;
            } else {
                previewUrl.innerHTML = '<span class="text-muted small">No redirect URL set</span>';
            }
        }
    }

    if (adSelect) {
        adSelect.addEventListener('change', updateAdPreview);
        updateAdPreview();
    }

    // ── Date toggles: "No limit" checkboxes ─────────────────────────────
    const startInput   = document.getElementById('start_at');
    const endInput     = document.getElementById('end_at');
    const startNoLimit = document.getElementById('start_no_limit');
    const endNoLimit   = document.getElementById('end_no_limit');

    function applyStartLimit() {
        if (!startInput || !startNoLimit) return;
        startInput.disabled = startNoLimit.checked;
        if (startNoLimit.checked) startInput.value = '';
    }

    function applyEndLimit() {
        if (!endInput || !endNoLimit) return;
        endInput.disabled = endNoLimit.checked;
        if (endNoLimit.checked) endInput.value = '';
    }

    if (startNoLimit) { startNoLimit.addEventListener('change', applyStartLimit); applyStartLimit(); }
    if (endNoLimit)   { endNoLimit.addEventListener('change', applyEndLimit);     applyEndLimit(); }

    // ── Quick duration buttons ────────────────────────────────────────────
    document.querySelectorAll('.quick-dur').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const days = parseInt(btn.dataset.days, 10);

            // Determine start — use current start_at value or now
            let base;
            if (startInput && startInput.value) {
                base = new Date(startInput.value);
            } else {
                base = new Date();
                // Round down to current minute
                base.setSeconds(0, 0);
            }

            // If start was blank, fill it with "now" and uncheck the no-limit toggle
            if (startInput && !startInput.value) {
                startInput.value = toLocalDatetimeInput(base);
                if (startNoLimit) {
                    startNoLimit.checked = false;
                    applyStartLimit();
                }
            }

            const end = new Date(base.getTime() + days * 24 * 60 * 60 * 1000);
            if (endInput) {
                endInput.value = toLocalDatetimeInput(end);
                if (endNoLimit) {
                    endNoLimit.checked = false;
                    applyEndLimit();
                }
            }

            // Highlight the clicked button briefly
            document.querySelectorAll('.quick-dur').forEach(function (b) {
                b.classList.remove('btn-secondary', 'active');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        });
    });

    function toLocalDatetimeInput(date) {
        // "YYYY-MM-DDTHH:MM" in local time
        const pad = function (n) { return String(n).padStart(2, '0'); };
        return date.getFullYear() + '-' +
            pad(date.getMonth() + 1) + '-' +
            pad(date.getDate()) + 'T' +
            pad(date.getHours()) + ':' +
            pad(date.getMinutes());
    }

    // ── Delete confirmation ───────────────────────────────────────────────
    window.confirmDeletePlacement = function (id) {
        const form = document.getElementById('deletePlacementForm' + id);
        if (!form) return;

        if (typeof Swal === 'undefined' || !Swal.fire) {
            if (confirm('{{ __('Are you sure you want to delete this placement?') }}')) {
                form.submit();
            }
            return;
        }

        Swal.fire({
            title: '{{ __('Delete Placement?') }}',
            text: '{{ __('This will remove the overlay ad targeting for that reel.') }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __('Yes, delete it!') }}',
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    };
}());
</script>
@endpush
