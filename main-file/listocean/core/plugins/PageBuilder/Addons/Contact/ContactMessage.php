<?php

namespace plugins\PageBuilder\Addons\Contact;

use App\Helpers\FormBuilderCustom;
use App\Helpers\SanitizeInput;
use App\Models\Backend\FormBuilder;
use plugins\PageBuilder\Fields\Select;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\PageBuilderBase;

class ContactMessage extends PageBuilderBase
{

    public function preview_image()
    {
        return 'contact/contact_message.jpg';
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
            'value' => $widget_saved_values['title'] ?? 260,
            'max' => 500,
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
        $output .= Select::get([
            'name' => 'custom_form_id',
            'label' => __('Custom Form'),
            'placeholder' => __('Select form'),
            'options' => FormBuilder::all()->pluck('title','id')->toArray(),
            'value' =>   $widget_saved_values['custom_form_id'] ?? []
         ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;

    }

    public function frontend_render()
    {
        $settings = $this->get_settings();
        $static_text = static_text();
        $title = SanitizeInput::esc_html($this->setting_item('title'));
        $custom_form_id = SanitizeInput::esc_html($this->setting_item('custom_form_id'));
        $title = $title ?? $static_text['get_in_touch'];
        $padding_top = $settings['padding_top'];
        $padding_bottom = $settings['padding_bottom'];

        if (!empty($custom_form_id)){
            $form = FormBuilder::find($custom_form_id);
            $form_details =  FormBuilderCustom::render_form(optional($form)->id,null,null,'btn-default');

        }

        return $this->renderBlade('contact.contact-message',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'title' => $title,
            'form_details' => $form_details,
        ]);

    }

    public function addon_title()
    {
        return __('Contact Message');
    }
}
