@extends('frontend.layout.master')
@section('site_title')
    {{ __('Notifications') }}
@endsection
@section('style')
    <style>
        .notif-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: background .15s;
        }
        .notif-card.unread {
            background: #f8f9ff;
            border-color: #e0e7ff;
        }
        .notif-card .notif-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ede9fe;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .notif-card .notif-body { flex: 1; min-width: 0; }
        .notif-card .notif-message { font-size: 14px; color: #334155; margin-bottom: 4px; }
        .notif-card .notif-time { font-size: 12px; color: #94a3b8; }
        .notif-card .unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #6366f1;
            margin-top: 6px;
            flex-shrink: 0;
        }
        .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
        .mark-all-btn { font-size: 13px; }
    </style>
@endsection
@section('content')
    <div class="dashboard-layout-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include('frontend.user.layout.partials.sidebar')
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="dashboard-right-content box-shadow1">

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="mb-0 fw-semibold">{{ __('Notifications') }}</h5>
                            @if(auth('web')->user()->unreadNotifications->count())
                                <button class="btn btn-sm btn-outline-primary mark-all-btn" id="markAllRead">
                                    {{ __('Mark all as read') }}
                                </button>
                            @endif
                        </div>

                        @if($notifications->count())
                            @foreach($notifications as $notification)
                                @php
                                    $data    = $notification->data ?? [];
                                    $message = $data['message'] ?? ($data['title'] ?? __('New notification'));
                                    $url     = $data['url'] ?? $data['link'] ?? null;
                                    $isUnread = is_null($notification->read_at);
                                @endphp
                                <div class="notif-card {{ $isUnread ? 'unread' : '' }}">
                                    <div class="notif-icon">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
                                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div class="notif-body">
                                        <div class="notif-message">
                                            @if($url)
                                                <a href="{{ $url }}" class="text-dark text-decoration-none">{{ $message }}</a>
                                            @else
                                                {{ $message }}
                                            @endif
                                        </div>
                                        <div class="notif-time">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                    @if($isUnread)
                                        <div class="unread-dot"></div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="mt-3">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="empty-state">
                                <svg width="60" height="60" fill="none" viewBox="0 0 24 24">
                                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p>{{ __('You have no notifications yet.') }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var markAllBtn = document.getElementById('markAllRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function() {
                fetch('{{ route("user.notification.read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(function(data) {
                    if (data.success) {
                        document.querySelectorAll('.notif-card.unread').forEach(function(el) {
                            el.classList.remove('unread');
                        });
                        document.querySelectorAll('.unread-dot').forEach(function(el) { el.remove(); });
                        markAllBtn.remove();
                        toastr_success_js('{{ __("All notifications marked as read.") }}');
                    }
                });
            });
        }
    </script>
@endsection
