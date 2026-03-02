<?php


namespace plugins\PageBuilder\Addons\BrowseCategory;

use App\Models\Backend\Category;
use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\IconPicker;
use plugins\PageBuilder\Fields\Number;
use plugins\PageBuilder\Fields\Select;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Switcher;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\PageBuilderBase;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;

class BrowseCategoryOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'category/browse_category_1.jpg';
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

        $output .= Select::get([
            'name' => 'order_by',
            'label' => __('Order By'),
            'options' => [
                'id' => __('ID'),
                'created_at' => __('Date'),
            ],
            'value' => $widget_saved_values['order_by'] ?? null,
            'info' => __('set order by')
        ]);
        $output .= Select::get([
            'name' => 'order',
            'label' => __('Order'),
            'options' => [
                'asc' => __('Accessing'),
                'desc' => __('Decreasing'),
            ],
            'value' => $widget_saved_values['order'] ?? null,
            'info' => __('set order')
        ]);
        $output .= Number::get([
            'name' => 'items',
            'label' => __('Items'),
            'value' => $widget_saved_values['items'] ?? null,
            'info' => __('enter how many item you want to show in frontend'),
        ]);

        $output .= Switcher::get([
            'name' => 'empty_category_show_hide',
            'label' => __('Category'),
            'value' => $widget_saved_values['empty_category_show_hide'] ?? null,
            'info' => __('Enable: The category will be displayed if it has listing or not. Disable: The category will be displayed if it has listing.'),
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
        $output .= ColorPicker::get([
            'name' => 'section_bg',
            'label' => __('Background Color'),
            'value' => $widget_saved_values['section_bg'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }


    public function frontend_render() : string
    {
        $settings = $this->get_settings();
        $title =$settings['title'] ?? '';
        $order_by =$settings['order_by'] ?? 0;
        $IDorDate =$settings['order'] ?? 0;
        $items =$settings['items'] ?? 0;
        $padding_top = $settings['padding_top'] ?? '';
        $padding_bottom = $settings['padding_bottom'] ?? '';
        $section_bg = $settings['section_bg'] ?? '';
        $empty_category =$settings['empty_category_show_hide'] ?? '';


        //static text helpers
        $static_text = static_text();
        if (!empty($empty_category)){
            $all_category = Category::with('listings')
                ->where('status',1)
                ->take($items)
                ->OrderBy($order_by,$IDorDate)
                ->get();
        }else{
            $all_category = Category::with('listings')
                ->where('status',1)
                ->whereHas('listings')
                ->take($items)
                ->OrderBy($order_by,$IDorDate)
                ->get();
        }

        return $this->renderBlade('category.browse-category-one',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'all_category' => $all_category,
            'title' => $title,
            'section_bg' => $section_bg,
        ]);

    }

    public function addon_title()
    {
        return __('Browse Category: 01');
    }
}
