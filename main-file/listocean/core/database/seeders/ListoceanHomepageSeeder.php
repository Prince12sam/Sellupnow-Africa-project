<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the minimum required data for the Listocean frontend homepage to render.
 *
 * After a fresh `migrate:fresh --seed`, the frontend DB (listocean_db) has no
 * `pages` record and no `static_options.home_page` entry, so the homepage blade
 * returns immediately and shows only the navbar.
 *
 * This seeder:
 *   1. Creates a "Home" page record with `layout = 'normal_layout'` so the
 *      dynamic-page-builder-part partial will invoke the PageBuilder.
 *   2. Inserts (or upserts) the `home_page` static option pointing to that page.
 *   3. Inserts a starter set of PageBuilder addons (HeaderStyleOne + core listing
 *      sections) for the homepage so content actually renders.
 *
 * Any addon_settings left as '{}' will cause each addon to render with its own
 * safe defaults — you can customise them via the SellUpNow admin panel after
 * first login.
 */
class ListoceanHomepageSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------ //
        // 1. Create (or reuse) the Home page record
        // ------------------------------------------------------------------ //
        $existingPageId = DB::table('pages')
            ->where('slug', 'home')
            ->value('id');

        if ($existingPageId) {
            $homePageId = (int) $existingPageId;
        } else {
            $homePageId = DB::table('pages')->insertGetId([
                'title'               => 'Home',
                'slug'                => 'home',
                'page_content'        => null,
                'page_class'          => null,
                'layout'              => 'normal_layout',
                'sidebar_layout'      => null,
                'breadcrumb_status'   => 'off',
                'navbar_variant'      => 'default',
                'footer_variant'      => 'default',
                'widget_style'        => null,
                'left_column'         => null,
                'right_column'        => null,
                'visibility'          => 'public',
                'back_to_top'         => 'on',
                'page_builder_status' => 'active',
                'status'              => 'publish',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // ------------------------------------------------------------------ //
        // 2. Set / update the `home_page` static option
        // ------------------------------------------------------------------ //
        $exists = DB::table('static_options')
            ->where('option_name', 'home_page')
            ->exists();

        if ($exists) {
            DB::table('static_options')
                ->where('option_name', 'home_page')
                ->update([
                    'option_value' => $homePageId,
                    'updated_at'   => now(),
                ]);
        } else {
            DB::table('static_options')->insert([
                'option_name'  => 'home_page',
                'option_value' => $homePageId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // ------------------------------------------------------------------ //
        // 3. Seed PageBuilder addons for the homepage
        //    Always delete and re-insert so re-running the seeder fixes stale
        //    settings (e.g. wrong keys from a previous run).
        // ------------------------------------------------------------------ //
        DB::table('page_builders')
            ->where('addon_page_id', $homePageId)
            ->where('addon_page_type', 'dynamic_page')
            ->delete();

        $addons = [
            [
                'addon_name'      => 'HeaderStyleOne',
                'addon_type'      => null,
                'addon_namespace' => 'plugins\\PageBuilder\\Addons\\Header\\HeaderStyleOne',
                'addon_location'  => null,
                'addon_order'     => 1,
                'addon_page_id'   => $homePageId,
                'addon_page_type' => 'dynamic_page',
                // Hero settings: images are configured via admin panel after first login
                'addon_settings'  => json_encode([
                    'title'            => 'Find What You Need',
                    'subtitle'         => 'Browse thousands of listings across Ghana',
                    'top_title'        => 'Welcome to SellUpNow',
                    'padding_top'      => '100',
                    'padding_bottom'   => '100',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'addon_name'      => 'BrowseCategoryOne',
                'addon_type'      => null,
                'addon_namespace' => 'plugins\\PageBuilder\\Addons\\BrowseCategory\\BrowseCategoryOne',
                'addon_location'  => null,
                'addon_order'     => 2,
                'addon_page_id'   => $homePageId,
                'addon_page_type' => 'dynamic_page',
                'addon_settings'  => json_encode([
                    'title'                    => 'Browse Categories',
                    'order_by'                 => 'id',
                    'order'                    => 'asc',
                    'items'                    => '12',
                    // '1' = show categories even when they have no listings yet
                    // Essential on a fresh install — production has no listings on day 1
                    'empty_category_show_hide' => '1',
                    'padding_top'              => '60',
                    'padding_bottom'           => '60',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'addon_name'      => 'ListingsOne',
                'addon_type'      => null,
                'addon_namespace' => 'plugins\\PageBuilder\\Addons\\Listing\\ListingsOne',
                'addon_location'  => null,
                'addon_order'     => 3,
                'addon_page_id'   => $homePageId,
                'addon_page_type' => 'dynamic_page',
                'addon_settings'  => json_encode([
                    'title'    => 'All Listings',
                    'order_by' => 'created_at',
                    'order'    => 'desc',
                    'items'    => '12',
                    'columns'  => '3',
                    'padding_top'    => '60',
                    'padding_bottom' => '60',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'addon_name'      => 'RecentListingOne',
                'addon_type'      => null,
                'addon_namespace' => 'plugins\\PageBuilder\\Addons\\Listing\\RecentListingOne',
                'addon_location'  => null,
                'addon_order'     => 4,
                'addon_page_id'   => $homePageId,
                'addon_page_type' => 'dynamic_page',
                'addon_settings'  => json_encode([
                    'title'          => 'Recent Listings',
                    'explore_all'    => 'View All',
                    'items'          => '8',
                    'padding_top'    => '60',
                    'padding_bottom' => '60',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'addon_name'      => 'TopListingOne',
                'addon_type'      => null,
                'addon_namespace' => 'plugins\\PageBuilder\\Addons\\Listing\\TopListingOne',
                'addon_location'  => null,
                'addon_order'     => 5,
                'addon_page_id'   => $homePageId,
                'addon_page_type' => 'dynamic_page',
                'addon_settings'  => json_encode([
                    'title'          => 'Top Listings',
                    'explore_all'    => 'View All',
                    'items'          => '8',
                    'padding_top'    => '60',
                    'padding_bottom' => '60',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'addon_name'      => 'MarketPlaceOne',
                'addon_type'      => null,
                'addon_namespace' => 'plugins\\PageBuilder\\Addons\\MarketPlace\\MarketPlaceOne',
                'addon_location'  => null,
                'addon_order'     => 6,
                'addon_page_id'   => $homePageId,
                'addon_page_type' => 'dynamic_page',
                // MarketPlaceOne is a CTA/promotional banner — no item count needed
                'addon_settings'  => json_encode([
                    'title'           => 'Sell Faster, Buy Smarter',
                    'subtitle'        => 'Join thousands of buyers and sellers on SellUpNow Africa',
                    'button_one_title' => 'Post a Listing',
                    'button_one_link'  => '/listing/create',
                    'button_two_title' => 'Browse All',
                    'button_two_link'  => '/listings',
                    'padding_top'      => '60',
                    'padding_bottom'   => '60',
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ];

        DB::table('page_builders')->insert($addons);
    }
}
