<!-- Story Area Start -->
<div class="about-us" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color: {{$section_bg}}">
    <div class="storyArewa ">
        <div class="container teamArea">
            <div class="section-tittle section-tittle6 text-center mb-90">
                <h2 class="tittle">
                   {{ $title }}
                </h2>
                <p class="text-center">
                    {{ $subtitle }}
                </p>
            </div>
        </div>
        <div class="story-data-wraper">
            <div class="line">
                <div class="contianer-1920 plr1 circle-wraper">
                    @foreach ($repeater_data['details_'] as $key => $details)
                        <div class="circle">
                            <div class="tooltip-wraper">
                                <span class="text">{{ $details }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End-of story -->
