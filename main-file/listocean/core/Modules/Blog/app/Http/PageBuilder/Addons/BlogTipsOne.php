<?php

namespace Modules\Blog\app\Http\PageBuilder\Addons;

use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\PageBuilderBase;

class BlogTipsOne extends PageBuilderBase
{
    // This function return the image name of the addon
    public function preview_image()
    {
        return 'blog-tips-one.png';
    }

    // This function points the location of the image, It accept only module name
    public function setAssetsFilePath()
    {
        return externalAddonImagepath('Blog');
    }

    // This function contains addon settings while using the addon in the page builder
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
            'name' => 'button_title',
            'label' => __('Button Title'),
            'value' => $widget_saved_values['button_title'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_link',
            'label' => __('Button Link'),
            'value' => $widget_saved_values['button_link'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'blog_tips_image',
            'label' => __('Image'),
            'value' => $widget_saved_values['blog_tips_image'] ?? null,
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

    // This function will render the addon on frontend, you can get the inputted values passed from the admin_render function
    public function frontend_render()
    {

        $settings = $this->get_settings();
        $padding_top = $settings['padding_top'];
        $padding_bottom = $settings['padding_bottom'];
        $section_bg = $settings['section_bg'];
        $title = $settings['title'] ?? '';
        $button_title = $settings['button_title'] ?? '';
        $button_link = $settings['button_link'] ?? '';
        $bg_image_one = render_image_markup_by_attachment_id($settings['blog_tips_image']);

        // readable values must be pass via an array
        $data = [
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_bg' => $section_bg,
            'title' => $title,
            'button_title' => $button_title,
            'button_link' => $button_link,
            'bg_image_one' => $bg_image_one,
        ];

        return self::renderBlade('blog-tips-one', $data, 'Blog');
    }

    // This function sets the addon name
    public function addon_title()
    {
        return __("Blog Tips One Addon");
    }
}
