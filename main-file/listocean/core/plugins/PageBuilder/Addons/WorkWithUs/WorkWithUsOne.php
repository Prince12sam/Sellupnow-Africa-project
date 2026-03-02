<?php


namespace plugins\PageBuilder\Addons\WorkWithUs;

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

class WorkWithUsOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'work-with-us/work-with-us-one.jpg';
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
            'name' => 'button_title_one',
            'label' => __('Button Title One'),
            'value' => $widget_saved_values['button_title_one'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_link_one',
            'label' => __('Button Link One'),
            'value' => $widget_saved_values['button_link_one'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_title_two',
            'label' => __('Button Title Two'),
            'value' => $widget_saved_values['button_title_two'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_link_two',
            'label' => __('Button Link Two'),
            'value' => $widget_saved_values['button_link_two'] ?? null,
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
        $title =$settings['title'] ?? '';
        $button_title_one = $settings['button_title_one'] ?? '';
        $button_title_two = $settings['button_title_two'] ?? '';
        $button_link_one = $settings['button_link_one'] ?? '';
        $button_link_two = $settings['button_link_two'] ?? '';


        return $this->renderBlade('work-with-us.work-with-us-one',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_bg' => $section_bg,
            'title' => $title,
            'button_title_one' => $button_title_one,
            'button_title_two' => $button_title_two,
            'button_link_one' => $button_link_one,
            'button_link_two' => $button_link_two,
        ]);

    }

    public function addon_title()
    {
        return __('Work with us One');
    }
}
