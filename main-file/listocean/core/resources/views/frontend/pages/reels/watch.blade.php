@extends('frontend.layout.master')

@section('site_title') {{ e($video->title) }} @endsection

@section('style')
<style>
    html, body { background: #000 !important; overflow: hidden; margin: 0; padding: 0; }
    footer, header, nav.navbar, nav { display: none !important; }

    /* STAGE */
    .tk-stage {
        position: relative; width: 100vw; height: 100dvh; height: 100vh;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; background: #000;
    }
    .tk-bg-blur { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
    .tk-bg-blur video {
        width: 100%; height: 100%; object-fit: cover;
        filter: blur(32px) brightness(0.28) saturate(1.5);
        transform: scale(1.1); pointer-events: none;
    }

    /* CENTER */
    .tk-center { position: relative; z-index: 1; display: flex; align-items: flex-end; gap: 0; }

    /* PLAYER */
    .tk-player-wrap {
        position: relative;
        height: calc(100dvh - 16px); height: calc(100vh - 16px);
        aspect-ratio: 9/16;
        max-width: min(calc(100vw - 80px), calc((100vh - 16px) * 9 / 16));
        flex-shrink: 0; overflow: hidden; border-radius: 14px;
    }
    .tk-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; cursor: pointer; z-index: 1; }
    .tk-gradient-top { position: absolute; top: 0; left: 0; right: 0; height: 25%; background: linear-gradient(to bottom, rgba(0,0,0,.55) 0%, transparent 100%); z-index: 2; pointer-events: none; }
    .tk-gradient { position: absolute; bottom: 0; left: 0; right: 0; height: 55%; background: linear-gradient(to top, rgba(0,0,0,.92) 0%, rgba(0,0,0,.45) 45%, transparent 100%); z-index: 2; pointer-events: none; }
    .tk-tap-zone { position: absolute; inset: 0; z-index: 3; cursor: pointer; }
    .tk-play-icon { position: absolute; inset: 0; z-index: 4; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .22s; pointer-events: none; }
    .tk-play-icon.show { opacity: 1; }
    .tk-play-icon svg { width: 68px; height: 68px; filter: drop-shadow(0 2px 14px rgba(0,0,0,.65)); }

    .tk-back {
        position: absolute; top: 14px; left: 14px; z-index: 10;
        display: flex; align-items: center; gap: 6px;
        color: rgba(255,255,255,.9); font-size: 13px; font-weight: 700;
        text-decoration: none; background: rgba(0,0,0,.4); backdrop-filter: blur(8px);
        padding: 6px 12px 6px 8px; border-radius: 100px; transition: background .2s;
    }
    .tk-back:hover { background: rgba(0,0,0,.6); color: #fff; }
    .tk-back svg { width: 15px; height: 15px; }

    .tk-sound-btn {
        position: absolute; top: 14px; right: 14px; z-index: 10;
        width: 36px; height: 36px; border-radius: 50%;
        background: rgba(0,0,0,.4); backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        border: none; cursor: pointer; color: #fff;
    }
    .tk-sound-btn svg { width: 16px; height: 16px; }

    .tk-info { position: absolute; bottom: 44px; left: 0; right: 0; z-index: 6; padding: 0 14px; }
    .tk-author { display: flex; align-items: center; gap: 8px; margin-bottom: 7px; }
    .tk-avatar { width: 36px; height: 36px; border-radius: 50%; background: #fe2c55; border: 2px solid rgba(255,255,255,.55); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0; text-decoration: none; }
    .tk-author-name { font-size: 14px; font-weight: 700; color: #fff; text-shadow: 0 1px 5px rgba(0,0,0,.6); }
    .tk-price-tag { display: inline-flex; align-items: center; background: rgba(254,44,85,.85); color: #fff; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 100px; margin-bottom: 6px; }
    .tk-title { font-size: 13px; font-weight: 400; color: rgba(255,255,255,.92); line-height: 1.5; text-shadow: 0 1px 4px rgba(0,0,0,.5); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 6px; }
    .tk-title.expanded { -webkit-line-clamp: unset; }
    .tk-title-more { display: inline; cursor: pointer; color: rgba(255,255,255,.55); font-size: 12px; font-weight: 600; }

    .tk-controls-strip { position: absolute; bottom: 10px; left: 12px; right: 12px; z-index: 6; display: flex; align-items: center; gap: 8px; }
    .tk-progress { flex: 1; height: 3px; background: rgba(255,255,255,.25); border-radius: 2px; cursor: pointer; position: relative; }
    .tk-progress-fill { height: 100%; background: #fff; border-radius: 2px; width: 0%; pointer-events: none; }
    .tk-time { font-size: 10px; color: rgba(255,255,255,.6); white-space: nowrap; }
    .tk-vol-btn, .tk-fullscreen-btn { background: none; border: none; padding: 0; cursor: pointer; color: rgba(255,255,255,.65); display: flex; align-items: center; }
    .tk-vol-btn svg, .tk-fullscreen-btn svg { width: 15px; height: 15px; }

    .tk-ad-rotator { position: absolute; left: 10px; right: 10px; bottom: 105px; z-index: 20; pointer-events: all; transform: translateZ(0); will-change: transform; }
    .tk-ad-slide { display: none; opacity: 0; transition: opacity .4s; }
    .tk-ad-slide.tk-ad-active { display: block; opacity: 1; }
    .tk-ad-label { display: block; text-align: right; font-size: 9px; color: rgba(255,255,255,.35); margin-bottom: 3px; letter-spacing: .04em; text-transform: uppercase; }
    .tk-ad-img { width: 100%; max-height: 80px; object-fit: contain; border-radius: 10px; box-shadow: 0 2px 16px rgba(0,0,0,.55); display: block; }

    /* OUTSIDE ACTION BUTTONS */
    .tk-ext-actions { display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 0 0 50px 16px; flex-shrink: 0; z-index: 10; }
    .tk-ext-avatar-wrap { position: relative; display: flex; flex-direction: column; align-items: center; margin-bottom: 4px; }
    .tk-ext-avatar { width: 52px; height: 52px; border-radius: 50%; background: #fe2c55; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #fff; text-decoration: none; }
    .tk-ext-plus { position: absolute; bottom: -8px; width: 22px; height: 22px; border-radius: 50%; background: #fe2c55; border: 2px solid #000; display: flex; align-items: center; justify-content: center; text-decoration: none; }
    .tk-ext-plus svg { width: 11px; height: 11px; }

    .tk-ext-btn { display: flex; flex-direction: column; align-items: center; gap: 5px; cursor: pointer; text-decoration: none !important; border: none; background: none; padding: 0; color: #fff; }
    .tk-ext-icon { width: 52px; height: 52px; border-radius: 50%; background: rgba(255,255,255,.1); display: flex; align-items: center; justify-content: center; transition: background .2s, transform .15s; }
    .tk-ext-btn:hover .tk-ext-icon { background: rgba(255,255,255,.2); transform: scale(1.08); }
    .tk-ext-btn.active .tk-ext-icon { background: rgba(254,44,85,.25); }
    .tk-ext-btn.active svg { stroke: #fe2c55 !important; fill: #fe2c55 !important; }
    .tk-ext-icon svg { width: 24px; height: 24px; }
    .tk-ext-count { font-size: 12px; color: #fff; font-weight: 700; letter-spacing: -.2px; line-height: 1; }

    /* MOBILE */
    @media (max-width: 640px) {
        .tk-player-wrap { aspect-ratio: auto; width: 100vw; max-width: 100vw; border-radius: 0; height: 100dvh; height: 100vh; }
        .tk-ext-actions { display: none !important; }
        .tk-actions-mobile { display: flex !important; }
        .tk-back { display: none; }
        .tk-mobile-nav { display: flex !important; }
    }
    .tk-actions-mobile { position: absolute; right: 10px; bottom: 120px; z-index: 6; display: none; flex-direction: column; align-items: center; gap: 18px; }
    .tk-mob-btn { display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; text-decoration: none !important; border: none; background: none; padding: 0; color: #fff; }
    .tk-mob-icon { width: 46px; height: 46px; border-radius: 50%; background: rgba(255,255,255,.12); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; }
    .tk-mob-icon svg { width: 21px; height: 21px; }
    .tk-mob-lbl { font-size: 11px; color: rgba(255,255,255,.8); font-weight: 600; }
    .tk-mobile-nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 10px 16px; display: none; align-items: center; gap: 10px; background: linear-gradient(to bottom, rgba(0,0,0,.65) 0%, transparent 100%); }
    .tk-mobile-nav a { color: rgba(255,255,255,.85); text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px; font-weight: 600; }

    /* COMMENTS DRAWER */
    .tk-cm-overlay { position: fixed; inset: 0; z-index: 300; pointer-events: none; }
    .tk-cm-overlay.open { pointer-events: all; }
    .tk-cm-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0); transition: background .3s; }
    .tk-cm-overlay.open .tk-cm-backdrop { background: rgba(0,0,0,.5); }
    .tk-cm-panel {
        position: absolute; top: 0; right: 0; bottom: 0;
        width: 380px; max-width: 100vw; background: #1c1c1c;
        display: flex; flex-direction: column;
        transform: translateX(100%); transition: transform .32s cubic-bezier(.4,0,.2,1);
        box-shadow: -4px 0 32px rgba(0,0,0,.55);
    }
    .tk-cm-overlay.open .tk-cm-panel { transform: translateX(0); }
    .tk-cm-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid rgba(255,255,255,.08); flex-shrink: 0; }
    .tk-cm-head h3 { margin: 0; font-size: 15px; font-weight: 700; color: #fff; }
    .tk-cm-close { background: none; border: none; cursor: pointer; color: rgba(255,255,255,.6); display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 50%; transition: background .18s; }
    .tk-cm-close:hover { background: rgba(255,255,255,.1); color: #fff; }
    .tk-cm-list { flex: 1; overflow-y: auto; padding: 6px 0; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.15) transparent; }
    .tk-cm-list::-webkit-scrollbar { width: 4px; }
    .tk-cm-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 2px; }
    .tk-cm-item { display: flex; gap: 10px; padding: 10px 18px; }
    .tk-cm-av { width: 36px; height: 36px; border-radius: 50%; background: #fe2c55; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0; }
    .tk-cm-body { flex: 1; min-width: 0; }
    .tk-cm-uname { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 3px; }
    .tk-cm-text { font-size: 13px; color: rgba(255,255,255,.85); line-height: 1.5; word-break: break-word; }
    .tk-cm-meta { display: flex; align-items: center; gap: 14px; margin-top: 5px; }
    .tk-cm-time { font-size: 11px; color: rgba(255,255,255,.38); }
    .tk-cm-lbtn { display: flex; align-items: center; gap: 4px; background: none; border: none; cursor: pointer; color: rgba(255,255,255,.4); font-size: 11px; font-weight: 600; padding: 0; transition: color .18s; }
    .tk-cm-lbtn:hover, .tk-cm-lbtn.liked { color: #fe2c55; }
    .tk-cm-lbtn svg { width: 12px; height: 12px; }
    .tk-cm-reply { font-size: 11px; font-weight: 600; color: rgba(255,255,255,.4); background: none; border: none; cursor: pointer; padding: 0; transition: color .18s; }
    .tk-cm-reply:hover { color: rgba(255,255,255,.75); }
    .tk-cm-empty { padding: 48px 18px; text-align: center; color: rgba(255,255,255,.3); font-size: 14px; }
    .tk-cm-loading { padding: 48px 18px; text-align: center; }
    .tk-cm-spinner { width: 26px; height: 26px; border: 3px solid rgba(255,255,255,.1); border-top-color: #fe2c55; border-radius: 50%; animation: tkSpin .7s linear infinite; display: inline-block; }
    @keyframes tkSpin { to { transform: rotate(360deg); } }
    .tk-cm-footer { flex-shrink: 0; padding: 12px 16px; border-top: 1px solid rgba(255,255,255,.08); background: #1c1c1c; }
    .tk-cm-irow { display: flex; align-items: center; gap: 10px; }
    .tk-cm-inp { flex: 1; background: rgba(255,255,255,.09); border: 1px solid rgba(255,255,255,.12); border-radius: 100px; padding: 9px 16px; font-size: 14px; color: #fff; outline: none; transition: border-color .18s; }
    .tk-cm-inp::placeholder { color: rgba(255,255,255,.35); }
    .tk-cm-inp:focus { border-color: rgba(254,44,85,.55); }
    .tk-cm-send { width: 38px; height: 38px; border-radius: 50%; background: #fe2c55; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: opacity .18s; }
    .tk-cm-send:disabled { opacity: .38; cursor: not-allowed; }
    .tk-cm-send svg { width: 15px; height: 15px; }
    .tk-cm-login { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: #fe2c55; color: #fff !important; font-size: 14px; font-weight: 700; border-radius: 100px; text-decoration: none !important; transition: opacity .2s; }
    .tk-cm-login:hover { opacity: .88; }
    @media (max-width: 640px) {
        .tk-cm-panel { top: auto; right: 0; left: 0; bottom: 0; width: 100%; max-height: 75dvh; max-height: 75vh; border-radius: 16px 16px 0 0; transform: translateY(100%); }
        .tk-cm-overlay.open .tk-cm-panel { transform: translateY(0); }
    }
</style>
@endsection

@section('content')
@php
    $creator     = $video->listing_creator;
    $authorName  = $creator ? trim(($creator->first_name ?? '') . ' ' . ($creator->last_name ?? '')) : ($video->contact_name ?? __('Seller'));
    $authorInitial = strtoupper(substr($authorName, 0, 1)) ?: 'S';
    $_posterData = get_attachment_image_by_id($video->image ?? 0);
    $_poster     = $_posterData['img_url'] ?? '';
    function _rvFmt(int $n): string {
        if ($n >= 1000000) return round($n/1000000, 1) . 'M';
        if ($n >= 1000)    return round($n/1000, 1) . 'K';
        return (string)$n;
    }
    $viewCount = _rvFmt((int)($video->view ?? 0));
@endphp

<div class="tk-mobile-nav">
    <a href="{{ route('frontend.trending.videos') }}">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
        {{ __('Explore') }}
    </a>
</div>

<div class="tk-stage">
    <div class="tk-bg-blur">
        <video src="{{ $video->video_url }}" muted autoplay loop playsinline preload="auto"></video>
    </div>

    <div class="tk-center">

        {{-- VIDEO PLAYER --}}
        <div class="tk-player-wrap" id="tk-player-wrap">
            <div class="tk-gradient-top"></div>
            <div class="tk-gradient"></div>

            <a href="{{ route('frontend.trending.videos') }}" class="tk-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                {{ __('Explore') }}
            </a>

            <button class="tk-sound-btn" onclick="toggleMute()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                    <path id="tk-vol-wave" d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/>
                    <path id="tk-mute-x" d="M23 9l-6 6M17 9l6 6" style="display:none;"/>
                </svg>
            </button>

            <video id="tk-video" class="tk-video" src="{{ $video->video_url }}"
                @if($_poster) poster="{{ $_poster }}" @endif
                autoplay muted loop playsinline preload="auto"></video>

            <div class="tk-tap-zone" id="tk-tap"></div>

            <div class="tk-play-icon" id="tk-play-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="11" fill="rgba(0,0,0,.42)"/>
                    <polygon points="10,7.5 10,16.5 18,12" fill="#fff"/>
                </svg>
            </div>

            {{-- Mobile inside actions --}}
            <div class="tk-actions-mobile">
                <button class="tk-mob-btn" onclick="toggleLike()">
                    <div class="tk-mob-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></div>
                    <span class="tk-mob-lbl">{{ $viewCount }}</span>
                </button>
                <button class="tk-mob-btn" onclick="shareReel()">
                    <div class="tk-mob-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></div>
                    <span class="tk-mob-lbl">{{ __('Share') }}</span>
                </button>
                <a href="{{ route('frontend.listing.details', $video->slug) }}" class="tk-mob-btn">
                    <div class="tk-mob-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></div>
                    <span class="tk-mob-lbl">{{ __('View') }}</span>
                </a>
            </div>

            {{-- Ad overlays --}}
            @if(!empty($adOverlays))
                <div class="tk-ad-rotator" id="watchAdRotator">
                    @foreach($adOverlays as $__i => $__ad)
                        <div class="tk-ad-slide{{ $__i === 0 ? ' tk-ad-active' : '' }}">
                            <span class="tk-ad-label">Ad</span>
                            @if(!empty($__ad['redirect_url']))
                                <a href="{{ $__ad['redirect_url'] }}" target="_blank" rel="noopener sponsored" style="display:block;">
                                    <img src="{{ $__ad['image'] }}" alt="{{ $__ad['title'] ?? 'Advertisement' }}" class="tk-ad-img">
                                </a>
                            @else
                                <img src="{{ $__ad['image'] }}" alt="{{ $__ad['title'] ?? 'Advertisement' }}" class="tk-ad-img">
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Bottom info --}}
            <div class="tk-info">
                @if(!empty($video->price))
                    <div class="tk-price-tag">
                        {{ number_format($video->price) }}
                        @if(!empty($video->negotiable) && $video->negotiable)
                            &nbsp;<span style="opacity:.7;font-weight:400;font-size:10px;">{{ __('Neg.') }}</span>
                        @endif
                    </div>
                @endif
                <div class="tk-title" id="tk-title-text">
                    {{ e($video->title) }}
                    @if(!empty($video->description) && strlen(strip_tags($video->description)) > 0)
                        &nbsp;<span class="tk-title-more" onclick="expandDesc()">{{ __('more') }}</span>
                    @endif
                </div>
            </div>

            {{-- Controls --}}
            <div class="tk-controls-strip">
                <button class="tk-vol-btn" onclick="toggleMute()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path id="tk-vol-waves" d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/></svg>
                </button>
                <div class="tk-progress" id="tk-progress" onclick="seekVideo(event)">
                    <div class="tk-progress-fill" id="tk-fill"></div>
                </div>
                <span class="tk-time" id="tk-time">0:00</span>
                <button class="tk-fullscreen-btn" onclick="goFullscreen()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3"/></svg>
                </button>
            </div>
        </div>

        {{-- OUTSIDE ACTIONS (TikTok-style, right of video) --}}
        <div class="tk-ext-actions">

            <div class="tk-ext-avatar-wrap">
                <a href="{{ route('frontend.listing.details', $video->slug) }}" class="tk-ext-avatar">{{ $authorInitial }}</a>
                <a href="{{ route('frontend.listing.details', $video->slug) }}" class="tk-ext-plus">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </a>
            </div>

            <button class="tk-ext-btn" id="tk-like-btn" onclick="toggleLike()">
                <div class="tk-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                </div>
                <span class="tk-ext-count">{{ $viewCount }}</span>
            </button>

            <button class="tk-ext-btn" id="tk-comment-btn" onclick="openComments()">
                <div class="tk-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <span class="tk-ext-count" id="tk-cm-badge">0</span>
            </button>

            <a href="{{ route('frontend.listing.details', $video->slug) }}" class="tk-ext-btn">
                <div class="tk-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                </div>
                <span class="tk-ext-count">{{ __('Save') }}</span>
            </a>

            <button class="tk-ext-btn" onclick="shareReel()">
                <div class="tk-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                </div>
                <span class="tk-ext-count">{{ __('Share') }}</span>
            </button>

            <a href="{{ route('frontend.listing.details', $video->slug) }}" class="tk-ext-btn">
                <div class="tk-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </div>
                <span class="tk-ext-count">{{ __('View') }}</span>
            </a>

        </div>

    </div>
</div>

{{-- COMMENTS DRAWER --}}
<div class="tk-cm-overlay" id="tk-cm-overlay">
    <div class="tk-cm-backdrop" onclick="closeComments()"></div>
    <div class="tk-cm-panel" role="dialog" aria-label="Comments">
        <div class="tk-cm-head">
            <h3>{{ __('Comments') }} <span id="tk-cm-count" style="color:rgba(255,255,255,.5);font-weight:400;"></span></h3>
            <button class="tk-cm-close" onclick="closeComments()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="tk-cm-list" id="tk-cm-list">
            <div class="tk-cm-loading"><span class="tk-cm-spinner"></span></div>
        </div>
        <div class="tk-cm-footer">
            @auth('web')
            <div class="tk-cm-irow">
                <input type="text" id="tk-cm-inp" class="tk-cm-inp" placeholder="{{ __('Add a comment…') }}" maxlength="500" autocomplete="off">
                <button class="tk-cm-send" id="tk-cm-send" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
            @else
            <a href="{{ route('user.login') }}" class="tk-cm-login">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                {{ __('Log in to comment') }}
            </a>
            @endauth
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    /* ── DOM refs ──────────────────────────────────────────────── */
    var vid      = document.getElementById('tk-video');
    var tapZone  = document.getElementById('tk-tap');
    var playIco  = document.getElementById('tk-play-icon');
    var fill     = document.getElementById('tk-fill');
    var timeEl   = document.getElementById('tk-time');
    var volWave  = document.getElementById('tk-vol-wave');
    var volWaves = document.getElementById('tk-vol-waves');
    var muteX    = document.getElementById('tk-mute-x');

    /* ── AUTOPLAY ────────────────────────────────────────────────────
       KEY RULE: NEVER call play() immediately on page load.
       The HTML `autoplay muted` attribute already issued a play
       request internally. Calling play() a second time before the
       browser finishes that first request causes an AbortError that
       kills BOTH requests — this is why the video stays paused.

       Strategy:
       - Set muted=true, then let the HTML autoplay attribute do its job.
       - Cancel our fallback timer the moment the 'play' event fires.
       - If the video is still paused after 600ms, THEN call play().
       - Hard fallbacks: window load + visibility change.
       - If user just scroll-navigated here (reel_nav flag), we have a
         user gesture context — use a very short 50ms delay instead.
    ───────────────────────────────────────────────────────────────── */
    if (vid) {
        vid.muted = true; // required for autoplay policy

        // Read & immediately clear the navigation flag
        var _userNavigated = false;
        try {
            _userNavigated = sessionStorage.getItem('reel_nav') === '1';
            sessionStorage.removeItem('reel_nav');
        } catch(e) {}

        var autoplayTimer = null;

        function doPlay() {
            if (!vid || !vid.paused) return;
            vid.muted = true;
            var p = vid.play();
            if (p !== undefined) {
                p.catch(function () {
                    // Still blocked — wait for canplay then try once more
                    vid.addEventListener('canplay', function () {
                        if (vid.paused) vid.play().catch(function () {
                            if (playIco) playIco.classList.add('show');
                        });
                    }, { once: true });
                });
            }
        }

        // If native autoplay fires first — perfect, cancel our timer
        vid.addEventListener('play', function () {
            clearTimeout(autoplayTimer);
        }, { once: true });

        // User navigated here via scroll/swipe → user gesture context,
        // use barely-there delay so play() runs before the gesture expires.
        // Otherwise wait 600ms to let HTML autoplay finish first.
        autoplayTimer = setTimeout(doPlay, _userNavigated ? 50 : 600);

        // Belt: once the full page finishes loading
        window.addEventListener('load', function () {
            if (vid.paused) doPlay();
        });

        // Belt: user switches back to this tab
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible' && vid.paused) doPlay();
        });
    }

    /* ── TAP TO PAUSE / PLAY ────────────────────────────────────── */
    if (tapZone) {
        tapZone.addEventListener('click', function () {
            if (!vid) return;
            if (vid.paused) {
                vid.play();
                if (playIco) playIco.classList.remove('show');
            } else {
                vid.pause();
                if (playIco) playIco.classList.add('show');
            }
        });
    }

    /* ── PROGRESS BAR ────────────────────────────────────────────── */
    function fmt(s) {
        s = Math.floor(s || 0);
        return Math.floor(s / 60) + ':' + (s % 60 < 10 ? '0' : '') + (s % 60);
    }
    if (vid) {
        vid.addEventListener('timeupdate', function () {
            if (!vid.duration) return;
            if (fill)   fill.style.width = (vid.currentTime / vid.duration * 100) + '%';
            if (timeEl) timeEl.textContent = fmt(vid.currentTime) + ' / ' + fmt(vid.duration);
        });
    }

    window.seekVideo = function (e) {
        if (!vid || !vid.duration) return;
        var bar  = document.getElementById('tk-progress');
        var rect = bar.getBoundingClientRect();
        vid.currentTime = ((e.clientX - rect.left) / rect.width) * vid.duration;
    };

    /* ── MUTE TOGGLE ─────────────────────────────────────────────── */
    window.toggleMute = function () {
        if (!vid) return;
        vid.muted = !vid.muted;
        if (vid.muted) {
            if (muteX)    muteX.style.display    = '';
            if (volWave)  volWave.style.display  = 'none';
            if (volWaves) volWaves.style.display = 'none';
        } else {
            if (muteX)    muteX.style.display    = 'none';
            if (volWave)  volWave.style.display  = '';
            if (volWaves) volWaves.style.display = '';
            vid.volume = 1;
        }
    };

    /* ── LIKE / FULLSCREEN / SHARE / EXPAND ──────────────────────── */
    window.toggleLike = function () {
        var btn = document.getElementById('tk-like-btn');
        if (btn) btn.classList.toggle('active');
    };

    window.goFullscreen = function () {
        var pw = document.getElementById('tk-player-wrap');
        if (!pw) return;
        (pw.requestFullscreen || pw.webkitRequestFullscreen || function () {}).call(pw);
    };

    window.shareReel = function () {
        var url = window.location.href;
        if (navigator.share) {
            navigator.share({ title: {!! json_encode($video->title) !!}, url: url }).catch(function () {});
        } else {
            navigator.clipboard.writeText(url).then(function () {
                alert('{{ __("Link copied!") }}');
            }).catch(function () { prompt('{{ __("Copy:") }}', url); });
        }
    };

    window.expandDesc = function () {
        var el = document.getElementById('tk-title-text');
        if (el) {
            el.classList.add('expanded');
            el.querySelectorAll('.tk-title-more').forEach(function (b) { b.remove(); });
        }
    };

    /* ── AD ROTATOR ──────────────────────────────────────────────── */
    (function () {
        var rotator = document.getElementById('watchAdRotator');
        if (!rotator) return;
        var slides  = Array.from(rotator.querySelectorAll('.tk-ad-slide'));
        if (slides.length < 2) return;
        var current = 0;
        setInterval(function () {
            slides[current].classList.remove('tk-ad-active');
            var prev = current;
            setTimeout(function () { slides[prev].style.display = 'none'; }, 400);
            current = (current + 1) % slides.length;
            slides[current].style.display = 'block';
            void slides[current].offsetWidth;
            slides[current].classList.add('tk-ad-active');
        }, 5000);
    })();

    /* ── SWIPE / SCROLL / KEYBOARD NAVIGATION ───────────────────────
       Build an ordered reel queue: [current, ...related].
       Persist to sessionStorage so each watch page can keep the same
       ordered list as the user navigates through the feed.
    ───────────────────────────────────────────────────────────────── */
    var QUEUE_KEY  = 'reel_queue';
    var freshIds   = {!! json_encode(collect([$video])->concat($related)->pluck('id')->values()) !!};
    var currentId  = {!! json_encode($video->id) !!};

    /* If we already have a stored queue that contains this reel, keep using
       it (preserves the order the user started from). Otherwise start fresh. */
    var reelIds;
    try {
        var stored = JSON.parse(sessionStorage.getItem(QUEUE_KEY) || 'null');
        reelIds = (Array.isArray(stored) && stored.indexOf(currentId) !== -1) ? stored : freshIds;
    } catch (e) {
        reelIds = freshIds;
    }
    try { sessionStorage.setItem(QUEUE_KEY, JSON.stringify(reelIds)); } catch (e) {}

    var currentPos = reelIds.indexOf(currentId);

    function navigateNext() {
        if (currentPos < reelIds.length - 1) {
            try { sessionStorage.setItem('reel_nav', '1'); } catch(e) {}
            window.location.href = '/reels/' + reelIds[currentPos + 1];
        }
    }

    function navigatePrev() {
        try { sessionStorage.setItem('reel_nav', '1'); } catch(e) {}
        if (currentPos > 0) {
            window.location.href = '/reels/' + reelIds[currentPos - 1];
        } else {
            window.location.href = '{{ route("frontend.trending.videos") }}';
        }
    }

    /* Wheel / trackpad */
    var wheelLocked = false;
    var cmOverlay = document.getElementById('tk-cm-overlay');
    function commentsOpen() {
        return cmOverlay && cmOverlay.classList.contains('open');
    }
    document.addEventListener('wheel', function (e) {
        if (commentsOpen()) return;   // comments panel is capturing scroll
        if (wheelLocked) return;
        if (e.deltaY === 0) return;
        // Normalize deltaMode: 0=pixels, 1=lines (~40px each), 2=pages (~800px each)
        var delta = e.deltaMode === 1 ? e.deltaY * 40
                  : e.deltaMode === 2 ? e.deltaY * 800
                  : e.deltaY;
        if (Math.abs(delta) < 20) return; // filter near-zero trackpad jitter only
        wheelLocked = true;
        if (delta > 0) navigateNext();
        else           navigatePrev();
        setTimeout(function () { wheelLocked = false; }, 900);
    }, { passive: true });

    /* Touch swipe */
    var touchStartY = 0;
    document.addEventListener('touchstart', function (e) {
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    document.addEventListener('touchend', function (e) {
        if (commentsOpen()) return;   // comments panel is capturing swipe
        var dy = touchStartY - e.changedTouches[0].clientY;
        if (Math.abs(dy) < 55) return; // swipe threshold
        if (dy > 0) navigateNext();    // swiped up  → next
        else        navigatePrev();    // swiped down → prev
    }, { passive: true });

    /* Keyboard: ArrowDown/Up for navigation, everything else for player */
    document.addEventListener('keydown', function (e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.code === 'Space' || e.code === 'KeyK') { e.preventDefault(); if (tapZone) tapZone.click(); }
        if (e.code === 'KeyM')        toggleMute();
        if (e.code === 'ArrowRight' && vid) vid.currentTime = Math.min((vid.currentTime || 0) + 5, vid.duration || 0);
        if (e.code === 'ArrowLeft'  && vid) vid.currentTime = Math.max((vid.currentTime || 0) - 5, 0);
        if (e.code === 'KeyF')        goFullscreen();
        if (e.code === 'ArrowDown') { e.preventDefault(); navigateNext(); }
        if (e.code === 'ArrowUp')   { e.preventDefault(); navigatePrev(); }
    });

    /* ── NEXT / PREV ARROW BUTTONS inside the player ────────────── */
    (function () {
        var pw = document.getElementById('tk-player-wrap');
        if (!pw || reelIds.length < 2) return;

        var s = document.createElement('style');
        s.textContent =
            '.tk-nav-arrow{position:absolute;left:50%;transform:translateX(-50%);z-index:9;' +
            'background:rgba(0,0,0,.38);border:none;border-radius:50%;width:34px;height:34px;' +
            'display:flex;align-items:center;justify-content:center;cursor:pointer;' +
            'color:rgba(255,255,255,.85);transition:background .18s;backdrop-filter:blur(6px);pointer-events:all;}' +
            '.tk-nav-arrow:hover{background:rgba(0,0,0,.6);}' +
            '#tk-arr-up{top:60px;}' +
            '#tk-arr-dn{bottom:55px;}';
        document.head.appendChild(s);

        if (currentPos > 0) {
            var ub = document.createElement('button');
            ub.id = 'tk-arr-up'; ub.className = 'tk-nav-arrow';
            ub.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>';
            ub.addEventListener('click', function (e) { e.stopPropagation(); navigatePrev(); });
            pw.appendChild(ub);
        }

        if (currentPos < reelIds.length - 1) {
            var db = document.createElement('button');
            db.id = 'tk-arr-dn'; db.className = 'tk-nav-arrow';
            db.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';
            db.addEventListener('click', function (e) { e.stopPropagation(); navigateNext(); });
            pw.appendChild(db);
        }
    })();

})();
</script>
<script>
/* ── COMMENTS PANEL ──────────────────────────────────────────────── */
(function () {
    var LISTING_ID  = {!! json_encode($video->id) !!};
    var CSRF        = '{{ csrf_token() }}';
    var IS_AUTH     = {!! Auth::guard('web')->check() ? 'true' : 'false' !!};
    var COMMENTS_URL = '/reels/' + LISTING_ID + '/comments';

    var overlay  = document.getElementById('tk-cm-overlay');
    var list     = document.getElementById('tk-cm-list');
    var countEl  = document.getElementById('tk-cm-count');
    var badge    = document.getElementById('tk-cm-badge');
    var inp      = document.getElementById('tk-cm-inp');
    var sendBtn  = document.getElementById('tk-cm-send');

    var loaded   = false;
    var sending  = false;

    /* enable send button when input has text */
    if (inp) {
        inp.addEventListener('input', function () {
            if (sendBtn) sendBtn.disabled = inp.value.trim().length === 0;
        });
        inp.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submitComment(); }
        });
    }
    if (sendBtn) {
        sendBtn.addEventListener('click', submitComment);
    }

    /* ── open ─────────────────────────────────────────────── */
    window.openComments = function () {
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        if (!loaded) loadComments();
    };

    /* ── close ────────────────────────────────────────────── */
    window.closeComments = function () {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    };

    /* close on Escape */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('open')) closeComments();
    });

    /* ── load comments via AJAX ───────────────────────────── */
    function loadComments() {
        list.innerHTML = '<div class="tk-cm-loading"><span class="tk-cm-spinner"></span></div>';
        fetch(COMMENTS_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                loaded = true;
                renderComments(data.comments, data.total);
            })
            .catch(function () {
                list.innerHTML = '<div class="tk-cm-empty">{{ __("Could not load comments.") }}</div>';
            });
    }

    /* ── render comment list ──────────────────────────────── */
    function renderComments(comments, total) {
        var t = total || 0;
        if (countEl) countEl.textContent = t;
        if (badge)   badge.textContent   = t;

        if (!comments || comments.length === 0) {
            list.innerHTML = '<div class="tk-cm-empty">{{ __("No comments yet. Be the first!") }}</div>';
            return;
        }

        var html = '';
        comments.forEach(function (c) {
            html += '<div class="tk-cm-item" id="tkc-' + c.id + '">' +
                '<div class="tk-cm-av">' + esc(c.initial) + '</div>' +
                '<div class="tk-cm-body">' +
                    '<div class="tk-cm-uname">' + esc(c.user_name) + '</div>' +
                    '<div class="tk-cm-text">' + esc(c.body) + '</div>' +
                    '<div class="tk-cm-meta">' +
                        '<span class="tk-cm-time">' + esc(c.created_at) + '</span>' +
                        '<button class="tk-cm-lbtn" data-id="' + c.id + '" data-liked="0">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>' +
                            ' <span>' + (c.likes || 0) + '</span>' +
                        '</button>' +
                        '<button class="tk-cm-reply">{{ __("Reply") }}</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });
        list.innerHTML = html;
    }

    /* ── client-side like toggle (UI only) ────────────────── */
    list.addEventListener('click', function (e) {
        var btn = e.target.closest('.tk-cm-lbtn');
        if (!btn) return;
        var liked = btn.dataset.liked === '1';
        var span  = btn.querySelector('span');
        var n     = parseInt(span.textContent, 10) || 0;
        if (liked) {
            btn.dataset.liked = '0';
            btn.classList.remove('liked');
            span.textContent = Math.max(0, n - 1);
        } else {
            btn.dataset.liked = '1';
            btn.classList.add('liked');
            span.textContent = n + 1;
        }
    });

    /* ── submit new comment ───────────────────────────────── */
    function submitComment() {
        if (!IS_AUTH || sending) return;
        var body = inp ? inp.value.trim() : '';
        if (!body) return;

        sending = true;
        if (sendBtn) sendBtn.disabled = true;

        fetch(COMMENTS_URL, {
            method:  'POST',
            headers: {
                'Content-Type':      'application/json',
                'X-CSRF-TOKEN':      CSRF,
                'X-Requested-With':  'XMLHttpRequest',
            },
            body: JSON.stringify({ body: body }),
        })
        .then(function (r) { return r.json(); })
        .then(function (c) {
            if (inp)  inp.value = '';
            if (sendBtn) sendBtn.disabled = true;

            /* prepend new comment */
            var item = document.createElement('div');
            item.className = 'tk-cm-item';
            item.id = 'tkc-' + c.id;
            item.innerHTML =
                '<div class="tk-cm-av">' + esc(c.initial) + '</div>' +
                '<div class="tk-cm-body">' +
                    '<div class="tk-cm-uname">' + esc(c.user_name) + '</div>' +
                    '<div class="tk-cm-text">' + esc(c.body) + '</div>' +
                    '<div class="tk-cm-meta">' +
                        '<span class="tk-cm-time">just now</span>' +
                        '<button class="tk-cm-lbtn" data-liked="0">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>' +
                            ' <span>0</span>' +
                        '</button>' +
                    '</div>' +
                '</div>';

            /* remove "no comments" placeholder if present */
            var empty = list.querySelector('.tk-cm-empty');
            if (empty) empty.remove();

            list.insertBefore(item, list.firstChild);

            /* bump counts */
            var prev = parseInt((countEl && countEl.textContent) || 0, 10);
            if (countEl) countEl.textContent = prev + 1;
            if (badge)   badge.textContent   = prev + 1;
        })
        .catch(function () {
            alert('{{ __("Failed to post comment.") }}');
        })
        .finally(function () {
            sending = false;
        });
    }

    /* ── HTML escape helper ───────────────────────────────── */
    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
})();
</script>
@endsection
