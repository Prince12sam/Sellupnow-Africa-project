<!-- Categorie Area  S t a r t-->
<div class="exploreCategories" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color: {{$section_bg}}">
    <div class="container-1440">
        <div class="row">
            <div class="col-xl-8 col-lg-7 col-md-10 col-sm-10">
                <div class="section-tittle">
                    <h2 class="tittle">{{ $title }} </h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="global-slick-init slider-inner-margin sliderArrow" data-infinite="true" data-arrows="true" data-dots="false"  data-rtl="{{get_user_lang_direction() == 'rtl' ? 'true' : 'false'}}"  data-slidesToShow="8" data-swipeToSlide="true" data-autoplay="false" data-autoplaySpeed="2500" data-prevArrow='<div class="prev-icon"><i class="las la-angle-left"></i></div>'
                     data-nextArrow='<div class="next-icon"><i class="las la-angle-right"></i></div>' data-responsive='[{"breakpoint": 1500,"settings": {"slidesToShow": 4}},{"breakpoint": 1600,"settings": {"slidesToShow": 4}},{"breakpoint": 1400,"settings": {"slidesToShow": 3}},{"breakpoint": 1200,"settings": {"slidesToShow": 3}},{"breakpoint": 991,"settings": {"slidesToShow": 2}},{"breakpoint": 768, "settings": {"slidesToShow": 2}},{"breakpoint": 576, "settings": {"slidesToShow": 1}}]'>
                <!-- Single -->
                    @foreach($all_category as $category)
                    <div class="singleCategories categories{{$category->id}} wow fadeInUp" data-wow-delay="0.1s">
                            <div class="categoriIcon text-center">
                                <a href="{{ route('frontend.show.listing.by.category', $category->slug ?? 'x') }}" class="title">
                                    {!! render_image_markup_by_attachment_id($category->image) !!}
                                   </a>
                            </div>
                        <div class="categorie-text">
                            <h4 class="text-center">
                                <a href="{{ route('frontend.show.listing.by.category', $category->slug ?? 'x') }}" class="title oneLine mt-2"> {{ $category->name }}   </a>
                            </h4>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End-of Categories -->
