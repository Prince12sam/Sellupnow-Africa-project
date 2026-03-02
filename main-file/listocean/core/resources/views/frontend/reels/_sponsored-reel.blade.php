{{--
    _sponsored-reel.blade.php  —  TikTok-style sponsored / promo video card.

    Expected $reel keys:
        type, id, title, video_url, thumbnail, cta_text, cta_url, sponsored, ad_overlays
--}}
@php
    $reelId   = $reel['id'];
    $isYt     = !empty($reel['video_url']) && (
        str_contains($reel['video_url'], 'youtube.com') ||
        str_contains($reel['video_url'], 'youtu.be')
    );
    $videoUrl = $reel['video_url'] ?? '';
    $ctaUrl   = $reel['cta_url'] ?? '#';
    $ctaText  = $reel['cta_text'] ?? __('Learn More');
@endphp

<div class="reel-wrapper" id="reel-{{ $reelId }}" data-reel-id="{{ $reelId }}" data-type="ad_video">

    <div class="reel-center">

        <div class="reel-player-wrap">

            {{-- Blurred bg INSIDE card to fill letterbox areas (TikTok technique) --}}
            @if(!$isYt)
            <video class="reel-card-bg" src="{{ $videoUrl }}" muted autoplay loop playsinline preload="none"></video>
            @endif

            <div class="reel-gradient-top"></div>
            <div class="reel-gradient"></div>

            {{-- Sponsored badge --}}
            <div class="sponsored-badge">{{ __('Sponsored') }}</div>

            {{-- Sound toggle --}}
            <button class="reel-sound-btn" onclick="rlToggleMute(this)" title="{{ __('Toggle sound') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                    <path class="rl-wave" d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/>
                    <path class="rl-mute-x" d="M23 9l-6 6M17 9l6 6" style="display:none;"/>
                </svg>
            </button>

            @if($isYt)
                <iframe
                    class="reel-main-video"
                    src="{{ $videoUrl }}?autoplay=0&mute=1&loop=1&controls=0&modestbranding=1"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                    loading="lazy"
                    title="{{ $reel['title'] }}"
                    style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:1;"
                ></iframe>
            @else
                <video
                    class="reel-main-video"
                    src="{{ $videoUrl }}"
                    @if(!empty($reel['thumbnail'])) poster="{{ $reel['thumbnail'] }}" @endif
                    loop muted playsinline preload="none"
                ></video>
            @endif

            <div class="reel-tap-zone" onclick="rlTapToggle(this)"></div>
            <div class="reel-play-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="11" fill="rgba(0,0,0,.42)"/>
                    <polygon points="10,7.5 10,16.5 18,12" fill="#fff"/>
                </svg>
            </div>

            {{-- Mobile actions --}}
            <div class="reel-actions-mobile">
                <button class="rl-mob-btn" onclick="rlShare(this, '{{ $ctaUrl }}')">
                    <div class="rl-mob-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    </div>
                    <span class="rl-mob-lbl">{{ __('Share') }}</span>
                </button>
                @if($ctaUrl && $ctaUrl !== '#')
                <a href="{{ $ctaUrl }}" class="rl-mob-btn" target="_blank" rel="noopener sponsored">
                    <div class="rl-mob-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </div>
                    <span class="rl-mob-lbl">{{ $ctaText }}</span>
                </a>
                @endif
            </div>

            {{-- Ad overlays --}}
            @if(!empty($reel['ad_overlays']))
                <div class="reel-ad-rotator" data-reel-id="{{ $reelId }}">
                    @foreach($reel['ad_overlays'] as $__i => $__ad)
                        @php
                            $__adSrc = filter_var($__ad['image'], FILTER_VALIDATE_URL)
                                ? $__ad['image']
                                : asset('storage/' . ltrim($__ad['image'], '/'));
                        @endphp
                        <div class="reel-ad-slide{{ $__i === 0 ? ' reel-ad-active' : '' }}">
                            <span class="reel-ad-label">{{ __('Ad') }}</span>
                            @if(!empty($__ad['redirect_url']))
                                <a href="{{ $__ad['redirect_url'] }}" target="_blank" rel="noopener sponsored" style="display:block;">
                                    <div class="reel-ad-img-wrap">
                                        <img src="{{ $__adSrc }}" alt="{{ $__ad['title'] ?? __('Advertisement') }}" class="reel-ad-img">
                                    </div>
                                </a>
                            @else
                                <div class="reel-ad-img-wrap">
                                    <img src="{{ $__adSrc }}" alt="{{ $__ad['title'] ?? __('Advertisement') }}" class="reel-ad-img">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Info overlay --}}
            <div class="reel-info">
                <div class="reel-card-title">{{ e(Str::limit($reel['title'], 80)) }}</div>
                @if($ctaUrl && $ctaUrl !== '#')
                    <a href="{{ $ctaUrl }}" target="_blank" rel="noopener sponsored"
                       style="display:inline-block;margin-top:8px;padding:8px 20px;background:#fe2c55;color:#fff;border-radius:100px;font-size:13px;font-weight:700;text-decoration:none;">
                        {{ $ctaText }}
                    </a>
                @endif
            </div>

            <div class="reel-controls-strip">
                <button class="rl-vol-btn" onclick="rlToggleMute(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                        <path d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/>
                    </svg>
                </button>
                <div class="reel-progress" onclick="rlSeek(this, event)">
                    <div class="reel-progress-fill"></div>
                </div>
                <span class="rl-time">0:00</span>
            </div>

        </div>{{-- /.reel-player-wrap --}}

        {{-- Desktop side actions --}}
        <div class="reel-ext-actions">
            <button class="rl-ext-btn" onclick="rlShare(this, '{{ $ctaUrl }}')">
                <div class="rl-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                </div>
                <span class="rl-ext-count">{{ __('Share') }}</span>
            </button>
            @if($ctaUrl && $ctaUrl !== '#')
            <a href="{{ $ctaUrl }}" class="rl-ext-btn" target="_blank" rel="noopener sponsored">
                <div class="rl-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </div>
                <span class="rl-ext-count">{{ $ctaText }}</span>
            </a>
            @endif
        </div>

    </div>{{-- /.reel-center --}}
</div>{{-- /.reel-wrapper --}}

