<?php


namespace plugins\PageBuilder\Addons\About;

use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Switcher;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\Fields\Textarea;
use plugins\PageBuilder\PageBuilderBase;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Helpers\RepeaterField;
use plugins\PageBuilder\Fields\Image;

class AboutUsOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'about/about_us_one.jpg';
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
        $output .= Textarea::get([
            'name' => 'subtitle',
            'label' => __('Subtitle'),
            'value' => $widget_saved_values['subtitle'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_title_one',
            'label' => __('Button Title One'),
            'value' => $widget_saved_values['button_title_one'] ?? null,
            'info' => __('add button title one')
        ]);

        $output .= Text::get([
            'name' => 'button_link_one',
            'label' => __('Button Link one'),
            'value' => $widget_saved_values['button_link_one'] ?? null,
            'info' => __('add button link one')
        ]);

        $output .= Text::get([
            'name' => 'button_title_two',
            'label' => __('Button Title Two'),
            'value' => $widget_saved_values['button_title_two'] ?? null,
            'info' => __('add button title two')
        ]);

        $output .= Text::get([
            'name' => 'button_link_two',
            'label' => __('Button Link Two'),
            'value' => $widget_saved_values['button_link_two'] ?? null,
            'info' => __('add button link two')
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'about_page_info_01',
            'fields' => [
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'counting_number',
                    'label' => __('Counting Number')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'counting_symbol',
                    'label' => __('symbol')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'counting_title',
                    'label' => __('Title')
                ],

            ]
        ]);

        $output .= Image::get([
            'name' => 'background_image',
            'label' => __('Upload Image'),
            'value' => $widget_saved_values['background_image'] ?? null,
            'dimensions' => '760x720'
        ]);

        $output .= ColorPicker::get([
            'name' => 'section_bg',
            'label' => __('Background Color'),
            'value' => $widget_saved_values['section_bg'] ?? null,
            'info' => __('select color you want to show in frontend'),
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

        $padding_top = $settings['padding_top'];
        $padding_bottom = $settings['padding_bottom'];
        $section_bg = $settings['section_bg'];
        $background_image = render_image_markup_by_attachment_id($settings['background_image'] ?? 0);

        $title =$settings['title'] ?? '';
        $subtitle = $settings['subtitle'] ?? '';
        $repeater_data = $settings['about_page_info_01'];

        $button_title_one = $settings['button_title_one'] ?? '';
        $button_title_two = $settings['button_title_two'] ?? '';
        $button_link_one = $settings['button_link_one'] ?? '';
        $button_link_two = $settings['button_link_two'] ?? '';

        // App store badges — same source as AppDownloadOne
        $android_img_id  = get_static_option('android_app_badge');
        $ios_img_id      = get_static_option('ios_app_badge');
        $android_link    = get_static_option('android_app_link') ?: '#';
        $ios_link        = get_static_option('ios_app_link') ?: '#';
        $android_img_html = $android_img_id
            ? render_image_markup_by_attachment_id($android_img_id, '', 'thumbnails')
            : '';
        $ios_img_html = $ios_img_id
            ? render_image_markup_by_attachment_id($ios_img_id, '', 'thumbnails')
            : '';

        return $this->renderBlade('about.about-us-one',[
            'padding_top'      => $padding_top,
            'padding_bottom'   => $padding_bottom,
            'section_bg'       => $section_bg,
            'background_image' => $background_image,
            'title'            => $title,
            'subtitle'         => $subtitle,
            'repeater_data'    => $repeater_data,
            'button_title_one' => $button_title_one,
            'button_title_two' => $button_title_two,
            'button_link_one'  => $button_link_one,
            'button_link_two'  => $button_link_two,
            'android_img_html' => $android_img_html,
            'ios_img_html'     => $ios_img_html,
            'android_link'     => $android_link,
            'ios_link'         => $ios_link,
        ]);

    }

    public function addon_title()
    {
        return __('About Us One');
    }
}
