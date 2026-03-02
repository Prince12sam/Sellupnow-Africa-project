<div class="contact-new-wraper" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}">
    <div class="container-1440">
        <div class="row justify-content-center">
            <div class="col-md-6 mb-5">
                <div class="get-in-touch-wraper">
                    <div class="text">
                        <h3 class="section-title">{{ $title }}</h3>
                        <p>{{ $sub_title }}</p>
                    </div>
                       {!! $form_details !!}
                 </div>
            </div>
            <div class="col-md-6">
                <div class="get-in-touch-right-part">
                    <div class="body-part box-shadow1 p-24">
                        <div class="address d-flex align-items-center gap-3 mb-3">
                            <div class="icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="text">
                                <p>{{ __('Address') }}</p>
                                <h5 class="title">{{ $address }}</h5>
                            </div>
                        </div>
                        <div class="email d-flex align-items-center gap-3 mb-3">
                            <div class="icon">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div class="text">
                                <p>{{ __('Email') }}</p>
                                <h5 class="title">{{ $email }}</h5>
                            </div>
                        </div>
                        <div class="phone d-flex align-items-center gap-3">
                            <div class="icon">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div class="text">
                                <p>{{ __('Phone Number') }}</p>
                                <h5 class="title">{{ $phone }}</h5>
                            </div>
                        </div>
                        <div class="devider"></div>
                        <div class="social-icon d-flex align-items-center gap-4">
                            @foreach($repeater_data_share_icons['icon_'] as $key => $icon)
                                <a href="{{$repeater_data_share_icons['icon_link_'][$key]}}" target="_blank">
                                    <div class="icon">
                                        <i class="{{$icon}}"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
