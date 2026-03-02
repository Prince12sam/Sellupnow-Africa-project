<?php

namespace Modules\Blog\app\Http\PageBuilder\Addons;

use App\Models\Backend\Category;
use Modules\Blog\app\Models\Blog;
use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Number;
use plugins\PageBuilder\Fields\Select;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\PageBuilderBase;

class AllBlog extends PageBuilderBase
{
    // This function return the image name of the addon
    public function preview_image()
    {
        return 'all-blog.png';
    }

    // This function points the location of the image, It accept only module name
    public function setAssetsFilePath()
    {
        return externalAddonImagepath('Blog');
    }

    // This function contains addon settings while using the addon in the page builder
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

    // This function will render the addon on frontend, you can get the inputted values passed from the admin_render function
    public function frontend_render()
    {

        $settings = $this->get_settings();
        $padding_top = $settings['padding_top'];
        $padding_bottom = $settings['padding_bottom'];
        $section_bg = $settings['section_bg'];
        $title = $settings['title'] ?? '';

        $order_by =$settings['order_by'];
        $IDorDate =$settings['order'];
        $items =$settings['items'];

        $all_blogs = Blog::with('category')->where('status','publish')
            ->OrderBy($order_by,$IDorDate)
            ->paginate($items);

        $blog_category_ids = $all_blogs->pluck('category_id')->unique();
        $blog_categories = Category::whereIn('id', $blog_category_ids)->take(500)->get();


        // readable values must be pass via an array
        $data = [
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_bg' => $section_bg,
            'title' => $title,
            'all_blogs' => $all_blogs,
            'blog_categories' => $blog_categories,
        ];

        return self::renderBlade('all-blog', $data, 'Blog');
    }

    // This function sets the addon name
    public function addon_title()
    {
        return __("All Blog Addon");
    }
}
