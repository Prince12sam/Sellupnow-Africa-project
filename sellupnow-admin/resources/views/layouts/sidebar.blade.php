<div class="app-sidebar pe-0">
    <div class="scrollbar-sidebar pe-1" style=" overflow-y: auto;
    overflow-x: hidden;">
        <div class="branding-logo">
            @php
                $request = request();
                $isAdmin = true;
                $url = route('admin.dashboard.index');
            @endphp
            <a href="{{ $url }}">
                <img src="{{ $generaleSetting?->logo ?? asset('assets/logo.png') }}" alt="logo" loading="lazy" />
            </a>
        </div>
        <div class="branding-logo-forMobile">
            <a href="{{ $generaleSetting?->logo ?? asset('assets/logo.png') }}"></a>
        </div>
        <div class="app-sidebar-inner">
            <ul class="vertical-nav-menu">
                @include('layouts.partials.admin-menu')
            </ul>
        </div>
        <div class="sideBarfooter">
            <button type="button" class="fullbtn hite-icon" onclick="toggleFullScreen(document.body)">
                <img src="{{ asset('assets/icons-admin/expand.svg') }}" alt="icon" loading="lazy" />
            </button>

            @php
                $isRootUser = false;
                if (auth()->check()) {
                    try {
                        $isRootUser = auth()->user()->getRoleNames()->contains('root');
                    } catch (\Throwable $th) {
                        $isRootUser = false;
                    }
                }
            @endphp

            <a href="javascript:void(0)" class="fullbtn hite-icon logout">
                <img src="{{ asset('assets/icons-admin/log-out.svg') }}" alt="icon" loading="lazy" />
            </a>
            <a href="javascript:void(0)" class="fullbtn hite-icon">
                <small style="font-size: 10px; color: #888;">
                    {{ config('app.version') }}
                </small>
            </a>
        </div>
    </div>
</div>
