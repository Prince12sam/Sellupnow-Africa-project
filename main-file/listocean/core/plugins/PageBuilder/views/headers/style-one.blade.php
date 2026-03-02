<!--Banner part Start-->
<div class="hero-section-wrapper" style="width: min(calc(100% - 360px), 1400px); margin-left: auto; margin-right: auto;">

    {{-- ===== Hero Image Slider ===== --}}
    <div class="home-banner hero-slideshow"
         id="heroSlideshow"
         data-padding-top="{{$padding_top}}"
         data-padding-bottom="{{$padding_bottom}}"
         style="position: relative; overflow: hidden; border-radius: 14px 14px 0 0; height: clamp(260px, 42vw, 500px);">

        @if(!empty($hero_slide_urls) && count($hero_slide_urls) > 0)
            @foreach($hero_slide_urls as $idx => $slide_url)
                <div class="hero-slide"
                     style="position: absolute; inset: 0;
                            background-image: url('{{ $slide_url }}');
                            background-size: cover;
                            background-position: center;
                            opacity: {{ $idx === 0 ? '1' : '0' }};
                            transition: opacity 1s ease-in-out;
                            z-index: {{ $idx === 0 ? '1' : '0' }};">
                </div>
            @endforeach

            {{-- Dot navigation (only if more than one slide) --}}
            @if(count($hero_slide_urls) > 1)
                <div class="hero-slide-dots"
                     style="position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
                            display: flex; gap: 10px; z-index: 20;">
                    @foreach($hero_slide_urls as $idx => $url)
                        <span class="hero-dot {{ $idx === 0 ? 'active' : '' }}"
                              data-slide="{{ $idx }}"
                              style="width: 11px; height: 11px; border-radius: 50%;
                                     background: {{ $idx === 0 ? '#fff' : 'rgba(255,255,255,0.45)' }};
                                     cursor: pointer; display: inline-block;
                                     transition: background 0.3s; border: 2px solid rgba(255,255,255,0.7);">
                        </span>
                    @endforeach
                </div>
            @endif
        @else
            {{-- No images configured --}}
            <div style="position: absolute; inset: 0; background: #e9ecef;"></div>
        @endif

    </div>

    {{-- ===== Search Bar (below the image) ===== --}}
    <style>
    @keyframes heroAdGlow {
        0%,100% { box-shadow: 0 0 0 0 rgba(230,57,70,0.0),  0 2px 10px rgba(0,0,0,0.10); }
        40%      { box-shadow: 0 0 0 6px rgba(230,57,70,0.32), 0 4px 18px rgba(230,57,70,0.20); }
        70%      { box-shadow: 0 0 0 2px rgba(230,57,70,0.14), 0 2px 10px rgba(0,0,0,0.10); }
    }
    .hero-side-ad {
        flex-shrink: 0;
        width: 200px;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        background: #000;
        border: 2px solid rgba(230,57,70,0.22);
        animation: heroAdGlow 2.6s ease-in-out infinite;
        transition: transform 0.2s;
        cursor: pointer;
        text-decoration: none;
        display: block;
    }
    .hero-side-ad:hover { transform: translateY(-2px) scale(1.03); animation: none; box-shadow: 0 6px 26px rgba(230,57,70,0.30); }
    .hero-side-ad img { width: 100%; height: 90px; object-fit: cover; display: block; }
    .hero-side-ad-placeholder {
        flex-shrink: 0;
        width: 200px;
    }
    @media (max-width: 900px) {
        .hero-side-ad, .hero-side-ad-placeholder { display: none !important; }
    }
    </style>

    <div class="hero-search-bar"
         style="background: #ffffff;
                padding: 14px 20px;
                border-radius: 0 0 14px 14px;
                box-shadow: 0 6px 18px rgba(0,0,0,0.10);">

        {{-- 3-column: left ad | search form | right ad --}}
        <div style="display:flex; align-items:center; gap:16px; justify-content:center;">

            {{-- LEFT AD --}}
            @if(!empty($hero_left_ad) && !empty($hero_left_ad['markup']))
                <a href="{{ $hero_left_ad['redirect_url'] }}" target="_blank" rel="noopener" class="hero-side-ad" title="{{ $hero_left_ad['title'] }}">
                    {!! str_replace('<img ', '<img style="width:100%;height:90px;object-fit:cover;display:block;" ', $hero_left_ad['markup']) !!}
                </a>
            @else
                <div class="hero-side-ad-placeholder"></div>
            @endif

            {{-- SEARCH FORM --}}
            <form action="{{get_static_option('listing_filter_page_url') ?? '/listings'}}"
                  class="d-flex align-items-center banner-search-location justify-content-center"
                  method="get"
                  style="max-width: 520px; width: 100%; flex: 1;">
                <div class="banner-form-wraper align-items-center" style="flex: 1; min-width: 0;">
                    @if(!empty(get_static_option('google_map_settings_on_off')))
                        <div class="new_banner__search__input">
                            <div class="new_banner__search__location_left" id="myLocationGetAddress">
                                <i class="fa-solid fa-location-crosshairs fs-4"></i>
                            </div>
                            <input class="form--control" name="change_address_new" id="change_address_new" type="hidden" value="">
                            <input class="banner-input-field w-100" name="autocomplete" id="autocomplete" type="text" placeholder="{{ __('Search location here') }}">
                        </div>
                    @endif
                    <div class="search-with-any-texts">
                        <input class="banner-input-field w-100" type="text" name="home_search" id="home_search" placeholder="{{ __('What are you looking for?') }}">
                        <span id="all_search_result" class="search_with_text_section"></span>
                    </div>
                </div>
                <div class="banner-btn" style="flex-shrink: 0; margin-left: 12px;">
                    <button type="submit" class="new-cmn-btn rounded-red-btn setLocation_btn border-0">{{ get_static_option('search_button_title') ?? __('Search') }}</button>
                </div>
            </form>

            {{-- RIGHT AD --}}
            @if(!empty($hero_right_ad) && !empty($hero_right_ad['markup']))
                <a href="{{ $hero_right_ad['redirect_url'] }}" target="_blank" rel="noopener" class="hero-side-ad" title="{{ $hero_right_ad['title'] }}">
                    {!! str_replace('<img ', '<img style="width:100%;height:90px;object-fit:cover;display:block;" ', $hero_right_ad['markup']) !!}
                </a>
            @else
                <div class="hero-side-ad-placeholder"></div>
            @endif

        </div>
    </div>

</div>

{{-- ===== Auto-slide JS ===== --}}
@if(!empty($hero_slide_urls) && count($hero_slide_urls) > 1)
<script>
(function () {
    var slides = document.querySelectorAll('#heroSlideshow .hero-slide');
    var dots   = document.querySelectorAll('#heroSlideshow .hero-dot');
    var total  = slides.length;
    var current = 0;
    var timer;

    function goTo(idx) {
        slides[current].style.opacity = '0';
        slides[current].style.zIndex  = '0';
        if (dots[current]) { dots[current].style.background = 'rgba(255,255,255,0.45)'; dots[current].classList.remove('active'); }
        current = (idx + total) % total;
        slides[current].style.zIndex  = '1';
        slides[current].style.opacity = '1';
        if (dots[current]) { dots[current].style.background = '#fff'; dots[current].classList.add('active'); }
    }

    function startTimer() { timer = setInterval(function () { goTo(current + 1); }, 4500); }
    function resetTimer()  { clearInterval(timer); startTimer(); }

    startTimer();

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () { resetTimer(); goTo(parseInt(this.dataset.slide)); });
    });
}());
</script>
@endif
<!--Banner part End-->
