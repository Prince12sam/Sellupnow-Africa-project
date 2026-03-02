@extends('frontend.layout.master')
@section('site_title')
    {{ __('My Videos') }}
@endsection
@section('style')
    <style>
        /* ── Card shell ── */
        .video-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            overflow: hidden;
            transition: box-shadow .18s, transform .18s;
        }
        .video-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.08); transform: translateY(-2px); }

        /* ── 16:9 thumbnail wrap ── */
        .video-thumb-wrap {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            background: #0f172a;
            overflow: hidden;
        }
        .video-thumb-wrap video,
        .video-thumb-wrap img {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%; object-fit: cover;
        }
        .video-thumb-wrap .play-overlay {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,.28);
            transition: background .15s;
        }
        .video-card:hover .play-overlay { background: rgba(0,0,0,.18); }

        /* ── Body ── */
        .video-body { padding: 14px 16px 16px; }
        .video-caption { font-size: 14px; font-weight: 600; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .video-listing-tag { font-size: 12px; color: #64748b; margin-bottom: 10px; }
        .video-listing-tag a { color: inherit; text-decoration: none; }
        .video-listing-tag a:hover { text-decoration: underline; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 64px 20px; color: #94a3b8; }
        .empty-state svg { margin-bottom: 18px; opacity: .35; }

        /* ── Delete modal ── */
        .del-modal-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: #fef2f2; display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .del-modal-icon svg { width: 30px; height: 30px; }
        #deleteVideoModal .modal-content {
            border: none; border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.15);
        }
        #deleteVideoModal .modal-header { border-bottom: none; padding-bottom: 0; }
        #deleteVideoModal .modal-footer { border-top: none; padding-top: 0; }
        .del-thumb-preview {
            width: 100%; border-radius: 8px; overflow: hidden;
            background: #0f172a; max-height: 140px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 10px;
        }
        .del-thumb-preview img,
        .del-thumb-preview video {
            width: 100%; height: 140px; object-fit: cover;
        }
    </style>
@endsection
@section('content')
    <div class="profile-setting my-videos section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>

                                {{-- Header bar --}}
                                <div class="relevant-ads box-shadow1 p-24 mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <h4 class="mb-1">{{ __('My Videos') }}
                                                <span class="badge bg-secondary ms-2" style="font-size:13px;">{{ $videos->total() }}</span>
                                            </h4>
                                            @if($videoQuota === -1)
                                                <p class="text-muted mb-0" style="font-size:13px;">{{ __('Unlimited uploads on your plan') }}</p>
                                            @elseif($videoQuota > 0)
                                                <p class="text-muted mb-0" style="font-size:13px;">{{ $videoUsed }} / {{ $videoQuota }} {{ __('videos used') }}</p>
                                            @endif
                                        </div>
                                        @if($videoQuota !== 0 && ($videoQuota === -1 || $videoUsed < $videoQuota))
                                            <a href="{{ route('user.my.videos.create') }}" class="red-btn">
                                                <i class="las la-plus me-1"></i> {{ __('Upload New Video') }}
                                            </a>
                                        @else
                                            <span class="badge bg-warning text-dark p-2" style="font-size:13px;">{{ __('Video limit reached — upgrade your plan') }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if(session('success'))<div class="alert alert-success mb-3">{{ session('success') }}</div>@endif
                                @if(session('error'))<div class="alert alert-danger mb-3">{{ session('error') }}</div>@endif

                                @if($videos->count() > 0)
                                    <div class="row g-3">
                                        @foreach($videos as $video)
                                            @php
                                                $status   = $video->moderationStatus();
                                                $videoSrc = $video->video_url;   // already a full URL
                                                $thumbSrc = $video->thumbnail_url; // already a full URL or null
                                            @endphp
                                            <div class="col-12 col-sm-6 col-lg-4">
                                                <div class="video-card h-100 d-flex flex-column">
{{-- Thumbnail — clicking the play overlay opens the video --}}
                                    <div class="video-thumb-wrap">
                                        @if($thumbSrc)
                                            <img src="{{ $thumbSrc }}" alt="{{ $video->caption }}">
                                        @else
                                            <video src="{{ $videoSrc }}" preload="metadata"></video>
                                        @endif
                                        <a href="{{ $videoSrc }}" target="_blank" class="play-overlay" style="text-decoration:none;">
                                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none">
                                                <circle cx="12" cy="12" r="11" fill="rgba(0,0,0,0.48)"/>
                                                <path d="M9.5 7.5l7 4.5-7 4.5V7.5z" fill="rgba(255,255,255,0.9)"/>
                                            </svg>
                                        </a>
                                                        <span class="badge {{ $status['class'] }}" style="position:absolute;top:8px;left:8px;font-size:11px;">{{ __($status['label']) }}</span>
                                                    </div>

                                                    {{-- Body --}}
                                                    <div class="video-body flex-grow-1 d-flex flex-column">
                                                        <div class="video-caption">{{ $video->caption ?: __('(No caption)') }}</div>
                                                        @if($video->listing)
                                                            <div class="video-listing-tag">
                                                                <i class="las la-tag me-1"></i>
                                                                <a href="{{ route('frontend.listing.details', $video->listing->slug) }}" target="_blank">{{ Str::limit($video->listing->title, 40) }}</a>
                                                            </div>
                                                        @else
                                                            <div class="video-listing-tag">{{ __('No listing tagged') }}</div>
                                                        @endif
                                                        @if($video->is_rejected && $video->reject_reason)
                                                            <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:12px;"><strong>{{ __('Rejected') }}:</strong> {{ $video->reject_reason }}</div>
                                                        @endif

                                                        {{-- Actions --}}
                                                        <div class="mt-auto d-flex gap-2 pt-2">
                                                            <a href="{{ $videoSrc }}" target="_blank"
                                                               class="btn btn-outline-secondary btn-sm">
                                                                <i class="las la-play me-1"></i>{{ __('Preview') }}
                                                            </a>
                                                            <a href="{{ route('user.my.videos.edit', $video->id) }}"
                                                               class="btn btn-outline-primary btn-sm">
                                                                <i class="las la-edit"></i>
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    title="{{ __('Delete') }}"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteVideoModal"
                                                                    data-delete-url="{{ route('user.my.videos.destroy', $video->id) }}"
                                                                    data-caption="{{ e($video->caption ?: __('(No caption)')) }}"
                                                                    data-thumb="{{ $thumbSrc ?? '' }}"
                                                                    data-video="{{ $videoSrc }}">
                                                                <i class="las la-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-4">{{ $videos->links() }}</div>
                                @else
                                    <div class="relevant-ads box-shadow1 p-24">
                                        <div class="empty-state">
                                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none"><path d="M15 10l4.553-2.277A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <h5>{{ __("You haven't uploaded any videos yet") }}</h5>
                                            <p class="text-muted">{{ __('Upload a short video to promote your listings and attract more buyers.') }}</p>
                                            @if($videoQuota !== 0)
                                                <a href="{{ route('user.my.videos.create') }}" class="red-btn mt-2 d-inline-block">{{ __('Upload Your First Video') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Delete confirmation modal ── --}}
    <div class="modal fade" id="deleteVideoModal" tabindex="-1" aria-labelledby="deleteVideoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content">
                <div class="modal-header pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body text-center px-4 pt-1 pb-2">
                    {{-- Icon --}}
                    <div class="del-modal-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                            <path d="M10 11v6M14 11v6"/>
                            <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                        </svg>
                    </div>
                    {{-- Thumbnail preview --}}
                    <div class="del-thumb-preview" id="delThumbWrap">
                        <img id="delThumbImg" src="" alt="" style="display:none;">
                        <video id="delThumbVideo" src="" preload="metadata" style="display:none;"></video>
                    </div>
                    <h5 class="fw-bold mb-1">{{ __('Delete this video?') }}</h5>
                    <p id="delCaption" class="text-muted mb-0" style="font-size:13px;"></p>
                    <p class="text-muted mt-2" style="font-size:12px;">{{ __('This action cannot be undone. The file will be permanently removed.') }}</p>
                </div>
                <div class="modal-footer justify-content-center gap-2 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        {{ __('Keep it') }}
                    </button>
                    <form method="POST" id="deleteVideoForm" action="">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="las la-trash-alt me-1"></i>{{ __('Yes, delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
(function () {
    var modal = document.getElementById('deleteVideoModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', function (e) {
        var btn      = e.relatedTarget;
        var url      = btn.getAttribute('data-delete-url');
        var caption  = btn.getAttribute('data-caption');
        var thumb    = btn.getAttribute('data-thumb');
        var video    = btn.getAttribute('data-video');

        document.getElementById('deleteVideoForm').setAttribute('action', url);
        document.getElementById('delCaption').textContent = caption;

        var imgEl   = document.getElementById('delThumbImg');
        var videoEl = document.getElementById('delThumbVideo');

        if (thumb) {
            imgEl.src           = thumb;
            imgEl.style.display = 'block';
            videoEl.style.display = 'none';
        } else {
            videoEl.src           = video;
            videoEl.style.display = 'block';
            imgEl.style.display   = 'none';
        }
    });
    // Reset on close
    modal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('delThumbImg').src  = '';
        document.getElementById('delThumbVideo').src = '';
    });
})();
</script>
@endsection
@section('content')
    <div class="profile-setting my-videos section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>

                                <div class="relevant-ads box-shadow1 p-24 mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <h4 class="mb-1">{{ __('My Videos') }}
                                                <span class="badge bg-secondary ms-2" style="font-size:13px;">{{ $videos->total() }}</span>
                                            </h4>
                                            @if($videoQuota === -1)
                                                <p class="text-muted mb-0" style="font-size:13px;">{{ __('Unlimited uploads on your plan') }}</p>
                                            @elseif($videoQuota > 0)
                                                <p class="text-muted mb-0" style="font-size:13px;">{{ $videoUsed }} / {{ $videoQuota }} {{ __('videos used') }}</p>
                                            @endif
                                        </div>
                                        @if($videoQuota !== 0 && ($videoQuota === -1 || $videoUsed < $videoQuota))
                                            <a href="{{ route('user.my.videos.create') }}" class="red-btn">
                                                <i class="las la-plus me-1"></i> {{ __('Upload New Video') }}
                                            </a>
                                        @else
                                            <span class="badge bg-warning text-dark p-2" style="font-size:13px;">{{ __('Video limit reached — upgrade your plan') }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if(session('success'))<div class="alert alert-success mb-3">{{ session('success') }}</div>@endif
                                @if(session('error'))<div class="alert alert-danger mb-3">{{ session('error') }}</div>@endif

                                @if($videos->count() > 0)
                                    <div class="row g-3">
                                        @foreach($videos as $video)
                                            @php $status = $video->moderationStatus(); @endphp
                                            <div class="col-12 col-sm-6 col-lg-4">
                                                <div class="video-card h-100 d-flex flex-column">
                                                    <div class="video-thumb-wrap">
                                                        @if($video->thumbnail_url)
                                                            <img src="{{ Storage::url($video->thumbnail_url) }}" alt="{{ $video->caption }}">
                                                        @else
                                                            <video src="{{ Storage::url($video->video_url) }}" preload="metadata"></video>
                                                        @endif
                                                        <div class="play-overlay">
                                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="rgba(255,255,255,0.85)">
                                                                <circle cx="12" cy="12" r="11" fill="rgba(0,0,0,0.45)"/>
                                                                <path d="M9.5 7.5l7 4.5-7 4.5V7.5z"/>
                                                            </svg>
                                                        </div>
                                                        <span class="badge {{ $status['class'] }}" style="position:absolute;top:8px;left:8px;font-size:11px;">{{ __($status['label']) }}</span>
                                                    </div>
                                                    <div class="video-body flex-grow-1 d-flex flex-column">
                                                        <div class="video-caption">{{ $video->caption ?: __('(No caption)') }}</div>
                                                        @if($video->listing)
                                                            <div class="video-listing-tag">
                                                                <i class="las la-tag me-1"></i>
                                                                <a href="{{ route('frontend.listing.details', $video->listing->slug) }}" target="_blank">{{ Str::limit($video->listing->title, 40) }}</a>
                                                            </div>
                                                        @else
                                                            <div class="video-listing-tag text-muted">{{ __('No listing tagged') }}</div>
                                                        @endif
                                                        @if($video->is_rejected && $video->reject_reason)
                                                            <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:12px;"><strong>{{ __('Rejected') }}:</strong> {{ $video->reject_reason }}</div>
                                                        @endif
                                                        <div class="mt-auto d-flex gap-2">
                                                            <a href="{{ Storage::url($video->video_url) }}" target="_blank" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                                                <i class="las la-play me-1"></i>{{ __('Preview') }}
                                                            </a>
                                                            <form method="POST" action="{{ route('user.my.videos.destroy', $video->id) }}" onsubmit="return confirm('{{ __('Delete this video?') }}')">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="las la-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-4">{{ $videos->links() }}</div>
                                @else
                                    <div class="relevant-ads box-shadow1 p-24">
                                        <div class="empty-state">
                                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none"><path d="M15 10l4.553-2.277A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <h5>{{ __("You haven't uploaded any videos yet") }}</h5>
                                            <p class="text-muted">{{ __('Upload a short video to promote your listings and attract more buyers.') }}</p>
                                            @if($videoQuota !== 0)
                                                <a href="{{ route('user.my.videos.create') }}" class="red-btn mt-2 d-inline-block">{{ __('Upload Your First Video') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
