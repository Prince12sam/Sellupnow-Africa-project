<!-- Hero Area S t a r t -->
<div class="sliderArea heroAboutStyle plr"  data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color: {{$section_bg}}">
    <div class="slider-active">
        <div class="single-slider d-flex align-items-center">
            <div class="container-fluid ">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xxl-6 col-xl-7 col-lg-5 col-md-12">
                        <div class="heroCaption mb-50">
                            <h1 class="tittle" data-animation="fadeInUp" data-delay="0.0s">
                                {{ $title }}
                            </h1>
                            <p class="pera" data-animation="fadeInUp" data-delay="0.3s">
                                {{ $subtitle }}
                            </p>
                            <div class="btn-wrapper">
                                <a href="{{$button_link_one}}" class="new-cmn-btn signup-btn mr-15 mb-10 wow fadeInLeft" data-wow-delay="0.3s">{{ $button_title_one }}</a>
                                <a href="{{$button_link_two}}" class="new-cmn-btn browse-ads mb-10 wow fadeInLeft" data-wow-delay="0.3s">{{ $button_title_two }}<i class="las la-angle-right icon"></i></a>
                            </div>
                            @if($android_img_html || $ios_img_html || $android_link !== '#' || $ios_link !== '#')
                            <div class="hero-app-badges d-flex flex-wrap gap-3 align-items-center mt-4">
                                {{-- Android --}}
                                @php $aHref = ($android_link && $android_link !== '#') ? $android_link : '#'; @endphp
                                <a href="{{ $aHref }}" @if($aHref !== '#') target="_blank" rel="noopener" @endif class="hero-app-badge hero-app-badge--android" title="Get it on Google Play">
                                    @if($android_img_html)
                                        <span class="hero-app-badge__img">{!! $android_img_html !!}</span>
                                    @else
                                        <span class="hero-app-badge__fallback">
                                            <span class="hero-app-badge__icon">
                                                <svg width="24" height="26" viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.5 0.8L14.5 13.5L1.5 26.2C0.9 25.9 0.5 25.2 0.5 24.4V2.6C0.5 1.8 0.9 1.1 1.5 0.8Z" fill="#EA4335"/>
                                                    <path d="M20 9.5L16 13.5L20 17.5L24.5 14.9C25.7 14.2 25.7 12.8 24.5 12.1L20 9.5Z" fill="#FBBC05"/>
                                                    <path d="M14.5 13.5L20 9.5L1.5 0.8C2 0.5 2.6 0.5 3.1 0.8L14.5 13.5Z" fill="#4CAF50"/>
                                                    <path d="M14.5 13.5L3.1 26.2C2.6 26.5 2 26.5 1.5 26.2L20 17.5L14.5 13.5Z" fill="#1565C0"/>
                                                </svg>
                                            </span>
                                            <span class="hero-app-badge__text"><small>GET IT ON</small><strong>Google Play</strong></span>
                                        </span>
                                    @endif
                                </a>
                                {{-- iOS --}}
                                @php $iHref = ($ios_link && $ios_link !== '#') ? $ios_link : '#'; @endphp
                                <a href="{{ $iHref }}" @if($iHref !== '#') target="_blank" rel="noopener" @endif class="hero-app-badge hero-app-badge--ios" title="Download on the App Store">
                                    @if($ios_img_html)
                                        <span class="hero-app-badge__img">{!! $ios_img_html !!}</span>
                                    @else
                                        <span class="hero-app-badge__fallback">
                                            <span class="hero-app-badge__icon">
                                                <svg width="22" height="26" viewBox="0 0 24 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20 14.5C20 11.1 22 9.6 22.1 9.5C20.5 7.3 18.1 7 17.3 7C15.2 6.8 13.2 8.2 12.1 8.2C11 8.2 9.4 7 7.6 7C5.1 7 2.8 8.4 1.5 10.5C-1.1 14.8 0.7 21.2 3.2 24.7C4.4 26.4 5.9 28.3 7.8 28.2C9.7 28.1 10.4 27 12.6 27C14.8 27 15.4 28.2 17.4 28.2C19.4 28.2 20.7 26.5 21.9 24.8C23.3 22.8 23.8 20.9 23.9 20.8C23.8 20.8 20.1 19.3 20 14.5Z" fill="currentColor"/>
                                                    <path d="M15.8 4.7C16.8 3.5 17.4 1.9 17.2 0.2C15.8 0.3 14.1 1.1 13.1 2.3C12.1 3.4 11.4 5 11.6 6.6C13.2 6.7 14.8 5.9 15.8 4.7Z" fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <span class="hero-app-badge__text"><small>DOWNLOAD ON THE</small><strong>App Store</strong></span>
                                        </span>
                                    @endif
                                </a>
                            </div>
                            @endif
                        </div>
                        <!--? Count Down S t a r t -->
                        <div class="countDown">
                            <div class="row">
                                @foreach ($repeater_data['counting_number_'] as $key => $counting_number)
                                    <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                                        <div class="single mb-24 wow fadeInLeft" data-wow-delay="0.2s">
                                            <div class="single-counter">
                                                <span class="counter odometer" data-count="{{ $counting_number }}"></span>
                                                <p class="icon">{{ $repeater_data['counting_symbol_'][$key] }}</p>
                                            </div>
                                            <div class="pera-count">
                                                <p class="pera">{{ $repeater_data['counting_title_'][$key] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Count Down E n d -->
                    </div>
                    <div class="col-xxl-6 col-xl-5 col-lg-7 ">
                        <div class="hero-man d-none d-lg-block f-right" >
                            {!! $background_image !!}
                        </div>
                    </div>
                </div>
                <!-- Search Box -->
            </div>
        </div>
    </div>
</div>
<!-- End-of Hero  -->
<style>
.hero-app-badges { line-height: 1; }
.hero-app-badge  { display: inline-flex; align-items: center; text-decoration: none !important;
                   border-radius: 10px; overflow: hidden; transition: transform .2s ease, box-shadow .2s ease;
                   box-shadow: 0 3px 12px rgba(0,0,0,.12); }
.hero-app-badge:hover { transform: translateY(-2px) scale(1.03); box-shadow: 0 6px 22px rgba(0,0,0,.18); }
.hero-app-badge__img img { display: block; height: 40px; width: auto; max-width: 180px;
                            object-fit: contain; border-radius: 10px; }
.hero-app-badge__fallback { display: inline-flex; align-items: center; gap: 10px;
                             padding: 8px 18px 8px 14px; background: #1a1a2e; color: #fff;
                             border-radius: 10px; min-width: 155px; }
.hero-app-badge__icon { display: flex; align-items: center; flex-shrink: 0; }
.hero-app-badge__text { display: flex; flex-direction: column; line-height: 1.1; }
.hero-app-badge__text small { font-size: .58rem; letter-spacing: .06em; text-transform: uppercase;
                               opacity: .75; font-weight: 500; }
.hero-app-badge__text strong { font-size: .98rem; font-weight: 700; color: #fff; }
</style>