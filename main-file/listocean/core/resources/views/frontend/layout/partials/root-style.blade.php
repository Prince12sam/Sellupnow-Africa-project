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

    .new-style .exploreCategories .singleCategories,
    .new-style .exploreCategories .categories1,
    .new-style .exploreCategories .categories2,
    .new-style .exploreCategories .categories3,
    .new-style .exploreCategories .categories4,
    .new-style .exploreCategories .categories5,
    .new-style .exploreCategories .categories6,
    .new-style .exploreCategories .categories7,
    .new-style .exploreCategories .categories8,
    .new-style .exploreCategories .categories9 {
        background: linear-gradient(var(--category-card-bg-from), rgba(255, 255, 255, 0)) !important;
        border-color: var(--category-card-border) !important;
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
</style>
