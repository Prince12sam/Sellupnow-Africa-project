<?php

namespace plugins\WidgetsBuilder\Widgets;

use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\WidgetsBuilder\WidgetBase;
use plugins\PageBuilder\Fields\Text;

class CommunityWidget extends WidgetBase
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

        $output .= Text::get([
            'name' => 'member_title',
            'label' => __('Become Member Title'),
            'value' => $widget_saved_values['member_title'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'member_link',
            'label' => __('Become Member Link'),
            'value' => $widget_saved_values['member_link'] ?? null,
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $settings = $this->get_settings();
        $title = purify_html($settings['title'] ?? '');
        $member_title = purify_html($settings['member_title'] ?? '');
        $member_link = purify_html($settings['member_link'] ?? '');
        $community_markup = '';
        if($member_link==''){
            $buyer_link = route('user.register',['type'=>'buyer']);
        }
        $community_markup.= <<<SOCIALICON
        <li class="lists">
            <li class="list"><a href="{$member_link}">{$member_title}</a></li>
        </li>

SOCIALICON;


   return <<<HTML
        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
            <div class="footer-widget widget  mb-24">
                <div class="footer-tittle">
                    <h6 class="footerTittle">{$title}</h6>
                    <ul class="listing">
                        {$community_markup}
                    </ul>
                </div>
            </div>
        </div>
HTML;
    }

    public function widget_title()
    {
        return __('Community');
    }

}
