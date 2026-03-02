<?php


namespace plugins\PageBuilder\Addons\Marketplace;

use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\IconPicker;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Switcher;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\Helpers\RepeaterField;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\PageBuilder\PageBuilderBase;

class MarketPlaceOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'marketplace/marketplace_1.jpg';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();


        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'subtitle',
            'label' => __('Subtitle'),
            'value' => $widget_saved_values['subtitle'] ?? null,
        ]);

        $output .= ColorPicker::get([
            'name' => 'section_bg',
            'label' => __('Background Color'),
            'value' => $widget_saved_values['section_bg'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'section_bg_image',
            'label' => __('Background Image'),
            'value' => $widget_saved_values['section_bg_image'] ?? null,
            'dimensions' => '280x408'
        ]);

        $output .= Image::get([
            'name' => 'banner_image_one',
            'label' => __('Image One'),
            'value' => $widget_saved_values['banner_image_one'] ?? null,
            'dimensions' => '280x408'
        ]);

        $output .= Text::get([
            'name' => 'button_one_title',
            'label' => __('Button One Title'),
            'value' => $widget_saved_values['button_one_title'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_one_link',
            'label' => __('Button One Link'),
            'value' => $widget_saved_values['button_one_link'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_two_title',
            'label' => __('Button Two Title'),
            'value' => $widget_saved_values['button_two_title'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_two_link',
            'label' => __('Button Two Link'),
            'value' => $widget_saved_values['button_two_link'] ?? null,
        ]);


        $output .= Switcher::get([
            'name' => 'button_one_show_hide',
            'label' => __('Button One'),
            'value' => $widget_saved_values['button_one_show_hide'] ?? null,
            'info' => __('Button One Hide/Show')
        ]);

        $output .= Switcher::get([
            'name' => 'button_two_show_hide',
            'label' => __('Button Two'),
            'value' => $widget_saved_values['button_two_show_hide'] ?? null,
            'info' => __('Button Two Hide/Show')
        ]);

        $output .= Text::get([
            'name' => 'app_section_title',
            'label' => __('App Section Title'),
            'value' => $widget_saved_values['app_section_title'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'app_section_subtitle',
            'label' => __('App Section Subtitle'),
            'value' => $widget_saved_values['app_section_subtitle'] ?? null,
        ]);

        $output .= Slider::get([
            'name' => 'padding_top',
            'label' => __('Padding Top'),
            'value' => $widget_saved_values['padding_top'] ?? 260,
            'max' => 500,
        ]);
        $output .= Slider::get([
            'name' => 'padding_bottom',
            'label' => __('Padding Bottom'),
            'value' => $widget_saved_values['padding_bottom'] ?? 190,
            'max' => 500,
        ]);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }


    public function frontend_render() : string
    {

        $settings = $this->get_settings();

        $title = $settings['title'] ?? '';
        $subtitle = $settings['subtitle'] ?? '';
        $section_bg = $settings['section_bg'] ?? '';
        $section_bg_image = render_background_image_markup_by_attachment_id($settings['section_bg_image']) ?? '';
        $banner_image_one = render_image_markup_by_attachment_id($settings['banner_image_one']) ?? '';

        // button section
        $button_one_title = $settings['button_one_title'] ?? '';
        $button_two_title = $settings['button_two_title'] ?? '';
        $button_one_link = $settings['button_one_link'] ?? '';
        $button_two_link = $settings['button_two_link'] ?? '';
        $button_one_show_hide = $settings['button_one_show_hide'] ?? '';
        $button_two_show_hide = $settings['button_two_show_hide'] ?? '';

        // padding
        $padding_top = $settings['padding_top'] ?? '100';
        $padding_bottom = $settings['padding_bottom'] ?? '100';

        // App store badges (from global static_options)
        $android_img_id   = get_static_option('android_app_badge');
        $ios_img_id       = get_static_option('ios_app_badge');
        $android_link     = get_static_option('android_app_link') ?: '#';
        $ios_link         = get_static_option('ios_app_link') ?: '#';
        $android_img_html = $android_img_id ? render_image_markup_by_attachment_id($android_img_id, '', 'thumbnails') : '';
        $ios_img_html     = $ios_img_id     ? render_image_markup_by_attachment_id($ios_img_id, '', 'thumbnails')     : '';

        $app_section_title    = $settings['app_section_title']    ?? __('Get the App');
        $app_section_subtitle = $settings['app_section_subtitle'] ?? __('Buy & sell on the go — download our app and explore thousands of listings.');

        return $this->renderBlade('marketplaces.marketplace-one',[
            'title' => $title,
            'subtitle' => $subtitle,
            'section_bg' => $section_bg,
            'section_bg_image' => $section_bg_image,
            'banner_image_one' => $banner_image_one,
            'button_one_title' => $button_one_title,
            'button_two_title' => $button_two_title,
            'button_one_link' => $button_one_link,
            'button_two_link' => $button_two_link,
            'button_one_show_hide' => $button_one_show_hide,
            'button_two_show_hide' => $button_two_show_hide,
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'android_img_html'    => $android_img_html,
            'ios_img_html'        => $ios_img_html,
            'android_link'        => $android_link,
            'ios_link'            => $ios_link,
            'app_section_title'    => $app_section_title,
            'app_section_subtitle' => $app_section_subtitle,
        ]);
}

    public function addon_title()
    {
        return __('Marketplace: 01');
    }
}
