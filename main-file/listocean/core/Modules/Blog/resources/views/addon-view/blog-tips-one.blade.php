<!--Blog Top Content Part-->
<div class="blog-top-content"  data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}">
    <div class="container-1440">
        <div class="row g-4 justify-content-between align-items-center">
            <div class="col-lg-6">
                <div class="text-part">
                    <h1 class="section-head-tittle">
                        {{ $title }}
                    </h1>
                    <div class="btn-wraper">
                        <a href="{{ $button_link }}" class="new-cmn-btn rounded-red-btn read-more-btn"> {{ $button_title }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="image">
                    {!! $bg_image_one !!}
                </div>
            </div>
        </div>
    </div>
</div>
