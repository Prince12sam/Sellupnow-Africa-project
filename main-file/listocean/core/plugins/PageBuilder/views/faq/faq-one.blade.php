<!--FAQ part-->
<div class="faq-part" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color: {{$section_bg}}">
    <div class="container-1440">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="list-ocean-faq faqwraper">
                    <h1 class="second-heading mb-4"> {{ $title }} </h1>
                    <div class="listocen-faq-wraper" id="listocen-faq-wraper">
                        @foreach($repeater_data['title_'] as $key => $title)
                            <div class="listocen-faq-item">
                                <h3 class="listocen-faq-title" data-bs-target="#securityOne">{{$title}}</h3>
                                <div class="listocen-faq-para" id="securityOne" data-bs-parent="#listocen-faq-wraper" style="display: none;">
                                    {{$repeater_data['description_'][$key]}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-part">
                        <p>{{ $contact_info }}</p>
                        <div>
                            <a href="{{ $contact_info_link }} ">{{ $contact_info_title }}
                                <span class="right-icon">
                                    <i class="fa-solid fa-arrow-right-long"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
