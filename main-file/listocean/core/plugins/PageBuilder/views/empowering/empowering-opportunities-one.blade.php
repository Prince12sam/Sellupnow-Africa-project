<!--Working and Hiring-->
<div class="about-us" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color: {{$section_bg}}">
  <div class="working-hiring 80">
         <div class="container">
            @foreach ($repeater_data['title_'] as $key => $empowering)
                @if($key == 0 || $key == 2 || $key == 4 || $key == 6)
                <div class="working mb-80">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="img">
                                {!! render_image_markup_by_attachment_id($repeater_data['image_'][$key]) !!}
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="text-part teamArea">
                                <div class="section-tittle">
                                    <h2 class="tittle">
                                        {{ $repeater_data['title_'][$key] }}
                                    </h2>
                                </div>
                                <p class="text">
                                    {{ $repeater_data['description_'][$key] }}
                                </p>
                                <div class="btn-wraper">
                                    <a href="{{ $repeater_data['button_link_'][$key] }}" class="new-cmn-btn rounded-red-btn">  {{ $repeater_data['button_title_'][$key] }} </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 @else
                     <div class="hiring mb-80">
                         <div class="row align-items-center">
                             <div class="col-md-7">
                                 <div class="text-part teamArea">
                                     <div class="section-tittle">
                                         <h2 class="tittle">
                                             {{ $repeater_data['title_'][$key] }}
                                         </h2>
                                     </div>
                                     <p class="text">
                                         {{ $repeater_data['description_'][$key] }}
                                     </p>
                                     <div class="btn-wraper mb-md-0 mb-4">
                                         <a href="{{ $repeater_data['button_link_'][$key] }}" class="new-cmn-btn rounded-red-btn">  {{ $repeater_data['button_title_'][$key] }} </a>
                                     </div>
                                 </div>
                             </div>
                             <div class="col-md-5">
                                 <div class="img">
                                     {!! render_image_markup_by_attachment_id($repeater_data['image_'][$key]) !!}
                                 </div>
                             </div>
                         </div>
                     </div>
                 @endif
            @endforeach
        </div>
    </div>
</div>
