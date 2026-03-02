<?php

namespace plugins\WidgetsBuilder\Widgets;

use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Textarea;
use plugins\PageBuilder\Helpers\RepeaterField;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\WidgetsBuilder\WidgetBase;
use plugins\PageBuilder\Fields\IconPicker;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Fields\Text;

class NewsLetterWidget extends WidgetBase
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

        $output .= Textarea::get([
            'name' => 'description',
            'label' => __('Description'),
            'value' => $widget_saved_values['description'] ?? null,
        ]);

        //repeater
        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'news_letter_info_01',
            'fields' => [
                [
                    'type' => RepeaterField::ICON_PICKER,
                    'name' => 'icon',
                    'label' => __('Icon')
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
        $description = purify_html($settings['description']);
        $repeater_data = $settings['news_letter_info_01'];
        $submit_title = __('Submit');
        $placeholder_title = __('Your Email Address');
        $news_letter_route = route('newsletter.subscription');

        $social_icon_markup = '';
        foreach ($repeater_data['icon_'] as $key => $icon) {
            $icon = $icon;
            $url = $repeater_data['url_'][$key];
            $social_icon_markup.= <<<SOCIALICON
            <a class="wow fadeInUp social" data-wow-delay="0.2s" href="{$url}"> <i class="{$icon}"></i> </a>

SOCIALICON;
    }

   return <<<HTML
   <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
        <div class="footer-widget widget  mb-24">
            <div class="footer-tittle">
                <h6 class="footerTittle">{$title}</h6>
                <div class="footer-pera footer-pera2">
                    <p class="pera wow fadeInUp">{$description}</p>
                </div>
                <div class="footer-form mt-10 wow fadeInRight">
                    <div class="form-row mb-20">
                        <form action="{$news_letter_route}" class="newsletter-footer"  method="post" id="newsletter_subscribe_from_addon">
                            <input class="input" type="email" name="email" placeholder="{$placeholder_title}">
                            <div class="error-message mt-2"></div>
                            <div class="btn-wrapper form-icon">
                                <button class="btn-default btn-rounded subscription_by_email">{$submit_title}</button>
                            </div>
                        </form>
                    </div>
                    <div class="footer-social2 ">
                       {$social_icon_markup}
                    </div>
                </div>
            </div>
        </div>
   </div>
HTML;
    }

    public function widget_title()
    {
        return __('Newsletter Widget');
    }

}
