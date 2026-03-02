<?php

namespace plugins\PageBuilder;
use App\Helpers\ModuleMetaData;
use App\Models\Backend\PageBuilder;

class PageBuilderSetup
{
    private static function register_widgets(): array
    {
        //check module wise widget by set condition
        $addons = [
            \plugins\PageBuilder\Addons\Listing\ListingsOne::class,
            \plugins\PageBuilder\Addons\Listing\TopListingOne::class,
            \plugins\PageBuilder\Addons\Listing\RecentListingOne::class,
            \plugins\PageBuilder\Addons\Listing\CategoryWiseListing::class,
            \plugins\PageBuilder\Addons\BrowseCategory\BrowseCategoryOne::class,
            \plugins\PageBuilder\Addons\MarketPlace\MarketPlaceOne::class,
            \plugins\PageBuilder\Addons\Contact\ContactInfo::class,
            \plugins\PageBuilder\Addons\Contact\ContactMessage::class,
            \plugins\PageBuilder\Addons\About\AboutUsOne::class,
            \plugins\PageBuilder\Addons\Header\HeaderStyleOne::class,
            \plugins\PageBuilder\Addons\Faq\FaqOne::class,
            \plugins\PageBuilder\Addons\Common\RawHTML::class,
            \plugins\PageBuilder\Addons\Common\TextEditor::class,
            \plugins\PageBuilder\Addons\Common\OnlyImage::class,
            \plugins\PageBuilder\Addons\CustomerReview\CustomerReviewOne::class,
            \plugins\PageBuilder\Addons\OurStory\OurStoryOne::class,
            \plugins\PageBuilder\Addons\Team\TeamOne::class,
            \plugins\PageBuilder\Addons\Empowering\EmpoweringOpportunitiesOne::class,
            \plugins\PageBuilder\Addons\WorkWithUs\WorkWithUsOne::class,
            \plugins\PageBuilder\Addons\Advertisement\Advertise::class,
        ];

        $customAddons = (new ModuleMetaData())->getCustomPageBuilderAddonList();
        return array_merge($addons, $customAddons);
    }
    public static function get_admin_panel_widgets(): string
    {
        $widgets_markup = '';
        $widget_list = self::register_widgets();
        foreach ($widget_list as $widget){
            if(!class_exists($widget)){
                continue;
            }
            try {
                $widget_instance = new  $widget();
            }catch (\Exception $e){
                $msg = $e->getMessage();
                throw new \ErrorException($msg);
            }
            if ($widget_instance->enable()){
                $widgets_markup .= self::render_admin_addon_item([
                    'addon_name' => $widget_instance->addon_name(),
                    'addon_namespace' => $widget_instance->addon_namespace(), // new added
                    'addon_title' => $widget_instance->addon_title(),
                    'preview_image' => $widget_instance->get_preview_image($widget_instance->preview_image())
                ]);
            }
        }
        return $widgets_markup;
    }
    private static function render_admin_addon_item($args): string
    {
        return '<li class="ui-state-default widget-handler" data-name="'.$args['addon_name'].'" data-namespace="'.base64_encode($args['addon_namespace']).'">
                    <h4 class="top-part"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$args['addon_title'].$args['preview_image'].'</h4>
                </li>';
    }
    public static function render_widgets_by_name_for_admin($args){
        $widget_class = $args['namespace'];
        if(class_exists($widget_class)){
            $instance = new $widget_class($args);
            if ($instance->enable()){
                return $instance->admin_render();
            }
        }

    }
    public static function render_widgets_by_name_for_frontend($args){
        $widget_class = $args['namespace'];
        if(class_exists($widget_class)){
            $instance = new $widget_class($args);
            if ($instance->enable()){
                return $instance->frontend_render();
            }
        }
    }
    public static function render_frontend_pagebuilder_content_by_location($location): string
    {
        $output = '';
        $all_widgets = PageBuilder::where(['addon_location' => $location])->orderBy('addon_order', 'ASC')->get();
        foreach ($all_widgets as $widget) {
            if(!file_exists(base_path(str_replace(['\\','App'],['/','app'],$widget->addon_namespace).'.php'))){
                continue;
            }
            if( !class_exists($widget->addon_namespace)){
                continue;
            }
            $output .= self::render_widgets_by_name_for_frontend([
                'name' => $widget->addon_name,
                'namespace' => $widget->addon_namespace,
                'location' => $location,
                'id' => $widget->id,
                'column' => $args['column'] ?? false
            ]);
        }
        return $output;
    }
    public static function get_saved_addons_by_location($location): string
    {
        $output = '';
        $all_widgets = PageBuilder::where(['addon_location' => $location])->orderBy('addon_order','asc')->get();
        foreach ($all_widgets as $widget) {
            if(!file_exists(base_path(str_replace(['\\','App'],['/','app'],$widget->addon_namespace).'.php'))){
                continue;
            }
            if( !class_exists($widget->addon_namespace)){
                continue;
            }
            $output .= self::render_widgets_by_name_for_admin([
                'name' => $widget->addon_name,
                'namespace' => $widget->addon_namespace,
                'id' => $widget->id,
                'type' => 'update',
                'order' => $widget->addon_order,
                'page_type' => $widget->addon_page_type,
                'page_id' => $widget->addon_page_id,
                'location' => $widget->addon_location
            ]);
        }
        return $output;
    }
    public static function get_saved_addons_for_dynamic_page($page_type,$page_id): string
    {
        $output = '';
        $all_widgets = PageBuilder::where(['addon_page_type' => $page_type,'addon_page_id' => $page_id])->orderBy('addon_order','asc')->get();
        foreach ($all_widgets as $widget) {
            if(!file_exists(base_path(str_replace(['\\','App'],['/','app'],$widget->addon_namespace).'.php'))){
                continue;
            }
            if( !class_exists($widget->addon_namespace)){
                continue;
            }
            $output .= self::render_widgets_by_name_for_admin([
                'name' => $widget->addon_name,
                'namespace' => $widget->addon_namespace,
                'id' => $widget->id,
                'type' => 'update',
                'order' => $widget->addon_order,
                'page_type' => $widget->addon_page_type,
                'page_id' => $widget->addon_page_id,
                'location' => $widget->addon_location
            ]);
        }

        return $output;
    }
    public static function render_frontend_pagebuilder_content_for_dynamic_page($page_type,$page_id): string
    {
        $output = '';
        $all_widgets = PageBuilder::where(['addon_page_type' => $page_type,'addon_page_id' => $page_id])->orderBy('addon_order','asc')->get();

        foreach ($all_widgets as $widget) {
            if(!file_exists(base_path(str_replace(['\\','App'],['/','app'],$widget->addon_namespace).'.php'))){
                continue;
            }
            if( !class_exists($widget->addon_namespace)){
                continue;
            }
            $output .= self::render_widgets_by_name_for_frontend([
                'name' => $widget->addon_name,
                'namespace' => $widget->addon_namespace,
                'id' => $widget->id,
                'column' => $args['column'] ?? false
            ]);
        }
        return $output;
    }
}
