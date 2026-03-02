<?php


namespace plugins\PageBuilder\Addons\Listing;

use App\Models\Backend\Category;
use App\Models\Backend\Listing;
use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\Number;
use plugins\PageBuilder\Fields\Select;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\PageBuilder\PageBuilderBase;
use Str;

class CategoryWiseListing extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'category-wise-listing/category-wise-listing.jpg';
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
            'name' => 'explore_all',
            'label' => __('Explore Text'),
            'value' => $widget_saved_values['explore_all'] ?? null,
        ]);

        $output .= Number::get([
            'name' => 'items',
            'label' => __('Items'),
            'value' => $widget_saved_values['items'] ?? null,
            'info' => __('enter how many item you want to show in frontend'),
        ]);


        $output .= Select::get([
            'name' => 'category_id',
            'label' => __('Category'),
            'placeholder' => __('Select Category'),
            'options' => Category::all()->pluck('name','id')->toArray(),
            'value' =>   $widget_saved_values['category_id'] ?? []
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
        $output .= ColorPicker::get([
            'name' => 'btn_color',
            'label' => __('Button Background Color'),
            'value' => $widget_saved_values['btn_color'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);

        $output .= ColorPicker::get([
            'name' => 'button_text_color',
            'label' => __('Button Text Color'),
            'value' => $widget_saved_values['button_text_color'] ?? null,
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
        $section_title =$settings['title'];
        $explore_text =$settings['explore_all'];
        $items =$settings['items'];
        $padding_top = $settings['padding_top'] ?? '';
        $padding_bottom = $settings['padding_bottom'] ?? '';
        $section_bg = $settings['section_bg'] ?? '';
        $btn_color = $settings['btn_color'] ?? '';
        $button_text_color = $settings['button_text_color'] ?? '';
        $category_id = $settings['category_id'] ?? 0;

        //static text helpers
        $static_text = static_text();

        $listings = Listing::where(['status' => 1,'is_published' => 1])
        ->where('category_id', $category_id);

        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
            $listings->orWhere(function ($query) use ($category_id) {
               $query->whereHas('user_membership', function ($sub) use ($category_id) {
                   $sub->where('category_id',$category_id);
               });
            });
        }

        $listings = $listings->take($items)
        ->inRandomOrder()
        ->where('status', 1)
        ->where('is_published', 1)
        ->get();

        // Retrieve category name
        $category = Category::find($category_id);
        $category_name = $category ? $category->name : '';

        return $this->renderBlade('listing.category-wise-listing',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_bg' => $section_bg,
            'section_title' => $section_title,
            'explore_text' => $explore_text,
            'listings' => $listings,
            'btn_color' => $btn_color,
            'button_text_color' => $button_text_color,
            'static_text' => $static_text,
            'category_name' => $category_name,
            'category_id' => $category_id,
        ]);
}

    public function addon_title()
    {
        return __('Category wise listing');
    }
}
