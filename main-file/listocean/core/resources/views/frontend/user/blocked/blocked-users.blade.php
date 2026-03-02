@extends('frontend.layout.master')
@section('site_title')
    {{ __('Blocked Users') }}
@endsection
@section('style')
    <style>
        .blocked-list-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .blocked-list-item:last-child { border-bottom: none; }
        #blocked-list img,
        .blocked-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .blocked-avatar-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .blocked-meta { flex: 1; min-width: 0; }
        .blocked-meta .bm-name {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .blocked-meta .bm-handle { font-size: 12px; color: #94a3b8; }
        .blocked-meta .bm-since  { font-size: 11px; color: #b0bbc9; margin-top: 1px; }
        .unblock-confirm-btn {
            background: #fff;
            border: 1px solid #fca5a5;
            color: #dc2626;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 20px;
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s, color .15s;
            flex-shrink: 0;
        }
        .unblock-confirm-btn:hover { background: #fee2e2; }
        .blocked-empty { text-align: center; padding: 60px 20px 30px; color: #94a3b8; }
        .blocked-empty svg { opacity: .35; margin-bottom: 14px; }
        .blocked-empty p { font-size: 14px; margin: 0; }
        .blocked-count-badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            background: #f1f5f9;
            border-radius: 20px;
            padding: 2px 10px;
            margin-left: 8px;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    <div class="profile-setting blocked-users-page section-padding2">
        <div class="container-1920 plr1">
            <div class="row">
                <div class="col-12">
                    <div class="profile-setting-wraper">
                        @include('frontend.user.layout.partials.user-profile-background-image')
                        <div class="down-body-wraper">
                            @include('frontend.user.layout.partials.sidebar')
                            <div class="main-body">
                                <x-frontend.user.responsive-icon/>
                                <div class="relevant-ads box-shadow1">
                                    <h4 class="dis-title">
                                        {{ __('Blocked Users') }}
                                        <span class="blocked-count-badge">{{ $blockedUsers->total() }}</span>
                                    </h4>

                                    @if($blockedUsers->count())
                                        <div id="blocked-list">
                                            @foreach($blockedUsers as $entry)
                                                @php $user = $entry->blockedUser; @endphp
                                                <div class="blocked-list-item" id="blocked-row-{{ $user->id ?? $entry->blocked_user_id }}">
                                                    {{-- Avatar --}}
                                                    @if(!empty($user))
                                                        @if(!empty($user->profile_photo) && $user->profile_photo !== 'default.png')
                                                            <img class="blocked-avatar"
                                                                 src="{{ asset('uploads/user/profile/' . $user->profile_photo) }}"
                                                                 alt="{{ $user->name }}">
                                                        @else
                                                            <div class="blocked-avatar-placeholder">
                                                                <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
                                                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
                                                                    <circle cx="12" cy="7" r="4" stroke="#94a3b8" stroke-width="2"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="blocked-avatar-placeholder">
                                                            <svg width="22" height="22" fill="none" viewBox="0 0 24 24">
                                                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
                                                                <circle cx="12" cy="7" r="4" stroke="#94a3b8" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                    @endif

                                                    {{-- Meta --}}
                                                    <div class="blocked-meta">
                                                        <div class="bm-name">{{ $user->name ?? __('Deleted User') }}</div>
                                                        @if(!empty($user->username))
                                                            <div class="bm-handle">@{{ $user->username }}</div>
                                                        @endif
                                                        <div class="bm-since">{{ __('Blocked') }} {{ $entry->created_at->diffForHumans() }}</div>
                                                    </div>

                                                    {{-- Actions --}}
                                                    @if(!empty($user))
                                                        <button class="unblock-confirm-btn"
                                                                data-id="{{ $user->id ?? $entry->blocked_user_id }}"
                                                                data-token="{{ csrf_token() }}">
                                                            {{ __('Unblock') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3">
                                            {{ $blockedUsers->links() }}
                                        </div>
                                    @else
                                        <div class="blocked-empty">
                                            <svg width="64" height="64" fill="none" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="10" stroke="#cbd5e1" stroke-width="1.5"/>
                                                <path d="M15 9l-6 6M9 9l6 6" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round"/>
                                            </svg>
                                            <p>{{ __('You have not blocked any users yet.') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.querySelectorAll('.unblock-confirm-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var userId = this.dataset.id;
                var token  = this.dataset.token;
                var row    = document.getElementById('blocked-row-' + userId);
                if (!confirm('{{ __("Unblock this user?") }}')) return;

                var self = this;
                self.disabled = true;
                self.textContent = '{{ __("Unblocking…") }}';

                fetch('{{ url("user/unblock") }}/' + userId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        if (row) {
                            row.style.transition = 'opacity .3s';
                            row.style.opacity = '0';
                            setTimeout(function() { row.remove(); }, 320);
                        }
                        if (typeof toastr_success_js === 'function') toastr_success_js(data.message);
                    } else {
                        self.disabled = false;
                        self.textContent = '{{ __("Unblock") }}';
                        if (typeof toastr_error_js === 'function') toastr_error_js(data.message);
                    }
                })
                .catch(function() {
                    self.disabled = false;
                    self.textContent = '{{ __("Unblock") }}';
                    if (typeof toastr_error_js === 'function') toastr_error_js('{{ __("Something went wrong.") }}');
                });
            });
        });
    </script>
@endsection
