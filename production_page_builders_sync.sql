-- ============================================================
-- SellUpNow — Homepage page_builders sync  [PRODUCTION-FIXED]
-- Run this on the PRODUCTION server MySQL (listocean)
-- ============================================================
-- Changes from original draft:
--   * addon_page_id changed from 3 → 1  (production home page IS id=1)
--   * home_page static_option kept as 1  (was wrongly set to 3)
--   * Section 0 added: renames duplicate "Car & Vehicles" (id=2) to "Electronics"
-- IMPORTANT NOTES:
--   1. category_id=1 = Car & Vehicles, category_id=2 will become Electronics
--      (production had id=2 as a duplicate "Car & Vehicles"; fixed in Section 0 below)
--   2. Hero background images are local-only. After running this script,
--      re-upload hero images via Admin > Media and set in Page Builder > HeaderStyleOne.
--   3. The banner ad slot (sellupnow:homepage_after_hero) shows nothing until
--      you create an Advertisement in Admin > Advertisements with that slot + status=active.
-- ============================================================

-- ============================================================
-- SECTION 0: Fix duplicate category — rename id=2 to Electronics
-- ============================================================
-- Production had id=1=Car & Vehicles and id=2=Car & Vehicles (duplicate).
-- Rename id=2 to Electronics so CategoryWiseListing works correctly.
UPDATE categories
  SET name = 'Electronics', slug = 'electronics'
  WHERE id = 2 AND name = 'Car & Vehicles';

START TRANSACTION;

-- Step 1: Remove all existing homepage sections (page id=1 is production home)
DELETE FROM page_builders WHERE addon_page_id = 1;

-- Step 2: Insert all homepage sections in order (addon_page_id=1)

-- 1. Hero section
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'HeaderStyleOne',
  NULL,
  'plugins\\PageBuilder\\Addons\\Header\\HeaderStyleOne',
  NULL,
  1,
  1,
  'dynamic_page',
  '{"title":"Sell your product quickly","subtitle":"Start posting listings in minutes","button_text":"Post Your Ad","button_url":"\\/user\\/register","top_image":"","left_image":"","shape_image":"","background_image":"","search_button_title":"Search","hero_enabled":"1","background_image_2":"","background_image_3":"","padding_top":"0","padding_bottom":"0"}'
);

-- 2. Banner Ad (after hero)
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'Advertise',
  NULL,
  'plugins\\PageBuilder\\Addons\\Advertisement\\Advertise',
  NULL,
  2,
  1,
  'dynamic_page',
  '{"advertisement_type":"image","advertisement_size":"950*200","container_class":"center","padding_top":30,"padding_bottom":30,"ad_slot":"sellupnow:homepage_after_hero"}'
);

-- 3. Browse Categories
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'BrowseCategoryOne',
  NULL,
  'plugins\\PageBuilder\\Addons\\BrowseCategory\\BrowseCategoryOne',
  NULL,
  3,
  1,
  'dynamic_page',
  '{"title":"Browse Categories","order_by":"id","order":"desc","items":8,"empty_category_show_hide":"on"}'
);

-- 4. Top Listings
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'TopListingOne',
  NULL,
  'plugins\\PageBuilder\\Addons\\Listing\\TopListingOne',
  NULL,
  4,
  1,
  'dynamic_page',
  '{"title":"Top Listings","explore_all":"See All","items":8}'
);

-- 5. Category Wise Listing — Electronics (id=2, renamed by Section 0 above)
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'CategoryWiseListing',
  NULL,
  'plugins\\PageBuilder\\Addons\\Listing\\CategoryWiseListing',
  NULL,
  5,
  1,
  'dynamic_page',
  '{"title":"Electronics","explore_all":"See all","items":8,"category_id":2}'
);

-- 6. Category Wise Listing — Car & Vehicles (id=1)
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'CategoryWiseListing',
  NULL,
  'plugins\\PageBuilder\\Addons\\Listing\\CategoryWiseListing',
  NULL,
  6,
  1,
  'dynamic_page',
  '{"title":"Car \u0026 Vehicles","explore_all":"See all","items":8,"category_id":1}'
);

-- 7. Marketplace / About section
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'MarketPlaceOne',
  NULL,
  'plugins\\PageBuilder\\Addons\\Marketplace\\MarketPlaceOne',
  NULL,
  7,
  1,
  'dynamic_page',
  '{"title":"Earn cash by selling or Find anything you desire","subtitle":"Earn cash by selling your pre-loved or new items on our platform or you can find anything on our platform you desire.","section_bg":"","section_bg_image":"","banner_image_one":"","button_one_title":"Get Started","button_one_link":"\\/login","button_one_show_hide":"on","button_two_title":"Register","button_two_link":"\\/user\\/register","button_two_show_hide":"on","app_section_title":"Get the App","app_section_subtitle":"Buy & sell on the go"}'
);

-- 8. Recent Listings
INSERT INTO page_builders
  (addon_name, addon_type, addon_namespace, addon_location, addon_order, addon_page_id, addon_page_type, addon_settings)
VALUES (
  'RecentListingOne',
  NULL,
  'plugins\\PageBuilder\\Addons\\Listing\\RecentListingOne',
  NULL,
  8,
  1,
  'dynamic_page',
  '{"title":"Recent Listing","explore_all":"See All","items":8}'
);

COMMIT;

-- Verify result
SELECT id, addon_name, addon_order, addon_page_type FROM page_builders WHERE addon_page_id=1 ORDER BY addon_order;

-- ============================================================
-- SECTION 2: Critical static_options fixes
-- ============================================================

-- Keep homepage pointing to page_id=1 (production correct value — do NOT change to 3)
INSERT INTO static_options (option_name, option_value)
  VALUES ('home_page', '1')
  ON DUPLICATE KEY UPDATE option_value = '1';

-- Listings filter page URL
INSERT INTO static_options (option_name, option_value)
  VALUES ('listing_filter_page_url', 'listings')
  ON DUPLICATE KEY UPDATE option_value = 'listings';

-- ============================================================
-- SECTION 3: Verify categories after Section 0 fix
-- ============================================================
SELECT id, name, slug, status FROM categories ORDER BY id LIMIT 10;

-- ============================================================
-- SECTION 4: Show categories even when empty (important!)
-- ============================================================
UPDATE page_builders
  SET addon_settings = JSON_SET(addon_settings, '$.empty_category_show_hide', 'on')
  WHERE addon_name = 'BrowseCategoryOne' AND addon_page_id = 1;

-- ============================================================
-- AFTER RUNNING THIS SCRIPT — do these steps on the server:
-- ============================================================
-- 1. php artisan cache:clear
-- 2. php artisan view:clear
-- 3. php artisan config:clear
-- 4. Visit https://www.sellupnow.com/ to verify
-- 5. Re-upload hero banner images via Admin > Media,
--    then set them in Admin > Page Builder > HeaderStyleOne
-- 6. If you want the banner ad, go to Admin > Advertisements,
--    create a new ad with slot = sellupnow:homepage_after_hero
-- ============================================================
