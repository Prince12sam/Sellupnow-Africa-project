<?php

namespace plugins\WidgetsBuilder\Widgets;

use plugins\PageBuilder\Helpers\RepeaterField;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\WidgetsBuilder\WidgetBase;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Fields\Text;

class CustomMenuWidget extends WidgetBase
{
    use LanguageFallbackForPageBuilder;

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


        //repeater
        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'help_and_support_info_01',
            'fields' => [
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'title',
                    'label' => __('title')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'url',
                    'label' => __('Url')
                ],
            ]
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $settings = $this->get_settings();
        $title = purify_html($settings['title']);
        $repeater_data = $settings['help_and_support_info_01'];
        $social_icon_markup = '';

        foreach ($repeater_data['title_'] as $key => $rep_title) {
            $url = $repeater_data['url_'][$key];
            $social_icon_markup.= <<<SOCIALICON
            <li class="listItem wow fadeInUp" data-wow-delay="0.0s"><a href="{$url}" class="singleLinks"> {$rep_title}</a></li>

SOCIALICON;
    }

   return <<<HTML
   <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6 col-sm-6">
        <div class="footer-widget widget  mb-24">
            <div class="footer-tittle">
                <h6 class="footerTittle">{$title}</h6>
                <ul class="listing">
                      {$social_icon_markup}
                </ul>
            </div>
        </div>
   </div>
HTML;
    }

    public function widget_title()
    {
        return __('Custom Menu Widget');
    }

}
