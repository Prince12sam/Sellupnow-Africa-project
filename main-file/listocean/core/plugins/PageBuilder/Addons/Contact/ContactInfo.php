<?php

namespace plugins\PageBuilder\Addons\Contact;

use App\Helpers\FormBuilderCustom;
use App\Helpers\SanitizeInput;
use App\Models\Backend\FormBuilder;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Select;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\PageBuilderBase;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Helpers\RepeaterField;

class ContactInfo extends PageBuilderBase
{

    public function preview_image()
    {
        return 'contact/contact.jpg';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();


        $output .= Text::get([
            'name' => 'address',
            'label' => __('Address'),
            'value' => $widget_saved_values['address'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'email',
            'label' => __('Email'),
            'value' => $widget_saved_values['email'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'phone',
            'label' => __('Phone'),
            'value' => $widget_saved_values['phone'] ?? null,
        ]);



        //share icons repeater
        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'contact_page_contact_info_share_icons',
            'fields' => [
                [
                    'type' => RepeaterField::ICON_PICKER,
                    'name' => 'icon',
                    'label' => __('Icon')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'icon_link',
                    'label' => __('Link')
                ]
            ]
        ]);

        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title']
        ]);

        $output .= Text::get([
            'name' => 'sub_title',
            'label' => __('Sub Title'),
            'value' => $widget_saved_values['sub_title'] ?? null,
        ]);

        $output .= Select::get([
            'name' => 'custom_form_id',
            'label' => __('Custom Form'),
            'placeholder' => __('Select form'),
            'options' => FormBuilder::all()->pluck('title','id')->toArray(),
            'value' =>   $widget_saved_values['custom_form_id'] ?? []
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

    public function frontend_render()
    {

        $settings = $this->get_settings();
        $padding_top = $settings['padding_top'] ?? '';
        $padding_bottom = $settings['padding_bottom'] ?? '';
        $address = $settings['address'] ?? '';
        $email = $settings['email'] ?? '';
        $phone = $settings['phone'] ?? '';
        $sub_title = $settings['sub_title'] ?? '';

        $repeater_data_share_icons = $settings['contact_page_contact_info_share_icons'] ?? '';
        $contact_background_image = $settings['contact_background_image'] ?? '';
        $image = render_image_markup_by_attachment_id($contact_background_image);

        $custom_form_id = SanitizeInput::esc_html($this->setting_item('custom_form_id'));
        $title = $settings['title'] ?? '';

        if (!empty($custom_form_id)){
            $form = FormBuilder::find($custom_form_id);
            $form_details =  FormBuilderCustom::render_form(optional($form)->id,null,null,'btn-default');
        }

        return $this->renderBlade('contact.contact-info',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'address' => $address,
            'email' => $email,
            'phone' => $phone,
            'repeater_data_share_icons' => $repeater_data_share_icons,
            'image' => $image,
            'title' => $title,
            'sub_title' => $sub_title,
            'form_details' => $form_details,
        ]);
    }
    public function addon_title()
    {
        return __('Contact Info Addon');
    }
}
