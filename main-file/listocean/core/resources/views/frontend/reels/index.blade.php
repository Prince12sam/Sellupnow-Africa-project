@extends('frontend.layout.master')

@section('title') {{ __('Reels') }} @endsection

@section('style')
<style>
html, body { background: #000 !important; overflow: hidden; margin: 0; padding: 0; }
footer, header, nav.navbar, nav { display: none !important; }

/* ═══════════════════════════════════════════════
   FEED CONTAINER
═══════════════════════════════════════════════ */
.reels-feed {
    width: 100vw; height: 100dvh; height: 100vh;
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    scrollbar-width: none; -ms-overflow-style: none;
    display: flex; flex-direction: column;
    background: #000;
}
.reels-feed::-webkit-scrollbar { display: none; }

/* ═══════════════════════════════════════════════
   REEL WRAPPER (one per video, full-screen snap item)
═══════════════════════════════════════════════ */
.reel-wrapper {
    width: 100%; height: 100dvh; height: 100vh;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
    background: #000;
    scroll-snap-align: start;
    scroll-snap-stop: always;
    display: flex; align-items: center; justify-content: center;
    touch-action: pan-y;
}

/* ═══════════════════════════════════════════════
   BLUR BACKGROUND
═══════════════════════════════════════════════ */
.reel-bg-blur { display: none !important; }

/* Blurred background VIDEO inside the card — fills letterbox areas */
.reel-card-bg {
    position: absolute !important;
    inset: -10% !important;
    width:  120% !important;
    height: 120% !important;
    max-width:  none !important;
    max-height: none !important;
    object-fit: cover !important;
    object-position: center;
    filter: blur(24px) brightness(0.3) saturate(1.8);
    z-index: 0;
    pointer-events: none;
}

/* ═══════════════════════════════════════════════
   CENTER ROW  (player + side actions)
═══════════════════════════════════════════════ */
.reel-center {
    position: relative; z-index: 1;
    display: flex; align-items: flex-end; gap: 0;
}

/* ═══════════════════════════════════════════════
   PLAYER WRAP — fixed 9:16 TikTok card
═══════════════════════════════════════════════ */
.reel-player-wrap {
    position: relative !important;
    height: calc(100dvh - 16px);
    height: calc(100vh  - 16px);
    width:  calc((100vh - 16px) * 9 / 16);
    max-width: calc(100vw - 90px);
    min-width: 160px;
    flex-shrink: 0;
    overflow: hidden;
    border-radius: 14px;
    background: #000;
    box-shadow: 0 4px 40px rgba(0,0,0,.6);
}
.reel-main-video {
    position: absolute !important;
    inset: 0 !important;
    width:  100% !important;
    height: 100% !important;
    max-width:  none !important;
    max-height: none !important;
    object-fit: cover !important;
    object-position: center !important;
    cursor: pointer;
    z-index: 1;
}
@media (max-width: 640px) {
    .reel-player-wrap {
        width:         100vw  !important;
        max-width:     100vw  !important;
        height:        100dvh !important;
        height:        100vh  !important;
        border-radius: 0      !important;
        box-shadow:    none   !important;
    }
}
.reel-gradient-top {
    position: absolute; top: 0; left: 0; right: 0; height: 25%;
    background: linear-gradient(to bottom, rgba(0,0,0,.55) 0%, transparent 100%);
    z-index: 2; pointer-events: none;
}
.reel-gradient {
    position: absolute; bottom: 0; left: 0; right: 0; height: 55%;
    background: linear-gradient(to top, rgba(0,0,0,.92) 0%, rgba(0,0,0,.45) 45%, transparent 100%);
    z-index: 2; pointer-events: none;
}

/* Tap zone — touch-action:pan-y lets swipe-scroll pass through to .reels-feed */
.reel-tap-zone { position: absolute; inset: 0; z-index: 3; cursor: pointer; touch-action: pan-y; }
.reel-play-icon {
    position: absolute; inset: 0; z-index: 4;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .22s; pointer-events: none;
}
.reel-play-icon.show { opacity: 1; }
.reel-play-icon svg { width: 68px; height: 68px; filter: drop-shadow(0 2px 14px rgba(0,0,0,.65)); }

/* Back / explore button */
.reel-back-btn {
    position: absolute; top: 14px; left: 14px; z-index: 10;
    display: flex; align-items: center; gap: 6px;
    color: rgba(255,255,255,.9); font-size: 13px; font-weight: 700;
    text-decoration: none; background: rgba(0,0,0,.4); backdrop-filter: blur(8px);
    padding: 6px 12px 6px 8px; border-radius: 100px; transition: background .2s;
}
.reel-back-btn:hover { background: rgba(0,0,0,.6); color: #fff; }
.reel-back-btn svg { width: 15px; height: 15px; }

/* Sound toggle */
.reel-sound-btn {
    position: absolute; top: 14px; right: 14px; z-index: 10;
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(0,0,0,.4); backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    border: none; cursor: pointer; color: #fff;
}
.reel-sound-btn svg { width: 16px; height: 16px; }

/* Info overlay */
.reel-info { position: absolute; bottom: 44px; left: 0; right: 0; z-index: 6; padding: 0 14px; }
.reel-author { display: flex; align-items: center; gap: 8px; margin-bottom: 7px; }
.reel-avatar { width: 36px; height: 36px; border-radius: 50%; background: #fe2c55; border: 2px solid rgba(255,255,255,.55); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0; }
.reel-author-name { font-size: 14px; font-weight: 700; color: #fff; text-shadow: 0 1px 5px rgba(0,0,0,.6); }
.reel-price-tag { display: inline-flex; align-items: center; background: rgba(254,44,85,.85); color: #fff; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 100px; margin-bottom: 6px; }
.reel-card-title { font-size: 13px; font-weight: 400; color: rgba(255,255,255,.92); line-height: 1.5; text-shadow: 0 1px 4px rgba(0,0,0,.5); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 6px; }

/* Controls strip */
.reel-controls-strip { position: absolute; bottom: 10px; left: 12px; right: 12px; z-index: 6; display: flex; align-items: center; gap: 8px; }
.reel-progress { flex: 1; height: 3px; background: rgba(255,255,255,.25); border-radius: 2px; cursor: pointer; position: relative; }
.reel-progress-fill { height: 100%; background: #fff; border-radius: 2px; width: 0%; pointer-events: none; }
.rl-time { font-size: 10px; color: rgba(255,255,255,.6); white-space: nowrap; }
.rl-vol-btn { background: none; border: none; padding: 0; cursor: pointer; color: rgba(255,255,255,.65); display: flex; align-items: center; }
.rl-vol-btn svg { width: 15px; height: 15px; }

/* Ad rotator */
.reel-ad-rotator {
    position: absolute; left: 10px; right: 10px; bottom: 105px;
    z-index: 20; pointer-events: all;
    transform: translateZ(0); will-change: transform;
}
.reel-ad-slide { display: none; opacity: 0; transition: opacity .4s; }
.reel-ad-slide.reel-ad-active { display: block; opacity: 1; }
.reel-ad-label { display: block; text-align: right; font-size: 9px; color: rgba(255,255,255,.35); margin-bottom: 3px; letter-spacing: .04em; text-transform: uppercase; }
.reel-ad-img-wrap { display: block; width: 100%; height: 90px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,.55); }
.reel-ad-img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Sponsored badge */
.sponsored-badge {
    position: absolute; top: 14px; right: 60px; z-index: 10;
    background: rgba(0,0,0,.55); color: #fff;
    font-size: 10px; padding: 3px 9px; border-radius: 12px; letter-spacing: .03em;
}

/* ═══════════════════════════════════════════════
   MOBILE IN-PLAYER ACTIONS
═══════════════════════════════════════════════ */
.reel-actions-mobile {
    position: absolute; right: 10px; bottom: 120px; z-index: 6;
    display: none; flex-direction: column; align-items: center; gap: 18px;
}
.rl-mob-btn {
    display: flex; flex-direction: column; align-items: center; gap: 4px;
    cursor: pointer; text-decoration: none !important;
    border: none; background: none; padding: 0; color: #fff;
}
.rl-mob-icon {
    width: 46px; height: 46px; border-radius: 50%;
    background: rgba(255,255,255,.12); backdrop-filter: blur(6px);
    display: flex; align-items: center; justify-content: center;
}
.rl-mob-icon svg { width: 21px; height: 21px; }
.rl-mob-lbl { font-size: 11px; color: rgba(255,255,255,.8); font-weight: 600; }

/* ═══════════════════════════════════════════════
   DESKTOP SIDE ACTIONS
═══════════════════════════════════════════════ */
.reel-ext-actions {
    display: flex; flex-direction: column; align-items: center;
    gap: 20px; padding: 0 0 50px 16px; flex-shrink: 0; z-index: 10;
}
.rl-ext-avatar-wrap { position: relative; display: flex; flex-direction: column; align-items: center; margin-bottom: 4px; }
.rl-ext-avatar { width: 52px; height: 52px; border-radius: 50%; background: #fe2c55; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #fff; text-decoration: none; }
.rl-ext-btn {
    display: flex; flex-direction: column; align-items: center; gap: 5px;
    cursor: pointer; text-decoration: none !important;
    border: none; background: none; padding: 0; color: #fff;
}
.rl-ext-icon { width: 52px; height: 52px; border-radius: 50%; background: rgba(255,255,255,.1); display: flex; align-items: center; justify-content: center; transition: background .2s, transform .15s; }
.rl-ext-btn:hover .rl-ext-icon { background: rgba(255,255,255,.2); transform: scale(1.08); }
.rl-ext-btn.active .rl-ext-icon { background: rgba(254,44,85,.25); }
.rl-ext-btn.active svg { stroke: #fe2c55 !important; fill: #fe2c55 !important; }
.rl-ext-icon svg { width: 24px; height: 24px; }
.rl-ext-count { font-size: 12px; color: #fff; font-weight: 700; letter-spacing: -.2px; line-height: 1; }

/* ═══════════════════════════════════════════════
   MOBILE NAV (top fixed)
═══════════════════════════════════════════════ */
.reel-mobile-nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    padding: 10px 16px;
    display: none; align-items: center; gap: 10px;
    background: linear-gradient(to bottom, rgba(0,0,0,.65) 0%, transparent 100%);
}
.reel-mobile-nav a { color: rgba(255,255,255,.85); text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 14px; font-weight: 600; }

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media (max-width: 640px) {
    .reel-ext-actions { display: none !important; }
    .reel-actions-mobile { display: flex !important; }
    .reel-back-btn { display: none; }
    .reel-mobile-nav { display: flex !important; }
}

/* ═══════════════════════════════════════════════
   EMPTY STATE  /  LOAD MORE
═══════════════════════════════════════════════ */
.reels-empty {
    display: flex; align-items: center; justify-content: center;
    min-height: 100vh; color: #999;
    flex-direction: column; gap: 12px; background: #000;
}
#loadMoreBtn {
    display: block; margin: 0 auto;
    padding: 10px 36px; background: #e74c3c; color: #fff;
    border: none; border-radius: 24px; font-size: .9rem; cursor: pointer;
    scroll-snap-align: start;
}
#loadMoreBtn:disabled { background: #555; cursor: default; }

/* ═══════════════════════════════════════════════
   COMMENTS DRAWER
═══════════════════════════════════════════════ */
.reel-cm-overlay { position: fixed; inset: 0; z-index: 300; pointer-events: none; }
.reel-cm-overlay.open { pointer-events: all; }
.reel-cm-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0); transition: background .3s; }
.reel-cm-overlay.open .reel-cm-backdrop { background: rgba(0,0,0,.5); }
.reel-cm-panel {
    position: absolute; top: 0; right: 0; bottom: 0;
    width: 380px; max-width: 100vw; background: #1c1c1c;
    display: flex; flex-direction: column;
    transform: translateX(100%); transition: transform .32s cubic-bezier(.4,0,.2,1);
    box-shadow: -4px 0 32px rgba(0,0,0,.55);
}
.reel-cm-overlay.open .reel-cm-panel { transform: translateX(0); }
.reel-cm-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid rgba(255,255,255,.08); flex-shrink: 0; }
.reel-cm-head h3 { margin: 0; font-size: 15px; font-weight: 700; color: #fff; }
.reel-cm-close { background: none; border: none; cursor: pointer; color: rgba(255,255,255,.6); display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 50%; transition: background .18s; }
.reel-cm-close:hover { background: rgba(255,255,255,.1); color: #fff; }
.reel-cm-list { flex: 1; overflow-y: auto; padding: 6px 0; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.15) transparent; }
.reel-cm-list::-webkit-scrollbar { width: 4px; }
.reel-cm-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 2px; }
.reel-cm-item { display: flex; gap: 10px; padding: 10px 18px; }
.reel-cm-av { width: 36px; height: 36px; border-radius: 50%; background: #fe2c55; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0; }
.reel-cm-body { flex: 1; min-width: 0; }
.reel-cm-uname { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 3px; }
.reel-cm-text { font-size: 13px; color: rgba(255,255,255,.85); line-height: 1.5; word-break: break-word; }
.reel-cm-meta { display: flex; align-items: center; gap: 14px; margin-top: 5px; }
.reel-cm-time { font-size: 11px; color: rgba(255,255,255,.38); }
.reel-cm-lbtn { display: flex; align-items: center; gap: 4px; background: none; border: none; cursor: pointer; color: rgba(255,255,255,.4); font-size: 11px; font-weight: 600; padding: 0; }
.reel-cm-lbtn:hover, .reel-cm-lbtn.liked { color: #fe2c55; }
.reel-cm-lbtn svg { width: 12px; height: 12px; }
.reel-cm-empty { padding: 48px 18px; text-align: center; color: rgba(255,255,255,.3); font-size: 14px; }
.reel-cm-loading { padding: 48px 18px; text-align: center; }
.reel-cm-spinner { width: 26px; height: 26px; border: 3px solid rgba(255,255,255,.1); border-top-color: #fe2c55; border-radius: 50%; animation: rlSpin .7s linear infinite; display: inline-block; }
@keyframes rlSpin { to { transform: rotate(360deg); } }
.reel-cm-footer { flex-shrink: 0; padding: 12px 16px; border-top: 1px solid rgba(255,255,255,.08); background: #1c1c1c; }
.reel-cm-irow { display: flex; align-items: center; gap: 10px; }
.reel-cm-inp { flex: 1; background: rgba(255,255,255,.09); border: 1px solid rgba(255,255,255,.12); border-radius: 100px; padding: 9px 16px; font-size: 14px; color: #fff; outline: none; transition: border-color .18s; }
.reel-cm-inp::placeholder { color: rgba(255,255,255,.35); }
.reel-cm-inp:focus { border-color: rgba(254,44,85,.55); }
.reel-cm-send { width: 38px; height: 38px; border-radius: 50%; background: #fe2c55; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: opacity .18s; }
.reel-cm-send:disabled { opacity: .38; cursor: not-allowed; }
.reel-cm-send svg { width: 15px; height: 15px; }
.reel-cm-login { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: #fe2c55; color: #fff !important; font-size: 14px; font-weight: 700; border-radius: 100px; text-decoration: none !important; }
@media (max-width: 640px) {
    .reel-cm-panel { top: auto; right: 0; left: 0; bottom: 0; width: 100%; max-height: 75dvh; max-height: 75vh; border-radius: 16px 16px 0 0; transform: translateY(100%); }
    .reel-cm-overlay.open .reel-cm-panel { transform: translateY(0); }
}

</style>
@endsection

@section('content')
{{-- Mobile top nav --}}
<nav class="reel-mobile-nav">
    <a href="{{ route('frontend.trending.videos') }}">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
        {{ __('Explore') }}
    </a>
</nav>

@if(count($reels) === 0)
<div class="reels-empty">
    <i class="fas fa-film" style="font-size:3rem;color:#444"></i>
    <p>{{ __('No reels yet. Be the first to add a video to your listing!') }}</p>
</div>
@else
<div class="reels-feed" id="reelsFeed">
    @foreach($reels as $reel)
        @if(isset($reel['type']) && $reel['type'] === 'ad_video')
            @include('frontend.reels._sponsored-reel', ['reel' => $reel])
        @else
            @include('frontend.reels._reel-card', ['reel' => $reel])
        @endif
    @endforeach

    @if($has_more)
    <div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#000;scroll-snap-align:start;">
        <button id="loadMoreBtn" data-page="{{ $page + 1 }}" data-loading="false">
            {{ __('Load More') }}
        </button>
    </div>
    @endif
</div>
@endif

{{-- Shared Comments Drawer --}}
<div class="reel-cm-overlay" id="rlCmOverlay">
    <div class="reel-cm-backdrop" onclick="rlCloseComments()"></div>
    <div class="reel-cm-panel" role="dialog">
        <div class="reel-cm-head">
            <h3>{{ __('Comments') }} <span id="rlCmCount" style="color:rgba(255,255,255,.5);font-weight:400;"></span></h3>
            <button class="reel-cm-close" onclick="rlCloseComments()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="reel-cm-list" id="rlCmList">
            <div class="reel-cm-loading"><span class="reel-cm-spinner"></span></div>
        </div>
        <div class="reel-cm-footer">
            @auth('web')
            <div class="reel-cm-irow">
                <input type="text" id="rlCmInp" class="reel-cm-inp" placeholder="{{ __('Add a comment…') }}" maxlength="500" autocomplete="off">
                <button class="reel-cm-send" id="rlCmSend" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
            @else
            <a href="{{ route('user.login') }}" class="reel-cm-login">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                {{ __('Log in to comment') }}
            </a>
            @endauth
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/reels.js') }}"></script>
<script>
/* ═══════════════════════════════════════════════════════
   SCROLL TO START VIDEO
═══════════════════════════════════════════════════════ */
(function () {
    var startId = {{ (int)($start_id ?? 0) }};
    if (!startId) return;
    function scrollToStart() {
        var el   = document.getElementById('reel-' + startId);
        var feed = document.getElementById('reelsFeed');
        if (el && feed) {
            feed.scrollTop = el.offsetTop;
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scrollToStart);
    } else {
        scrollToStart();
    }
})();

/* ═══════════════════════════════════════════════════════
   SOUND TOGGLE
═══════════════════════════════════════════════════════ */
function rlToggleMute(btn) {
    var wrapper = btn.closest('.reel-wrapper');
    if (!wrapper) return;
    var vid = wrapper.querySelector('video.reel-main-video');
    if (!vid) return;
    vid.muted = !vid.muted;
    /* update all mute/sound icons in this wrapper */
    wrapper.querySelectorAll('.rl-wave, .rl-vol-wave').forEach(function(el) {
        el.style.display = vid.muted ? 'none' : '';
    });
    wrapper.querySelectorAll('.rl-mute-x, .rl-mute-x2').forEach(function(el) {
        el.style.display = vid.muted ? '' : 'none';
    });
    if (!vid.muted) vid.volume = 1;
}

/* ═══════════════════════════════════════════════════════
   TAP PAUSE / PLAY
═══════════════════════════════════════════════════════ */
function rlTapToggle(tapZone) {
    var wrapper = tapZone.closest('.reel-wrapper');
    if (!wrapper) return;
    var vid = wrapper.querySelector('video.reel-main-video');
    var ico = wrapper.querySelector('.reel-play-icon');
    if (!vid) return;
    if (vid.paused) {
        vid.play();
        if (ico) ico.classList.remove('show');
    } else {
        vid.pause();
        if (ico) ico.classList.add('show');
    }
}

/* ═══════════════════════════════════════════════════════
   SEEK (progress bar click)
═══════════════════════════════════════════════════════ */
function rlSeek(bar, e) {
    var wrapper = bar.closest('.reel-wrapper');
    if (!wrapper) return;
    var vid = wrapper.querySelector('video.reel-main-video');
    if (!vid || !vid.duration) return;
    var rect = bar.getBoundingClientRect();
    vid.currentTime = ((e.clientX - rect.left) / rect.width) * vid.duration;
}

/* ═══════════════════════════════════════════════════════
   LIKE TOGGLE
═══════════════════════════════════════════════════════ */
function rlLike(btn) {
    var btns = btn.closest('.reel-wrapper');
    if (!btns) return;
    /* toggle active on all like buttons across ext + mobile in same wrapper */
    btns.querySelectorAll('[onclick^="rlLike"]').forEach(function(b) {
        b.classList.toggle('active');
    });
}

/* ═══════════════════════════════════════════════════════
   SHARE
═══════════════════════════════════════════════════════ */
function rlShare(btn, url) {
    if (navigator.share) {
        navigator.share({ url: url, title: document.title }).catch(function(){});
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function(){
            var orig = btn.querySelector('.rl-ext-count, .rl-mob-lbl');
            if (orig) { var t = orig.textContent; orig.textContent = '{{ __("Copied!") }}'; setTimeout(function(){orig.textContent=t;}, 2000); }
        });
    } else {
        window.prompt('{{ __("Copy link:") }}', url);
    }
}

/* ═══════════════════════════════════════════════════════
   AD ROTATOR
═══════════════════════════════════════════════════════ */
(function initAdRotators() {
    document.querySelectorAll('.reel-ad-rotator').forEach(function(rotator) {
        var slides = Array.from(rotator.querySelectorAll('.reel-ad-slide'));
        if (slides.length < 2) return;
        var current = 0;
        setInterval(function() {
            slides[current].classList.remove('reel-ad-active');
            (function(prev){ setTimeout(function(){ slides[prev].style.display='none'; }, 400); })(current);
            current = (current + 1) % slides.length;
            slides[current].style.display = 'block';
            void slides[current].offsetWidth;
            slides[current].classList.add('reel-ad-active');
        }, 5000);
    });
})();

/* ═══════════════════════════════════════════════════════
   COMMENTS
═══════════════════════════════════════════════════════ */
(function() {
    var _currentReelId = null;
    var _loaded        = false;
    var _sending       = false;
    var IS_AUTH        = {!! Auth::guard('web')->check() ? 'true' : 'false' !!};
    var CSRF           = '{{ csrf_token() }}';

    var overlay  = document.getElementById('rlCmOverlay');
    var list     = document.getElementById('rlCmList');
    var countEl  = document.getElementById('rlCmCount');
    var inp      = document.getElementById('rlCmInp');
    var sendBtn  = document.getElementById('rlCmSend');

    if (inp) {
        inp.addEventListener('input', function(){ if (sendBtn) sendBtn.disabled = inp.value.trim().length === 0; });
        inp.addEventListener('keydown', function(e){ if (e.key==='Enter' && !e.shiftKey){ e.preventDefault(); submitComment(); } });
    }
    if (sendBtn) sendBtn.addEventListener('click', submitComment);

    window.rlOpenComments = function(btn, reelId) {
        _currentReelId = reelId;
        _loaded = false;
        if (overlay) overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        loadComments();
        /* update badge source */
        var badge = btn ? btn.querySelector('.rl-ext-count') : null;
        if (countEl && badge) countEl.textContent = badge.textContent;
    };
    window.rlCloseComments = function() {
        if (overlay) overlay.classList.remove('open');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function(e){
        if (e.key==='Escape' && overlay && overlay.classList.contains('open')) rlCloseComments();
    });

    function loadComments() {
        if (!_currentReelId) return;
        if (list) list.innerHTML = '<div class="reel-cm-loading"><span class="reel-cm-spinner"></span></div>';
        fetch('/reels/' + _currentReelId + '/comments', { headers: {'X-Requested-With':'XMLHttpRequest'} })
            .then(function(r){ return r.json(); })
            .then(function(data){ _loaded = true; renderComments(data.comments, data.total); })
            .catch(function(){ if (list) list.innerHTML = '<div class="reel-cm-empty">{{ __("Could not load comments.") }}</div>'; });
    }

    function renderComments(comments, total) {
        var t = total || 0;
        if (countEl) countEl.textContent = t;
        if (!comments || comments.length === 0) {
            if (list) list.innerHTML = '<div class="reel-cm-empty">{{ __("No comments yet. Be the first!") }}</div>';
            return;
        }
        var html = '';
        comments.forEach(function(c) {
            html += '<div class="reel-cm-item">' +
                '<div class="reel-cm-av">' + esc(c.initial) + '</div>' +
                '<div class="reel-cm-body">' +
                    '<div class="reel-cm-uname">' + esc(c.user_name) + '</div>' +
                    '<div class="reel-cm-text">' + esc(c.body) + '</div>' +
                    '<div class="reel-cm-meta">' +
                        '<span class="reel-cm-time">' + esc(c.created_at) + '</span>' +
                        '<button class="reel-cm-lbtn" data-liked="0">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>' +
                            '<span>' + (c.likes||0) + '</span>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });
        if (list) list.innerHTML = html;
    }

    if (list) {
        list.addEventListener('click', function(e) {
            var btn = e.target.closest('.reel-cm-lbtn');
            if (!btn) return;
            var liked = btn.dataset.liked === '1';
            var span = btn.querySelector('span');
            var n = parseInt(span.textContent, 10) || 0;
            btn.dataset.liked = liked ? '0' : '1';
            btn.classList.toggle('liked', !liked);
            span.textContent = liked ? Math.max(0, n-1) : n+1;
        });
    }

    function submitComment() {
        if (!IS_AUTH || _sending || !_currentReelId) return;
        var body = inp ? inp.value.trim() : '';
        if (!body) return;
        _sending = true;
        if (sendBtn) sendBtn.disabled = true;
        fetch('/reels/' + _currentReelId + '/comments', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With':'XMLHttpRequest' },
            body: JSON.stringify({ body: body }),
        })
        .then(function(r){ return r.json(); })
        .then(function(c) {
            if (inp) inp.value = '';
            if (sendBtn) sendBtn.disabled = true;
            var item = document.createElement('div');
            item.className = 'reel-cm-item';
            item.innerHTML = '<div class="reel-cm-av">' + esc(c.initial) + '</div><div class="reel-cm-body"><div class="reel-cm-uname">' + esc(c.user_name) + '</div><div class="reel-cm-text">' + esc(c.body) + '</div><div class="reel-cm-meta"><span class="reel-cm-time">just now</span></div></div>';
            var empty = list && list.querySelector('.reel-cm-empty');
            if (empty) empty.remove();
            if (list) list.insertBefore(item, list.firstChild);
            var prev = parseInt((countEl && countEl.textContent) || 0, 10);
            if (countEl) countEl.textContent = prev + 1;
        })
        .catch(function(){ alert('{{ __("Failed to post comment.") }}'); })
        .finally(function(){ _sending = false; });
    }

    function esc(s) {
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();

/* ═══════════════════════════════════════════════════════
   LOAD MORE
═══════════════════════════════════════════════════════ */
(function() {
    var loadBtn = document.getElementById('loadMoreBtn');
    if (!loadBtn) return;
    loadBtn.addEventListener('click', function () {
        if (loadBtn.dataset.loading === 'true') return;
        loadBtn.dataset.loading = 'true';
        loadBtn.textContent = '{{ __("Loading...") }}';
        loadBtn.disabled = true;
        var page = parseInt(loadBtn.dataset.page, 10);
        fetch('/reels/load?page=' + page, { headers: {'X-Requested-With':'XMLHttpRequest'} })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                if (data.has_more) {
                    loadBtn.dataset.page    = page + 1;
                    loadBtn.dataset.loading = 'false';
                    loadBtn.textContent     = '{{ __("Load More") }}';
                    loadBtn.disabled        = false;
                } else {
                    loadBtn.closest('div').remove();
                }
            })
            .catch(function() {
                loadBtn.dataset.loading = 'false';
                loadBtn.textContent     = '{{ __("Load More") }}';
                loadBtn.disabled        = false;
            });
    });
})();
</script>
@endsection
