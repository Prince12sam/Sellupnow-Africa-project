<?php


namespace plugins\PageBuilder\Fields;


use plugins\PageBuilder\Helpers\Traits\FieldInstanceHelper;
use plugins\PageBuilder\PageBuilderField;

class Switcher extends PageBuilderField
{
    use FieldInstanceHelper;

    /**
     * render field markup
     * */
    public function render()
    {
        // Implement render() method.
        $output = '';
        $output .= $this->field_before();
        $output .= $this->label();
        $checked = !empty($this->value()) ? 'checked' : '';
        $output .='<div class="switch_box style_7"><input type="checkbox" name="'.$this->name().'"  '.$checked.' ><label></label></div>';
        $output .= $this->field_after();

        return $output;
    }
}
