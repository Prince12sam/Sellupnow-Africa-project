<?php


namespace plugins\PageBuilder\Addons\Team;

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

class TeamOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'team/team-one.jpg';
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

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'our_team_01',
            'fields' => [
                [
                    'type' => RepeaterField::IMAGE,
                    'name' => 'image',
                    'label' => __('Image')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'name',
                    'label' => __('Name')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'designation',
                    'label' => __('Designation')
                ],
                [
                    'type' => RepeaterField::ICON_PICKER,
                    'name' => 'social_icon_one',
                    'label' => __('Social Media Icon One')
                ],
                [
                    'type' => RepeaterField::ICON_PICKER,
                    'name' => 'social_icon_two',
                    'label' => __('Social Media Icon Two')
                ],
                [
                    'type' => RepeaterField::ICON_PICKER,
                    'name' => 'social_icon_three',
                    'label' => __('Social Media Icon Three')
                ],
                [
                    'type' => RepeaterField::ICON_PICKER,
                    'name' => 'social_icon_four',
                    'label' => __('Social Media Icon Four')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'social_icon_link_one',
                    'label' => __('Social Media Icon Link One')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'social_icon_link_two',
                    'label' => __('Social Media Icon Link Two')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'social_icon_link_three',
                    'label' => __('Social Media Icon Link Three')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'social_icon_link_four',
                    'label' => __('Social Media Icon Link Four')
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
        $title =$settings['title'] ?? '';
        $subtitle = $settings['subtitle'] ?? '';
        $repeater_data = $settings['our_team_01'];

        return $this->renderBlade('team.team-one',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_bg' => $section_bg,
            'title' => $title,
            'subtitle' => $subtitle,
            'repeater_data' => $repeater_data
        ]);

    }

    public function addon_title()
    {
        return __('Team One');
    }
}
