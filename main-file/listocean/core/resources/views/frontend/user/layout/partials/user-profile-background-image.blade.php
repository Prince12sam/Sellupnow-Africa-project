<!--Top pic part-->
@if(!empty(Auth::guard('web')->user()->profile_background))
<div class="top-pic-part mb-3">
    {!! render_image_markup_by_attachment_id(Auth::guard('web')->user()->profile_background, '') !!}
    @if(auth()->check() && Request::is('user/profile/settings'))
        <div class="background_image_change_button">
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2"
                    data-bs-toggle="modal"
                    data-bs-target="#userProfileEditModal">
                    <span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.33333 4.66666H4C4.35362 4.66666 4.69276 4.52618 4.94281 4.27613C5.19286 4.02608 5.33333 3.68695 5.33333 3.33332C5.33333 3.15651 5.40357 2.98694 5.5286 2.86192C5.65362 2.73689 5.82319 2.66666 6 2.66666H10C10.1768 2.66666 10.3464 2.73689 10.4714 2.86192C10.5964 2.98694 10.6667 3.15651 10.6667 3.33332C10.6667 3.68695 10.8071 4.02608 11.0572 4.27613C11.3072 4.52618 11.6464 4.66666 12 4.66666H12.6667C13.0203 4.66666 13.3594 4.80713 13.6095 5.05718C13.8595 5.30723 14 5.64637 14 5.99999V12C14 12.3536 13.8595 12.6927 13.6095 12.9428C13.3594 13.1928 13.0203 13.3333 12.6667 13.3333H3.33333C2.97971 13.3333 2.64057 13.1928 2.39052 12.9428C2.14048 12.6927 2 12.3536 2 12V5.99999C2 5.64637 2.14048 5.30723 2.39052 5.05718C2.64057 4.80713 2.97971 4.66666 3.33333 4.66666Z" stroke="#0F172A" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 8.66666C6 9.19709 6.21071 9.7058 6.58579 10.0809C6.96086 10.4559 7.46957 10.6667 8 10.6667C8.53043 10.6667 9.03914 10.4559 9.41421 10.0809C9.78929 9.7058 10 9.19709 10 8.66666C10 8.13622 9.78929 7.62752 9.41421 7.25244C9.03914 6.87737 8.53043 6.66666 8 6.66666C7.46957 6.66666 6.96086 6.87737 6.58579 7.25244C6.21071 7.62752 6 8.13622 6 8.66666Z" stroke="#0F172A" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </span>
                {{ __('Edit Profile') }}
            </button>
        </div>
    @endif
</div>
@endif
@include('frontend.layout.partials.dashboard-notice')
