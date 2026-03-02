<?php

namespace plugins\WidgetsBuilder\Widgets;

use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Helpers\RepeaterField;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\WidgetsBuilder\WidgetBase;
use plugins\PageBuilder\Fields\IconPicker;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Fields\Text;

class ContactInfoWidget extends WidgetBase
{
    use LanguageFallbackForPageBuilder;

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();

        $output .= Image::get([
            'name' => 'image',
            'label' => __('Site Logo'),
            'value' => $widget_saved_values['image'] ?? null,
            'dimensions' => '173x41'
        ]);

        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'address',
            'label' => __('Address'),
            'value' => $widget_saved_values['address'] ?? null,
        ]);
        $output .= IconPicker::get([
            'name' => 'address_icon',
            'label' => __('Address Icon'),
            'value' => $widget_saved_values['address_icon'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'phone',
            'label' => __('Phone'),
            'value' => $widget_saved_values['phone'] ?? null,
        ]);
        $output .= IconPicker::get([
            'name' => 'phone_icon',
            'label' => __('Phone Icon'),
            'value' => $widget_saved_values['phone_icon'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'email',
            'label' => __('Email'),
            'value' => $widget_saved_values['email'] ?? null,
        ]);
        $output .= IconPicker::get([
            'name' => 'email_icon',
            'label' => __('Email Icon'),
            'value' => $widget_saved_values['email_icon'] ?? null,
        ]);

        //repeater
        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'contact_page_contact_info_01',
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
        $address = purify_html($settings['address']);
        $address_icon = purify_html($settings['address_icon']);
        $phone = purify_html($settings['phone']);
        $phone_icon = purify_html($settings['phone_icon']);
        $email = purify_html($settings['email']);
        $email_icon = purify_html($settings['email_icon']);
        $logo = render_image_markup_by_attachment_id($settings['image']);
        $route = route('homepage');
        $social_icon_markup = '';

        if(isset($settings['contact_page_contact_info_01']) && !empty($settings['contact_page_contact_info_01'])){
            $repeater_data = $settings['contact_page_contact_info_01'];
            foreach ($repeater_data['icon_'] as $key => $icon) {
                $icon = $icon;
                $url = $repeater_data['url_'][$key];
                $social_icon_markup.= <<<SOCIALICON
            <a class="wow fadeInUp social" data-wow-delay="0.2s" href="{$url}"> <i class="{$icon}"></i> </a>

SOCIALICON;
            }
        }

    $contact_part_title = '';
    if(isset($title) && !empty($title)){
        $contact_part_title = '<h6 class="footerTittle">' . $title . '</h6>';
    }

   return <<<HTML
   <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
        <div class="footer-widget widget  mb-24">
            <div class="footer-tittle">
                {$contact_part_title}
                <div class="footer-logo mb-40">
                    <a href="{$route}" class="footer-logo">{$logo}</a>
                </div>
                <ul class="listing">
                    <li class="listItem wow fadeInUp" data-wow-delay="0.0s"><a href="#" class="singleLinks"> <i class="{$address_icon} icon"></i> {$address}</a></li>
                    <li class="listItem wow fadeInUp" data-wow-delay="0.0s"> <a href="#" class="singleLinks"> <i class="{$phone_icon} icon"></i> {$phone}</a></li>
                    <li class="listItem wow fadeInUp" data-wow-delay="0.0s"> <a href="#" class="singleLinks"> <i class="{$email_icon} icon"></i> {$email}</a></li>
                </ul>
                 <div class="footer-social2">
                   {$social_icon_markup}
                </div>
            </div>
        </div>
   </div>
HTML;
    }

    public function widget_title()
    {
        return __('Contact Info');
    }

}
