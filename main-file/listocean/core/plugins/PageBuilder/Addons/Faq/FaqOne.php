<?php


namespace plugins\PageBuilder\Addons\Faq;

use plugins\PageBuilder\Fields\IconPicker;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\PageBuilderBase;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use App\Category;
use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Fields\Textarea;
use plugins\PageBuilder\Helpers\RepeaterField;
use App\ServiceCity;
use App\User;

class FaqOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'faq/faq-one.jpg';
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
            'name' => 'contact_info',
            'label' => __('Contact Info'),
            'value' => $widget_saved_values['contact_info'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'contact_info_title',
            'label' => __('Contact Info Title'),
            'value' => $widget_saved_values['contact_info_title'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'contact_info_link',
            'label' => __('Contact Info Link'),
            'value' => $widget_saved_values['contact_info_link'] ?? null,
        ]);


        //repeater
        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'contact_page_contact_info_01',
            'fields' => [
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'title',
                    'label' => __('Title')
                ],
                [
                    'type' => RepeaterField::TEXTAREA,
                    'name' => 'description',
                    'label' => __('Details'),
                    'info' => __('new line count as a separate text')
                ],

            ]
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
        $title = $settings['title'];
        $contact_info = $settings['contact_info'];
        $contact_info_title = $settings['contact_info_title'];
        $contact_info_link = $settings['contact_info_link'];

        $repeater_data = $settings['contact_page_contact_info_01'];

        return $this->renderBlade('faq.faq-one',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_bg' => $section_bg,
            'title' => $title,
            'contact_info' => $contact_info,
            'contact_info_title' => $contact_info_title,
            'contact_info_link' => $contact_info_link,
            'repeater_data' => $repeater_data
        ]);
}

    public function addon_title()
    {
        return __('Faq One');
    }
}
