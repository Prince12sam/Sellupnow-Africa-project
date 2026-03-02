<!--Ad Banner-->
@if(!empty($add_markup))
<div class="homepage-ad-banner" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}"
     style="padding-top: {{$padding_top}}px; padding-bottom: {{$padding_bottom}}px;">
    <div style="width: min(calc(100% - 360px), 1400px); margin-left: auto; margin-right: auto;">
        <div class="text-{{$custom_container ?: 'center'}} single-banner-ads ads-banner-box" id="home_advertisement_store"
             style="border-radius: 10px; overflow: hidden; line-height: 0;">
            <input type="hidden" id="add_id" value="{{$add_id}}">
            <div style="font-size:0; line-height:0;">
                {!! str_replace('<img ', '<img style="width:100%;height:auto;display:block;" ', $add_markup) !!}
            </div>
        </div>
    </div>
</div>
@elseif(get_static_option('google_adsense_status') == 'on')
<div class="google-adds" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}"
     style="padding-top: {{$padding_top}}px; padding-bottom: {{$padding_bottom}}px;">
    <div style="width: min(calc(100% - 360px), 1400px); margin-left: auto; margin-right: auto;">
        <div class="text-{{$custom_container ?: 'center'}} single-banner-ads ads-banner-box" id="home_advertisement_store">
            <input type="hidden" id="add_id" value="{{$add_id}}">
            {!! $add_markup !!}
        </div>
    </div>
</div>
@endif
<!--Ad Banner-->
