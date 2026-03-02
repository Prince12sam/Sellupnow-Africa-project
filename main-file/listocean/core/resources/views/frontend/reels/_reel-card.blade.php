{{--
    _reel-card.blade.php  —  TikTok-style feed card.

    Expected $reel keys:
        type, id, title, slug, image, video_url, price,
        seller, seller_photo, sponsored, ad_overlays, view_count (optional)
--}}
@php
    $reelId        = $reel['id'];
    $isYt          = !empty($reel['video_url']) && (
        str_contains($reel['video_url'], 'youtube.com') ||
        str_contains($reel['video_url'], 'youtu.be')
    );
    $videoUrl      = $reel['video_url'] ?? '';
    $sellerName    = $reel['seller'] ?? '';
    $sellerInitial = strtoupper(substr($sellerName ?: 'S', 0, 1));
    // listing_url override lets non-listing reels (user_video type) supply a precomputed URL
    $listingUrl    = $reel['listing_url'] ?? route('frontend.listing.details', $reel['slug'] ?? $reel['id']);
    // poster_url override lets user_video reels use a fully-resolved thumbnail URL
    $poster        = $reel['poster_url'] ?? (!empty($reel['image']) ? asset('storage/' . $reel['image']) : null);
    $viewCount     = $reel['view_count'] ?? 0;
@endphp

<div class="reel-wrapper" id="reel-{{ $reelId }}" data-reel-id="{{ $reelId }}" data-type="{{ $reel['type'] ?? 'listing' }}">

    {{-- Center layout: player column + side actions column --}}
    <div class="reel-center">

        {{-- ── VIDEO PLAYER WRAP ── --}}
        <div class="reel-player-wrap">

            {{-- Blurred bg INSIDE card to fill letterbox areas (TikTok technique) --}}
            @if(!$isYt)
            <video class="reel-card-bg" src="{{ $videoUrl }}" muted autoplay loop playsinline preload="none"></video>
            @endif

            <div class="reel-gradient-top"></div>
            <div class="reel-gradient"></div>

            {{-- Back to Explore (hidden on mobile, shown on desktop) --}}
            <a href="{{ route('frontend.trending.videos') }}" class="reel-back-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                {{ __('Explore') }}
            </a>

            {{-- Sound toggle --}}
            <button class="reel-sound-btn" onclick="rlToggleMute(this)" title="{{ __('Toggle sound') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                    <path class="rl-wave" d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/>
                    <path class="rl-mute-x" d="M23 9l-6 6M17 9l6 6" style="display:none;"/>
                </svg>
            </button>

            {{-- Sponsored badge --}}
            @if($reel['sponsored'] ?? false)
                <div class="sponsored-badge">{{ __('Sponsored') }}</div>
            @endif

            {{-- Video or YouTube embed --}}
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
                    @if($poster) poster="{{ $poster }}" @endif
                    loop muted playsinline preload="none"
                ></video>
            @endif

            {{-- Tap to pause / play --}}
            <div class="reel-tap-zone" onclick="rlTapToggle(this)"></div>
            <div class="reel-play-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="11" fill="rgba(0,0,0,.42)"/>
                    <polygon points="10,7.5 10,16.5 18,12" fill="#fff"/>
                </svg>
            </div>

            {{-- Mobile in-player actions (visible on mobile only) --}}
            <div class="reel-actions-mobile">
                <button class="rl-mob-btn" onclick="rlLike(this)">
                    <div class="rl-mob-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                    </div>
                    <span class="rl-mob-lbl">{{ $viewCount }}</span>
                </button>
                <button class="rl-mob-btn" onclick="rlOpenComments(this, {{ $reelId }})">
                    <div class="rl-mob-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </div>
                    <span class="rl-mob-lbl rl-cm-badge">0</span>
                </button>
                <button class="rl-mob-btn" onclick="rlShare(this, '{{ $listingUrl }}')">
                    <div class="rl-mob-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    </div>
                    <span class="rl-mob-lbl">{{ __('Share') }}</span>
                </button>
                <a href="{{ $listingUrl }}" class="rl-mob-btn">
                    <div class="rl-mob-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </div>
                    <span class="rl-mob-lbl">{{ __('View') }}</span>
                </a>
            </div>

            {{-- Ad overlays — rotate every 5s --}}
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

            {{-- Bottom info overlay --}}
            <div class="reel-info">
                @if(!empty($reel['price']))
                    <a href="{{ $listingUrl }}" class="reel-price-tag" style="text-decoration:none;">
                        {{ amount_with_currency_symbol($reel['price']) }}
                    </a>
                @endif
                <a href="{{ $listingUrl }}" class="reel-card-title" style="text-decoration:none;color:inherit;display:block;">{{ e(Str::limit($reel['title'], 80)) }}</a>
            </div>

            {{-- Controls strip: volume, progress bar, time --}}
            <div class="reel-controls-strip">
                <button class="rl-vol-btn" onclick="rlToggleMute(this)" title="{{ __('Volume') }}">
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

        {{-- ── DESKTOP SIDE ACTIONS ── --}}
        <div class="reel-ext-actions">

            <div class="rl-ext-avatar-wrap">
                <a href="{{ $listingUrl }}" class="rl-ext-avatar">{{ $sellerInitial }}</a>
            </div>

            <button class="rl-ext-btn" onclick="rlLike(this)">
                <div class="rl-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                </div>
                <span class="rl-ext-count">{{ $viewCount }}</span>
            </button>

            <button class="rl-ext-btn" onclick="rlOpenComments(this, {{ $reelId }})">
                <div class="rl-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <span class="rl-ext-count rl-cm-badge">0</span>
            </button>

            <button class="rl-ext-btn" onclick="rlShare(this, '{{ $listingUrl }}')">
                <div class="rl-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                </div>
                <span class="rl-ext-count">{{ __('Share') }}</span>
            </button>

            <a href="{{ $listingUrl }}" class="rl-ext-btn">
                <div class="rl-ext-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </div>
                <span class="rl-ext-count">{{ __('View') }}</span>
            </a>

        </div>{{-- /.reel-ext-actions --}}

    </div>{{-- /.reel-center --}}
</div>{{-- /.reel-wrapper --}}

