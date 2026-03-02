<div class="testimonialArea" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color:{{$section_bg}}">
    <div class="container-fluid">
        <!-- Section Tittle -->
        <div class="row">
            <div class="col-xl-8 col-lg-7 col-md-10 col-sm-10">
                <div class="section-tittle mb-50">
                    <h2 class="tittle ">{{ $section_title }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="colo-lg-12">
                <div class="global-slick-init slider-inner-margin sliderArrow" data-rtl="{{get_user_lang_direction() == 'rtl' ? 'true' : 'false'}}" data-infinite="true" data-arrows="true" data-dots="false" data-slidesToShow="4" data-swipeToSlide="true" data-autoplay="true" data-autoplaySpeed="2500" data-prevArrow='<div class="prev-icon"><i class="las la-angle-left"></i></div>'
                     data-nextArrow='<div class="next-icon"><i class="las la-angle-right"></i></div>' data-responsive='[{"breakpoint": 1800,"settings": {"slidesToShow": 4}},{"breakpoint": 1600,"settings": {"slidesToShow": 4}},{"breakpoint": 1400,"settings": {"slidesToShow": 3}},{"breakpoint": 1200,"settings": {"slidesToShow": 3}},{"breakpoint": 991,"settings": {"slidesToShow": 2}},{"breakpoint": 768, "settings": {"slidesToShow": 2}},{"breakpoint": 576, "settings": {"slidesToShow": 1}}]'>

                    <!-- Single Testimonial -->
                    @foreach($all_reviews_user as $user)
                        <div class="singleTestimonial">
                            <div class="testimonialCap">
                                <ul class="rattingList">
                                    @for($i = 0; $i < 5; $i++)
                                        @if($i < $user->rating)
                                            <li class="listItems"><i class="las la-star icon"></i></li>
                                        @else
                                            <li class="listItems"><i class="lar la-star icon"></i></li>
                                        @endif
                                    @endfor
                                </ul>
                                <div class="testiPera">
                                    <p class="pera">{{ $user->message }}</p>
                                </div>
                                <!-- Client -->
                                <div class="testimonialClient d-flex align-items-center">
                                    <div class="seller-img">
                                        @if(!empty($buyer->user->image))
                                            {!! render_image_markup_by_attachment_id(optional($user)->user->image, '','') !!}
                                        @else
                                            <img src="{{ asset('assets/frontend/img/static/user-no-image.webp') }}">
                                        @endif
                                    </div>
                                    <div class="clientText">
                                        <span class="clientName">{{ $user->fullname }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
