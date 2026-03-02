<?php

namespace plugins\WidgetsBuilder\Widgets;
use App\Models\Backend\SubCategory as SubcategoryModel;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\WidgetsBuilder\WidgetBase;
use plugins\PageBuilder\Fields\Number;
use plugins\PageBuilder\Fields\Select;
use plugins\PageBuilder\Fields\Text;

class SubCategory extends WidgetBase
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

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $settings = $this->get_settings();
        $title_text = purify_html($settings['title'] ?? '');
        $order_by = purify_html($settings['order_by'] ?? 'id');
        $IDorDate = purify_html($settings['order'] ?? 'asc');
        $items = purify_html($settings['items'] ?? '');
        $subcategories = SubCategoryModel::whereHas('listings')->where('status',1)->select('id','name','slug')->orderBy($order_by,$IDorDate)->take($items)->get();

        $subcategory_markup = '';

       foreach ($subcategories as $sub_cat){
      $route = route('frontend.show.listing.by.subcategory', $sub_cat->slug ?? 'x');
       $sub_category_name = $sub_cat->name;
       $subcategory_markup.= <<<CATEGORY
    <li class="listItem wow fadeInUp" data-wow-delay="0.0s"><a class="singleLinks" href="{$route}">{$sub_category_name}</a></li>
CATEGORY;

}

   return <<<HTML
   <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
        <div class="footer-widget widget  mb-24">
         <div class="footer-tittle">
            <h6 class="footerTittle">{$title_text}</h6>
            <ul class="listing">
                {$subcategory_markup}
            </ul>
            </div>
        </div>
    </div>
HTML;
    }

    public function widget_title()
    {
        return __('Sub Category');
    }

}
