@extends('frontend.layout.master')

@section('site_title') {{ __('Explore') }} @endsection

@section('style')
<style>
    /* ── Hide chrome ── */
    html, body { background: #000 !important; overflow-x: hidden; margin: 0; padding: 0; }
    header, nav.navbar, nav, footer { display: none !important; }

    /* ── Top bar ── */
    .xp-topbar {
        position: fixed; top: 0; left: 0; right: 0; z-index: 100;
        background: #000; border-bottom: 1px solid #1a1a1a;
        height: 56px; display: flex; align-items: center;
        padding: 0 16px; gap: 14px;
    }
    .xp-logo {
        font-size: 17px; font-weight: 900; color: #fff; letter-spacing: -.5px;
        text-decoration: none; white-space: nowrap; flex-shrink: 0;
    }
    .xp-logo span { color: #fe2c55; }
    .xp-search-wrap {
        flex: 1; max-width: 420px; position: relative;
    }
    .xp-search-wrap svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        pointer-events: none; opacity: .45;
    }
    .xp-search {
        width: 100%; background: #161616; border: 1px solid #2a2a2a;
        border-radius: 100px; padding: 8px 16px 8px 38px;
        color: #fff; font-size: 13px; outline: none;
        transition: border-color .2s;
    }
    .xp-search::placeholder { color: #555; }
    .xp-search:focus { border-color: #444; }
    .xp-count { font-size: 12px; color: #444; margin-left: auto; }
    .xp-home-btn {
        display: flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 50%;
        background: #161616; border: 1px solid #2a2a2a;
        color: #fff; flex-shrink: 0; transition: background .2s, border-color .2s;
        text-decoration: none !important;
    }
    .xp-home-btn:hover { background: #fe2c55; border-color: #fe2c55; color: #fff; }
    .reels-page { background: #000; min-height: 100vh; padding: 20px 12px 60px; }
    /* ── Grid ── */
    .xp-grid-wrap { margin-top: 56px; padding: 2px; }
    .xp-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2px;
    }
    @media(min-width: 480px)  { .xp-grid { grid-template-columns: repeat(3, 1fr); } }
    @media(min-width: 700px)  { .xp-grid { grid-template-columns: repeat(4, 1fr); } }
    @media(min-width: 960px)  { .xp-grid { grid-template-columns: repeat(5, 1fr); } }
    @media(min-width: 1200px) { .xp-grid { grid-template-columns: repeat(6, 1fr); } }
    @media(min-width: 1600px) { .xp-grid { grid-template-columns: repeat(7, 1fr); } }

    /* ── Card ── */
    .xp-card {
        position: relative; display: block; overflow: hidden;
        cursor: pointer; text-decoration: none !important;
        background: #111; -webkit-tap-highlight-color: transparent;
    }
    .xp-thumb-wrap { position: relative; aspect-ratio: 3/4; overflow: hidden; background: #1a1a1a; }
    .xp-thumb { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .35s ease; }
    .xp-card:hover .xp-thumb { transform: scale(1.06); }
    .xp-hover-vid {
        position: absolute; inset: 0; width: 100%; height: 100%;
        object-fit: cover; display: block; z-index: 1;
        opacity: 0; transition: opacity .3s ease;
    }
    .xp-hover-vid.xp-vid-ready { opacity: 1; }
    .xp-card-bottom {
        position: absolute; bottom: 0; left: 0; right: 0; z-index: 3;
        padding: 40px 8px 8px;
        background: linear-gradient(to top, rgba(0,0,0,.9) 0%, transparent 100%);
    }
    .xp-card-title {
        font-size: 11.5px; font-weight: 600; color: #fff; line-height: 1.3;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; margin: 0 0 5px;
        text-shadow: 0 1px 4px rgba(0,0,0,.7);
    }
    .xp-card-views {
        display: flex; align-items: center; gap: 4px;
        font-size: 11px; color: rgba(255,255,255,.7); font-weight: 500;
    }
    /* Playing badge */
    .xp-playing-badge {
        position: absolute; top: 8px; right: 8px; z-index: 4;
        display: none; align-items: center; gap: 4px;
        background: rgba(0,0,0,.55); border-radius: 100px;
        padding: 3px 7px; font-size: 10px; color: rgba(255,255,255,.85); font-weight: 600;
    }
    .xp-card:hover .xp-playing-badge { display: flex; }
    .xp-bar-wrap { display: flex; align-items: flex-end; gap: 2px; height: 12px; }
    .xp-bar { width: 3px; background: #fe2c55; border-radius: 2px; animation: xpBarBounce .6s ease-in-out infinite alternate; }
    .xp-bar:nth-child(2) { animation-delay: .15s; }
    .xp-bar:nth-child(3) { animation-delay: .3s; }
    @keyframes xpBarBounce { from { height: 3px; } to { height: 12px; } }

    /* Pagination */
    .xp-pag { padding: 24px 16px 32px; display: flex; justify-content: center; background: #000; }
    .xp-pag .pagination .page-link {
        background: #111; border-color: #222; color: #fff; border-radius: 6px; font-size: 13px; padding: 7px 14px;
    }
    .xp-pag .pagination .page-item.active .page-link,
    .xp-pag .pagination .page-link:hover { background: #fe2c55; border-color: #fe2c55; color: #fff; }

    /* Empty */
    .xp-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; color: #444; gap: 14px; }
    .xp-empty svg { width: 56px; height: 56px; opacity: .2; }
    .xp-empty p { font-size: 14px; margin: 0; }
</style>
@endsection

@section('content')

{{-- Top Bar --}}
<div class="xp-topbar">
    <a href="{{ url('/') }}" class="xp-logo">SellUp<span>Now</span></a>
    <a href="{{ url('/') }}" class="xp-home-btn" title="{{ __('Home') }}">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>
    </a>
    <div class="xp-search-wrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input class="xp-search" type="text" id="xpSearch" placeholder="{{ __('Search videos…') }}" autocomplete="off">
    </div>
    @if($videos->total())
        <span class="xp-count">{{ number_format($videos->total()) }} {{ __('videos') }}</span>
    @endif
</div>

<div class="xp-grid-wrap">
    @if($videos->isNotEmpty())
        <div class="xp-grid" id="xpGrid">
            @foreach($videos as $item)
                @php
                    // Resolve thumbnail — user_video items carry a full thumbnail_url;
                    // listing items use the attachment system.
                    if (isset($item->_type) && $item->_type === 'user_video') {
                        $_thumb = $item->thumbnail_url ?? '';
                    } else {
                        $_imgData = get_attachment_image_by_id($item->image ?? 0);
                        $_thumb   = $_imgData['img_url'] ?? '';
                    }
                    $vCount = (int)($item->view ?? 0);
                    $vLabel = $vCount >= 1000000 ? round($vCount/1000000,1).'M' : ($vCount >= 1000 ? round($vCount/1000,1).'K' : $vCount);
                    // Link: user videos use av_start so the feed opens on that specific video
                    $_startLink = (isset($item->_type) && $item->_type === 'user_video')
                        ? route('reels.index') . '?av_start=' . $item->id
                        : route('reels.index') . '?start=' . $item->id;
                @endphp
                <a href="{{ $_startLink }}"
                   class="xp-card"
                   data-video="{{ $item->video_url }}"
                   data-title="{{ strtolower(e($item->title)) }}">
                    <div class="xp-thumb-wrap">
                        @if($_thumb)
                            <img class="xp-thumb" src="{{ $_thumb }}" alt="{{ e($item->title) }}" loading="lazy">
                        @else
                            <video class="xp-thumb" src="{{ $item->video_url }}"
                                muted playsinline preload="metadata"
                                style="width:100%;height:100%;object-fit:cover;display:block;"></video>
                        @endif

                        <div class="xp-playing-badge">
                            <div class="xp-bar-wrap">
                                <div class="xp-bar"></div>
                                <div class="xp-bar"></div>
                                <div class="xp-bar"></div>
                            </div>
                        </div>

                        <div class="xp-card-bottom">
                            <div class="xp-card-title">{{ e($item->title) }}</div>
                            <div class="xp-card-views">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                {{ $vLabel }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="xp-pag">{{ $videos->links() }}</div>
    @else
        <div class="xp-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5">
                <rect x="2" y="2" width="20" height="20" rx="4"/>
                <polygon points="9,7 9,17 18,12" fill="#fff"/>
            </svg>
            <p>{{ __('No trending reels yet.') }}</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    /* ── Live search filter ── */
    var searchEl = document.getElementById('xpSearch');
    if (searchEl) {
        searchEl.addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            document.querySelectorAll('.xp-card').forEach(function (card) {
                var t = (card.dataset.title || '').toLowerCase();
                card.style.display = (!q || t.includes(q)) ? '' : 'none';
            });
        });
    }

    /* ── Hover-to-play (desktop only) ── */
    var isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
    if (isTouchDevice) return;

    document.querySelectorAll('.xp-card[data-video]').forEach(function (card) {
        var videoSrc  = card.dataset.video;
        if (!videoSrc) return;

        var thumbWrap = card.querySelector('.xp-thumb-wrap');
        var hoverVid  = null;

        card.addEventListener('mouseenter', function () {
            if (hoverVid) return;

            hoverVid              = document.createElement('video');
            hoverVid.src          = videoSrc;
            hoverVid.muted        = true;
            hoverVid.autoplay     = true;
            hoverVid.loop         = true;
            hoverVid.playsInline  = true;
            hoverVid.preload      = 'metadata';
            hoverVid.className    = 'xp-hover-vid';

            if (thumbWrap) thumbWrap.appendChild(hoverVid);

            hoverVid.addEventListener('canplay', function () {
                hoverVid && hoverVid.classList.add('xp-vid-ready');
            }, { once: true });

            hoverVid.play().catch(function () {
                if (hoverVid) { hoverVid.remove(); hoverVid = null; }
            });
        });

        card.addEventListener('mouseleave', function () {
            if (hoverVid) {
                hoverVid.pause();
                hoverVid.classList.remove('xp-vid-ready');
                hoverVid.remove();
                hoverVid = null;
            }
        });
    });
})();
</script>
@endsection
