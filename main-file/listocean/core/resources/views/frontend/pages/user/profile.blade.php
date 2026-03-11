@extends('frontend.layout.master')
@section('site-title')
    {{ __('User Profile') }}
@endsection
@section('style')
<style>
/* ── Profile Hero ── */
.profile-hero {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    padding: 28px 28px 22px;
    margin-bottom: 24px;
}
.profile-avatar-wrap {
    position: relative;
    width: 96px;
    flex-shrink: 0;
}
.profile-avatar-wrap img {
    width: 96px;
    height: 96px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #f1f5f9;
}
.profile-avatar-initials {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fe2c55, #ff6b35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 700;
    color: #fff;
    border: 3px solid #f1f5f9;
}
.verification-badge-approved {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #22c55e;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    margin-top: 4px;
}
.verification-badge-pending {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f97316;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    margin-top: 4px;
}
.verification-badge-get {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #e2e8f0;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
    margin-top: 4px;
}
.profile-stats-bar {
    display: flex;
    gap: 24px;
    margin-top: 18px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}
.profile-stat-item {
    text-align: center;
    min-width: 70px;
}
.profile-stat-item .stat-number {
    font-size: 22px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.1;
}
.profile-stat-item .stat-label {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 2px;
}
.profile-stat-divider {
    width: 1px;
    background: #e2e8f0;
    align-self: stretch;
}
/* ── Profile Contact ── */
.profile-contact-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 16px;
}
.profile-contact-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    color: #64748b;
}
.profile-contact-item svg { flex-shrink: 0; opacity: .7; }
/* ── Tabs ── */
.profile-tabs {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 24px;
}
.profile-tab-btn {
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    cursor: pointer;
    transition: color .15s, border-color .15s;
    display: flex;
    align-items: center;
    gap: 6px;
}
.profile-tab-btn.active {
    color: #fe2c55;
    border-bottom-color: #fe2c55;
}
.profile-tab-btn .tab-count {
    background: #f1f5f9;
    color: #94a3b8;
    font-size: 11px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 20px;
}
.profile-tab-btn.active .tab-count {
    background: #fee2e2;
    color: #fe2c55;
}
.profile-tab-pane { display: none; }
.profile-tab-pane.active { display: block; }
/* ── Videos Grid ── */
.profile-videos-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}
@media (max-width: 768px) {
    .profile-videos-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
    .profile-videos-grid { grid-template-columns: 1fr; }
}
.video-card {
    border-radius: 12px;
    overflow: hidden;
    background: #000;
    display: block;
    text-decoration: none;
    position: relative;
    aspect-ratio: 16/9;
}
.video-card img,
.video-card video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .3s;
    opacity: .9;
}
.video-card:hover img,
.video-card:hover video { transform: scale(1.04); opacity: 1; }
.video-card-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,.25);
    transition: background .2s;
}
.video-card:hover .video-card-overlay { background: rgba(0,0,0,.45); }
.video-play-btn {
    width: 52px;
    height: 52px;
    background: rgba(255,255,255,.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 12px rgba(0,0,0,.4);
}
.video-play-btn svg { color: #fe2c55; margin-left: 4px; }
.video-card-title {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 24px 12px 10px;
    background: linear-gradient(transparent, rgba(0,0,0,.75));
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.3;
}
.no-videos-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}
.no-videos-state svg { opacity: .3; margin-bottom: 12px; }
.no-videos-state p { font-size: 15px; }
/* ── Rating action ── */
.rate-seller-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: #fe2c55;
    color: #fff;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: background .15s;
}
.rate-seller-btn:hover { background: #e0193f; color: #fff; }
</style>
@endsection
@section('content')
    <div class="user-profile section-padding2">
        <div class="container-1920 plr1">
            <div class="container-1492 mx-auto">
                <x-breadcrumb.user-profile-breadcrumb
                    :title="''"
                    :innerTitle="__('User Profile')"
                    :subInnerTitle="''"
                    :chidInnerTitle="''"
                    :routeName="'#'"
                    :subRouteName="'#'"
                />

                {{-- ── Profile Hero Card ── --}}
                <div class="profile-hero">
                    <div class="d-flex align-items-start gap-4 flex-wrap">
                        {{-- Avatar --}}
                        <div class="profile-avatar-wrap">
                            @if(!empty($user->image))
                                {!! render_image_markup_by_attachment_id($user->image, '', 'thumbnail') !!}
                            @else
                                <div class="profile-avatar-initials">
                                    {{ strtoupper(substr($user->fullname ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                <h2 class="mb-0" style="font-size:22px;font-weight:700;color:#1e293b;">{{ $user->fullname }}</h2>
                                <x-badge.user-verified-badge :user="$user"/>
                            </div>

                            {{-- Verification call-to-action badge (read-only info for visitors) --}}
                            @if(!is_null($user->identity_verify) && $user->identity_verify->status === 1)
                                <span class="verification-badge-approved">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5"/></svg>
                                    {{ __('Verified Seller') }}
                                </span>
                            @elseif(!is_null($user->identity_verify) && is_null($user->identity_verify->status))
                                <span class="verification-badge-pending">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 8v4l2.5 2.5"/></svg>
                                    {{ __('Verification Pending') }}
                                </span>
                            @else
                                <span class="verification-badge-get">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2Zm0 6v4m0 4h.01"/></svg>
                                    {{ __('Not Verified') }}
                                </span>
                            @endif

                            {{-- Rating row --}}
                            <div class="d-flex align-items-center flex-wrap gap-3 mt-2">
                                @if($averageRating >= 1)
                                    <span>{!! ratting_star(round($averageRating, 1)) !!}</span>
                                    <span style="font-size:13px;color:#64748b;">({{ $user_review_count }} {{ __('reviews') }})</span>
                                @endif
                                <a href="javascript:void(0)" class="rate-seller-btn review_add_modal"
                                   data-bs-toggle="modal" data-bs-target="#reviewModal"
                                   data-user_id="{{ $user->id }}">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                                    {{ __('Rate this Seller') }}
                                </a>
                            </div>

                            {{-- Contact info --}}
                            <div class="profile-contact-row">
                                @if(!empty(userProfileLocation($user)))
                                <div class="profile-contact-item">
                                    <svg width="14" height="15" viewBox="0 0 16 17" fill="none"><path d="M5.5 7.167a2.5 2.5 0 1 0 5 0 2.5 2.5 0 0 0-5 0Z" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.714 11.881 9.178 15.417a1.667 1.667 0 0 1-2.356 0L3.286 11.88A6.667 6.667 0 1 1 12.714 11.88Z" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span>{!! userProfileLocation($user) !!}</span>
                                </div>
                                @endif
                                @if(!empty($user->email))
                                <div class="profile-contact-item">
                                    <svg width="14" height="12" viewBox="0 0 16 14" fill="none"><path d="M.5 2.833A1.667 1.667 0 0 1 2.167 1.167h11.666A1.667 1.667 0 0 1 15.5 2.833M.5 2.833v8.334A1.667 1.667 0 0 0 2.167 12.833h11.666A1.667 1.667 0 0 0 15.5 11.167V2.833M.5 2.833 8 7.833l7.5-5" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span>{{ $user->email }}</span>
                                </div>
                                @endif
                                @if(!empty($user->phone))
                                <div class="profile-contact-item">
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M2.167 1.333H5.5l1.667 4.167-2.084 1.25a8.333 8.333 0 0 0 4.167 4.167l1.25-2.084 4.167 1.667v3.333A1.667 1.667 0 0 1 13 15.5 13.333 13.333 0 0 1 .5 3a1.667 1.667 0 0 1 1.667-1.667Z" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span>{{ $user->phone }}</span>
                                </div>
                                @endif
                                <div class="profile-contact-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="2" stroke="#1E293B" stroke-width="1.5"/><path d="M3 9h18" stroke="#1E293B" stroke-width="1.5" stroke-linecap="round"/><path d="M8 2v3M16 2v3" stroke="#1E293B" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    <span>{{ __('Member since') }} {{ optional($user->created_at)->format('Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Bar --}}
                    <div class="profile-stats-bar">
                        <div class="profile-stat-item">
                            <div class="stat-number">{{ $userListings->total() }}</div>
                            <div class="stat-label">{{ __('Listings') }}</div>
                        </div>
                        <div class="profile-stat-divider"></div>
                        <div class="profile-stat-item">
                            <div class="stat-number">{{ $user_review_count }}</div>
                            <div class="stat-label">{{ __('Reviews') }}</div>
                        </div>
                        <div class="profile-stat-divider"></div>
                        <div class="profile-stat-item">
                            <div class="stat-number">{{ $userVideos->count() }}</div>
                            <div class="stat-label">{{ __('Videos') }}</div>
                        </div>
                        @if($averageRating >= 1)
                        <div class="profile-stat-divider"></div>
                        <div class="profile-stat-item">
                            <div class="stat-number">{{ number_format($averageRating, 1) }}</div>
                            <div class="stat-label">{{ __('Avg. Rating') }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ── Ad Slots ── --}}
                <x-ads.ad-slot placement="user_profile_under_header" class="mb-4" />
                @if(get_static_option('google_adsense_status') == 'on' && !empty($add_markup))
                    <div class="googleAdd-wraper mb-4">
                        <div class="text-{{$custom_container}} single-banner-ads ads-banner-box" id="home_advertisement_store">
                            <input type="hidden" id="add_id" value="{{$add_id}}">
                            {!! $add_markup !!}
                        </div>
                    </div>
                @endif

                {{-- ── Tabs ── --}}
                <div class="profile-tabs" id="profileTabs">
                    <button class="profile-tab-btn active" data-tab="listings">
                        {{ __('Listings') }}
                        <span class="tab-count">{{ $userListings->total() }}</span>
                    </button>
                    <button class="profile-tab-btn" data-tab="videos">
                        {{ __('Videos') }}
                        <span class="tab-count">{{ $userVideos->count() }}</span>
                    </button>
                    <button class="profile-tab-btn" data-tab="reviews">
                        {{ __('Reviews') }}
                        <span class="tab-count">{{ $user_review_count }}</span>
                    </button>
                </div>

                {{-- Ad slot: between profile tabs bar and content --}}
                <x-ads.ad-slot placement="user_profile_between_tabs" class="mb-3" />

                {{-- ── Listings Tab ── --}}
                <div class="profile-tab-pane active" id="tab-listings">
                    <div class="relevant-ads all-listings box-shadow1">
                        <div class="add-wraper">
                            <x-listings.relevant-ads-view :listings="$userListings"/>
                        </div>
                    </div>
                </div>

                {{-- ── Videos Tab ── --}}
                <div class="profile-tab-pane" id="tab-videos">
                    @if($userVideos->isEmpty())
                        <div class="no-videos-state">
                            <svg width="64" height="64" fill="none" viewBox="0 0 24 24"><rect x="2" y="4" width="15" height="16" rx="2" stroke="#94a3b8" stroke-width="1.5"/><path d="M17 9l5-3v12l-5-3V9z" stroke="#94a3b8" stroke-width="1.5" stroke-linejoin="round"/></svg>
                            <p>{{ __('No videos from this seller yet.') }}</p>
                        </div>
                    @else
                        <div class="profile-videos-grid">
                            @foreach($userVideos as $video)
                                @php
                                    $watchUrl = route('frontend.reel.watch', $video->id);
                                @endphp
                                <a href="{{ $watchUrl }}" class="video-card">
                                    <video src="{{ $video->video_url }}" muted autoplay loop playsinline preload="metadata"
                                           style="width:100%;height:100%;object-fit:cover;"></video>
                                    <div class="video-card-overlay">
                                        <div class="video-play-btn">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#fe2c55"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <div class="video-card-title">{{ Str::limit($video->title, 55) }}</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Reviews Tab ── --}}
                <div class="profile-tab-pane" id="tab-reviews">
                    <div class="all-reviews box-shadow1">
                        <div class="review-wraper">
                            <x-user.user-reviews :reviews="$user->reviews" :user="$user" :reviewtype="''"/>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @include('frontend.pages.user.review-add-modal')
@endsection
@section('scripts')
    <script src="{{ asset('assets/frontend/js/rating.js') }}"></script>
    <x-listings.user-review-add-js/>
    <script>
    (function () {
        const tabs = document.querySelectorAll('#profileTabs .profile-tab-btn');

        function handleTabSwitch(activeTab) {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            document.querySelectorAll('.profile-tab-pane').forEach(function (p) { p.classList.remove('active'); });
            activeTab.classList.add('active');
            var pane = document.getElementById('tab-' + activeTab.dataset.tab);
            pane.classList.add('active');

            // Auto-play/pause videos based on active tab
            var allVids = document.querySelectorAll('#tab-videos video');
            if (activeTab.dataset.tab === 'videos') {
                allVids.forEach(function(v) { v.play().catch(function(){}); });
            } else {
                allVids.forEach(function(v) { v.pause(); });
            }
        }

        tabs.forEach(function (btn) {
            btn.addEventListener('click', function () {
                handleTabSwitch(btn);
            });
        });

        // If URL hash matches a tab, activate it
        var hash = window.location.hash.replace('#', '');
        if (hash && document.getElementById('tab-' + hash)) {
            var activeBtn = document.querySelector('[data-tab="' + hash + '"]');
            if (activeBtn) handleTabSwitch(activeBtn);
        }

        // Pause videos on initial load if Videos tab is not active
        var videosTab = document.querySelector('[data-tab="videos"]');
        if (videosTab && !videosTab.classList.contains('active')) {
            document.querySelectorAll('#tab-videos video').forEach(function(v) { v.pause(); });
        }
    })();
    </script>
@endsection

