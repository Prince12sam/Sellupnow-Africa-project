<?php


namespace plugins\PageBuilder\Addons\Header;

use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\IconPicker;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Switcher;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\Helpers\RepeaterField;
use plugins\PageBuilder\PageBuilderBase;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;

class HeaderStyleOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'header/01.jpg';
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

        $output .= Text::get([
            'name' => 'top_title',
            'label' => __('Top Title'),
            'value' => $widget_saved_values['top_title'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'top_image',
            'label' => __('Top Image'),
            'value' => $widget_saved_values['top_image'] ?? null,
            'dimensions' => '24x25'
        ]);

        $output .= ColorPicker::get([
            'name' => 'header_background_color',
            'label' => __('Background Color'),
            'value' => $widget_saved_values['header_background_color'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'background_image',
            'label' => __('Background Image / Slide 1 (1920x800 recommended)'),
            'value' => $widget_saved_values['background_image'] ?? null,
            'dimensions' => '1920x800'
        ]);

        $output .= Image::get([
            'name' => 'background_image_2',
            'label' => __('Slide 2 (optional — enables auto-slide)'),
            'value' => $widget_saved_values['background_image_2'] ?? null,
            'dimensions' => '1920x800'
        ]);

        $output .= Image::get([
            'name' => 'background_image_3',
            'label' => __('Slide 3 (optional)'),
            'value' => $widget_saved_values['background_image_3'] ?? null,
            'dimensions' => '1920x800'
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'hero_slider_images_01',
            'fields' => [
                [
                    'type' => RepeaterField::IMAGE,
                    'name' => 'hero_slide_img',
                    'label' => __('Extra Slide Images (add more slides beyond 3)')
                ],
            ]
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'banner_left_images_01',
            'fields' => [
                [
                    'type' => RepeaterField::IMAGE,
                    'name' => 'banner_left_images',
                    'label' => __('Left Banner Images (maximus add six images)')
                ],
            ]
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'banner_right_images_02',
            'fields' => [
                [
                    'type' => RepeaterField::IMAGE,
                    'name' => 'banner_right_images',
                    'label' => __('Right Banner Images (maximus add six images)')
                ],
            ]
        ]);

        $output .= Text::get([
            'name' => 'search_button_title',
            'label' => __('Search Button Title'),
            'value' => $widget_saved_values['search_button_title'] ?? null,
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

        $padding_top = $settings['padding_top'] ?? '100';
        $padding_bottom = $settings['padding_bottom'] ?? '100';
        $title = $settings['title'] ?? '';
        $subtitle = $settings['subtitle'] ?? '';
        $top_title = $settings['top_title'] ?? '';
        $top_image =  render_image_markup_by_attachment_id($settings['top_image']) ?? '';
        $background_image =  render_background_image_markup_by_attachment_id($settings['background_image']) ?? '';
        $header_background_color = $settings['header_background_color'] ?? '';
        $search_button_title = $settings['search_button_title'] ?? '';
        $banner_left_images_01 = $settings['banner_left_images_01'] ?? '';
        $banner_right_images_02 = $settings['banner_right_images_02'] ?? '';

        // Build slide URL array for the auto-slider
        $hero_slider_images_01 = $settings['hero_slider_images_01'] ?? [];
        $hero_slide_urls = [];
        if (!empty($hero_slider_images_01) && is_array($hero_slider_images_01)) {
            foreach ($hero_slider_images_01 as $slide) {
                $img_id = $slide['hero_slide_img'] ?? null;
                if (!empty($img_id)) {
                    $img_data = get_attachment_image_by_id($img_id, 'full');
                    if (!empty($img_data['img_url'])) {
                        $hero_slide_urls[] = $img_data['img_url'];
                    }
                }
            }
        }
        // Fallback: use background_image, background_image_2, background_image_3 as slides
        if (empty($hero_slide_urls)) {
            foreach (['background_image', 'background_image_2', 'background_image_3'] as $field) {
                $img_id = $settings[$field] ?? null;
                if (!empty($img_id)) {
                    $img_data = get_attachment_image_by_id($img_id, 'full');
                    if (!empty($img_data['img_url'])) {
                        $hero_slide_urls[] = $img_data['img_url'];
                    }
                }
            }
        }

        // Side search-bar mini-ads
        $hero_left_ad  = null;
        $hero_right_ad = null;
        try {
            $leftAd  = \App\Models\Backend\Advertisement::where('slot', 'sellupnow:hero_search_left')->where('status', 1)->first();
            $rightAd = \App\Models\Backend\Advertisement::where('slot', 'sellupnow:hero_search_right')->where('status', 1)->first();
            if ($leftAd) {
                $hero_left_ad = [
                    'markup'       => render_image_markup_by_attachment_id($leftAd->image),
                    'redirect_url' => $leftAd->redirect_url ?? '#',
                    'title'        => $leftAd->title ?? '',
                ];
            }
            if ($rightAd) {
                $hero_right_ad = [
                    'markup'       => render_image_markup_by_attachment_id($rightAd->image),
                    'redirect_url' => $rightAd->redirect_url ?? '#',
                    'title'        => $rightAd->title ?? '',
                ];
            }
        } catch (\Throwable $e) {
            // DB not ready — skip side ads silently
        }

    return $this->renderBlade('headers.style-one',[
        'padding_top' => $padding_top,
        'padding_bottom' => $padding_bottom,
        'title' => $title,
        'subtitle' => $subtitle,
        'top_title' => $top_title,
        'top_image' => $top_image,
        'background_image' => $background_image,
        'header_background_color' => $header_background_color,
        'search_button_title' => $search_button_title,
        'banner_left_images_01' => $banner_left_images_01,
        'banner_right_images_02' => $banner_right_images_02,
        'hero_slide_urls' => $hero_slide_urls,
        'hero_left_ad'  => $hero_left_ad,
        'hero_right_ad' => $hero_right_ad,
    ]);

}
    public function addon_title()
    {
        return __('Header: 01');
    }
}
