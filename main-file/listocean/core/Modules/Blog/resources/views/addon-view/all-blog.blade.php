<!--Daily Blog Part-->
<div class="daily-blog-part">
    <div class="container-1440">
        <div class="daily-blog-top-part">
            <h2 class="blog-heading">{{ $title }}</h2>
        </div>
        <div class="shorting-button-wraper">
            <div class="shorting-buttons global-slick-init slider-inner-margin sliderArrow" data-centerMode="false" data-variableWidth="true" data-rtl="{{get_user_lang_direction() == 'rtl' ? 'true' : 'false'}}" data-infinite="true" data-arrows="true" data-dots="false" data-slidesToShow="5" data-swipeToSlide="true" data-autoplay="false" data-autoplaySpeed="2500" data-prevArrow='<div class="prev-icon blog-preview"><i class="las la-angle-left"></i></div>'
                 data-nextArrow='<div class="next-icon blog-next"><i class="las la-angle-right"></i></div>' data-responsive='[{"breakpoint": 1400,"settings": {"slidesToShow": 5}},{"breakpoint": 1200,"settings": {"slidesToShow": 4}},{"breakpoint": 992,"settings": {"slidesToShow": 4}},{"breakpoint": 768, "settings": {"slidesToShow": 3}},{"breakpoint": 577, "settings": {"slidesToShow": 3}}, {"breakpoint": 510, "settings": {"slidesToShow": 2}}, {"breakpoint": 375, "settings": {"slidesToShow": 1}}]'>
                <div class="short-btn active" data-filter=".catagory">{{ __('All') }}</div>
                @foreach($blog_categories as $category)
                    <div class="short-btn" data-filter=".catagory{{ $category->id }}">{{ $category->name }}</div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="devider"></div>
    <div class="blog-wraper">
        <div class="container-1440">
            <div class="row g-4 grid">
                @foreach($all_blogs as $blog)
                    <div class="col-lg-3 col-md-4 col-sm-6 catagory catagory{{$blog->category?->id}}">
                        <div class="blog-card">
                            <a href="{{ route('frontend.blog.single', $blog->slug ?? 'x') }}">
                                <div class="img">
                                    {!! render_image_markup_by_attachment_id($blog->image,'','','thumb') !!}
                                </div>
                            </a>
                            <div class="text-part">
                                <div class="date">{{ optional($blog->created_at)->diffForHumans() }}</div>
                                <div class="title">
                                    <a href="{{ route('frontend.blog.single', $blog->slug ?? 'x') }}">
                                        {!! strlen($blog->title) > 55 ? substr($blog->title, 0, 55) . '...' : $blog->title !!}
                                    </a>
                                </div>
                                <p class="pera">
                                    {!! strlen(strip_tags($blog->blog_content)) > 80 ? substr(strip_tags($blog->blog_content), 0, 80) . '...' : $blog->blog_content !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<x-pagination.frontend-laravel-paginate :alldata="$all_blogs" :title="__('No Blog Yet')"/>


