@php
    $custom_body_font_get = getCustomFont(1);
    $custom_heading_font_get = getCustomFont(2);
@endphp
@if(!empty($custom_body_font_get) || !empty($custom_heading_font_get))
    <style>
        /*heading font*/
        @font-face {
            font-family: {{optional($custom_heading_font_get)->file}};
            src: url('{{optional($custom_heading_font_get)->path}}') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        /*body font*/
        @font-face {
            font-family: {{optional($custom_body_font_get)->file}};
            src: url('{{optional($custom_body_font_get)->path}}') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --heading-font: '{{optional($custom_heading_font_get)->file}}', sans-serif !important;
            --heading-font1: '{{optional($custom_heading_font_get)->file}}', sans-serif !important;
            --body-font: '{{optional($custom_body_font_get)->file}}', sans-serif !important;
            --body-font1: '{{optional($custom_body_font_get)->file}}', sans-serif !important;
        }
        #all_search_result {
            position: absolute;
            top: 0;
            left: 0;
            background-color: white;
            padding: 10px;
            z-index: 9999;
        }
    </style>
@else
    {!! load_google_fonts() !!}

    <style>
        /*heading font*/
        :root {
            --heading-font1: '{{get_static_option('heading_font_family')}}', sans-serif !important;
            --heading-font: '{{get_static_option('heading_font_family')}}', sans-serif !important;
            --body-font1: '{{get_static_option('body_font_family')}}', sans-serif !important;
            --body-font: '{{get_static_option('body_font_family')}}', sans-serif !important;
        }
    </style>
@endif
