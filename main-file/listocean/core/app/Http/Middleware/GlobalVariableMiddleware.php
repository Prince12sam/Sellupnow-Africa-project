<?php

namespace App\Http\Middleware;

use App\Helpers\LanguageHelper;
use App\Models\Backend\Language;
use App\Models\Backend\Menu;
use App\Models\Backend\Page;
use App\Models\Backend\SocialIcon;
use App\Models\Backend\StaticOption;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class GlobalVariableMiddleware
{

    public function handle($request, Closure $next)
    {
        try {
            $lang = LanguageHelper::user_lang_slug();
            if (Request::is('home/01')) {
                $path_variant = '01';
            } elseif (Request::is('home/02')) {
                $path_variant = '02';
            } elseif (Request::is('home/03')) {
                $path_variant = '03';
            } elseif (Request::is('home/04')) {
                $path_variant = '04';
            } else {
                $path_variant = 'auto';
            }

            //make a function to call all static option by home page
            $static_option_arr = [
            'site_white_logo',
            'site_google_analytics',
            'og_meta_image_for_site',
            'site_main_color_one',
            'site_main_color_two',
            'site_secondary_color',
            'site_heading_color',
            'site_paragraph_color',
            'heading_font',
            'heading_font_family',
            'body_font_family',
            'site_rtl_enabled',
            'services_page_slug',
            'about_page_slug',
            'contact_page_slug',
            'blog_page_slug',
            'team_page_slug',
            'faq_page_slug',
            'works_page_slug',
            'site_third_party_tracking_code',
            'site_favicon',
            'home_page_variant',
            'item_license_status',
            'site_script_unique_key',
            'site_meta_'.$lang.'_description',
            'site_meta_'.$lang.'_tags',
            'site_'.$lang.'_title',
            'site_'.$lang.'_tag_line',
        ];

            $cacheKey = 'global_variable_payload:' . $lang . ':' . $path_variant;
            $buildPayload = function () use ($static_option_arr, $path_variant) {
                $all_language = Language::query()->get();
                $primary_menu = Menu::query()->where('status', 'default')->first();
                $all_social_icons = SocialIcon::query()->get();

                $static_field_data = StaticOption::query()
                    ->whereIn('option_name', $static_option_arr)
                    ->pluck('option_value', 'option_name')
                    ->toArray();

                $home_variant_number = $path_variant === 'auto'
                    ? ((string) ($static_field_data['home_page_variant'] ?? get_static_option('home_page_variant')))
                    : $path_variant;

                $navbar_number = Page::query()->select(['id'])->find(16);

                return [
                    'all_language' => $all_language,
                    'primary_menu_id' => optional($primary_menu)->id,
                    'home_variant_number' => $home_variant_number,
                    'all_social_icons' => $all_social_icons,
                    'static_field_data' => $static_field_data,
                    'navbar_number' => $navbar_number,
                ];
            };

            try {
                $shared = Cache::remember($cacheKey, now()->addSeconds(5), $buildPayload);
                // Guard against a poisoned null value in cache
                if (! is_array($shared)) {
                    Cache::forget($cacheKey);
                    $shared = $buildPayload();
                }
            } catch (\Exception $cacheEx) {
                // Cache unavailable — compute fresh without caching
                $shared = $buildPayload();
            }

            $all_language = $shared['all_language'];
            $primary_menu_id = $shared['primary_menu_id'];
            $home_variant_number = $shared['home_variant_number'];
            $all_social_icons = $shared['all_social_icons'];
            $static_field_data = $shared['static_field_data'];
            $navbar_number = $shared['navbar_number'];
        } catch (\Exception $e) {
            // If the database is unavailable (development environment), provide safe defaults
            $lang = 'en';
            $all_language = collect();
            $primary_menu_id = null;
            $home_variant_number = '01';
            $all_social_icons = collect();
            $static_field_data = [];
            $navbar_number = null;
        }

        view()->share([
            'global_static_field_data' => $static_field_data,
            'all_language' => $all_language,
            'user_select_lang_slug' => $lang,
            'home_variant_number' => $home_variant_number,
            'primary_menu' => $primary_menu_id,
            'all_social_icons' => $all_social_icons,
            'navbar_number' => $navbar_number,
        ]);


        return $next($request);
    }
}
