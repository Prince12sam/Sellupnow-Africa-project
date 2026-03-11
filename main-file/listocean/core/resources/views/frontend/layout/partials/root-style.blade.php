<style>
    :root {
        --main-color-one: {{get_static_option('site_main_color_one','#1DBF73')}};
        --main-color-two: {{get_static_option('site_main_color_two','#47C8ED')}};
        --main-color-three: {{get_static_option('site_main_color_three','#FF6B2C')}};
        --heading-color: {{get_static_option('heading_color','#333333')}};
        --light-color: {{get_static_option('light_color','#666666')}};
        --extra-light-color: {{get_static_option('extra_light_color','#999999')}};
        --heading-font: {{get_static_option('heading_font_family')}}, sans-serif;
        --body-font: {{get_static_option('body_font_family')}}, sans-serif;
        --category-card-bg-from: {{ get_static_option('category_card_bg_from', 'rgba(246, 145, 93, 0.2)') }};
        --category-card-border: {{ get_static_option('category_card_border', 'rgba(246, 145, 93, 0.3215686275)') }};
        --header-bg-color: {{ get_static_option('header_bg_color', '#ffffff') }};
        --footer-bg-color: {{ get_static_option('footer_bg_color', '#F9FAFB') }};
    }

    .headerBg3,
    .new-style .headerBg4 {
        background: var(--header-bg-color) !important;
    }

    .footerStyleOne,
    .footerStyleThree,
    .footerStyleTwo {
        background: var(--footer-bg-color) !important;
    }

    /* Keep category grids breathable instead of packing too many cards per row. */
    .new-style .catagory-wise-listing .services_sub_category_load_wraper {
        gap: 24px;
    }

    .new-style .exploreCategories .global-slick-init.slider-inner-margin .slick-list {
        margin-left: -10px;
        margin-right: -10px;
    }

    .new-style .exploreCategories .global-slick-init.slider-inner-margin .slick-slide {
        padding-left: 10px;
        padding-right: 10px;
        box-sizing: border-box;
    }

    .new-style .catagory-wise-listing .services_sub_category_load_wraper .singleCategories {
        width: calc(20% - 19.2px);
    }

    @media (max-width: 1399.98px) {
        .new-style .catagory-wise-listing .services_sub_category_load_wraper .singleCategories {
            width: calc(25% - 18px);
        }
    }

    @media (max-width: 1199.98px) {
        .new-style .catagory-wise-listing .services_sub_category_load_wraper .singleCategories {
            width: calc(33.333333% - 16px);
        }
    }

    @media (max-width: 767.98px) {
        .new-style .catagory-wise-listing .services_sub_category_load_wraper .singleCategories {
            width: calc(50% - 12px);
        }
    }

    @media (max-width: 575.98px) {
        .new-style .catagory-wise-listing .services_sub_category_load_wraper .singleCategories {
            width: 100%;
        }
    }

    /* Keep footer text consistently white across all states. */
    footer .footerWrapper,
    footer .footerWrapper p,
    footer .footerWrapper span,
    footer .footerWrapper li,
    footer .footerWrapper small,
    footer .footerWrapper strong,
    footer .footerWrapper em,
    footer .footerWrapper .pera,
    footer .footerWrapper .footerTittle,
    footer .footerWrapper .singleLinks,
    footer .footerWrapper a,
    footer .footerWrapper a:link,
    footer .footerWrapper a:visited,
    footer .footerWrapper a:hover,
    footer .footerWrapper a:active,
    footer .footerWrapper a:focus {
        color: #ffffff !important;
    }
</style>
