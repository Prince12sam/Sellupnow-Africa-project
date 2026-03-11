<section class="aboutArea" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color:{{$section_bg}}">
    <div class="container-1440">
        <div class="aboutAreaWraper">
            <div class="row justify-content-between flex-lg-row flex-column-reverse gap-lg-0 gap-4">
                <div class="col-lg-6">
                    <div class="about-caption">
                        <div class="section-tittle section-tittle2 mb-80">
                            <h2 class="head2 wow fadeInUp" data-wow-delay="0.1s">{{ $title }}</h2>
                            <p class="wow fadeInUp mt-3" data-wow-delay="0.2s">{{ $subtitle }}</p>
                        </div>
                        <div class="btn-wrapper">
                            <a href="{{$button_one_link}}" class="cmn-btn2 mr-15 mb-10 wow fadeInLeft" data-wow-delay="0.3s">{{ $button_one_title }}</a>
                            <a href="{{$button_two_link}}" class="cmn-btn2 transparent-btn mb-10 wow fadeInRight" data-wow-delay="0.3s">{{ $button_two_title }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="marketplace-phone-col d-flex flex-column align-items-center ps-lg-5">
                        {{-- Phone illustration --}}
                        <div class="marketplace-phone-wrap" aria-hidden="true">
                            <div class="marketplace-phone">
                                <svg viewBox="0 0 120 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="marketplace-phone-svg">
                                    <rect x="4" y="4" width="112" height="192" rx="18" fill="#1a1a2e" stroke="#e2e8f0" stroke-width="2"/>
                                    <rect x="14" y="20" width="92" height="148" rx="6" fill="#fff"/>
                                    <rect x="46" y="8" width="28" height="5" rx="2.5" fill="#374151"/>
                                    <circle cx="74" cy="10.5" r="2" fill="#374151"/>
                                    <circle cx="60" cy="183" r="7" fill="#374151"/>
                                    <rect x="20" y="28" width="80" height="48" rx="4" fill="#f0f4ff"/>
                                    <rect x="20" y="82" width="36" height="10" rx="3" fill="#e5e7eb"/>
                                    <rect x="64" y="82" width="36" height="10" rx="3" fill="#e5e7eb"/>
                                    <rect x="20" y="98" width="80" height="8" rx="3" fill="#e5e7eb"/>
                                    <rect x="20" y="112" width="60" height="8" rx="3" fill="#e5e7eb"/>
                                    <rect x="20" y="130" width="80" height="28" rx="4" fill="#6366f1" opacity=".15"/>
                                </svg>
                            </div>
                        </div>
                        {{-- Get the App — sits directly below the phone --}}
                        <div class="marketplace-getapp text-center wow fadeInUp" data-wow-delay="0.4s">
                            @if(!empty($app_section_title))
                            <h4 class="marketplace-getapp__title">{{ $app_section_title }}</h4>
                            @endif
                            @if(!empty($app_section_subtitle))
                            <p class="marketplace-getapp__subtitle">{{ $app_section_subtitle }}</p>
                            @endif
                            @if(!empty($android_img_html) || !empty($ios_img_html))
                            <div class="marketplace-app-badges d-flex flex-wrap gap-3 mt-3 justify-content-center">
                                @if(!empty($android_img_html))
                                <a href="{{ $android_link }}" @if($android_link !== '#') target="_blank" rel="noopener" @endif class="marketplace-badge">
                                    {!! $android_img_html !!}
                                </a>
                                @endif
                                @if(!empty($ios_img_html))
                                <a href="{{ $ios_link }}" @if($ios_link !== '#') target="_blank" rel="noopener" @endif class="marketplace-badge">
                                    {!! $ios_img_html !!}
                                </a>
                                @endif
                            </div>
                            @endif

                            <div class="mt-3">
                                <x-ads.ad-slot placement="marketplace_under_badges" class="mx-auto" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.marketplace-getapp {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0,0,0,.08);
    width: 100%;
}
.marketplace-getapp__title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 6px;
}
.marketplace-getapp__subtitle {
    font-size: .92rem;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 auto 0;
    max-width: 380px;
}
.marketplace-app-badges { flex-wrap: wrap; }
.marketplace-badge img  { height: 40px; width: auto; display: block; }
.marketplace-phone-wrap { min-height: 260px; }
.marketplace-phone {
    filter: drop-shadow(0 20px 40px rgba(99,102,241,.22));
    animation: mpPhoneFloat 3.8s ease-in-out infinite;
}
.marketplace-phone-svg  { width: 160px; height: auto; display: block; }
@keyframes mpPhoneFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-12px); }
}
</style>
