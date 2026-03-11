-- ============================================================
-- SellUpNow Category Seed
-- Soft-deletes old placeholder categories, inserts new ones.
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET @now = NOW();

-- Nullify category_id on the 2 test listings so we can hard-delete categories safely
SET foreign_key_checks = 0;
UPDATE listings SET category_id = NULL WHERE deleted_at IS NULL;
-- Hard-delete all existing categories (old test/placeholder data)
DELETE FROM categories;
SET foreign_key_checks = 1;

-- ────────────────────────────────────────────────────────────
-- PARENT CATEGORIES (position drives display order)
-- ────────────────────────────────────────────────────────────
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Vehicles',              'vehicles',              NULL, 1,  1, 'other', @now, @now),
  ('Property',              'property',              NULL, 2,  1, 'other', @now, @now),
  ('Electronics',           'electronics',           NULL, 3,  1, 'other', @now, @now),
  ('Home & Garden',         'home-garden',           NULL, 4,  1, 'other', @now, @now),
  ('Fashion & Beauty',      'fashion-beauty',        NULL, 5,  1, 'other', @now, @now),
  ('Jobs',                  'jobs',                  NULL, 6,  1, 'other', @now, @now),
  ('Services',              'services',              NULL, 7,  1, 'other', @now, @now),
  ('Pets & Animals',        'pets-animals',          NULL, 8,  1, 'other', @now, @now),
  ('Baby & Kids',           'baby-kids',             NULL, 9,  1, 'other', @now, @now),
  ('Sports & Hobbies',      'sports-hobbies',        NULL, 10, 1, 'other', @now, @now),
  ('Business & Industrial', 'business-industrial',   NULL, 11, 1, 'other', @now, @now),
  ('Food & Agriculture',    'food-agriculture',      NULL, 12, 1, 'other', @now, @now);

-- ────────────────────────────────────────────────────────────
-- SUB-CATEGORIES
-- ────────────────────────────────────────────────────────────

-- Vehicles
SET @vehicles = (SELECT id FROM categories WHERE slug = 'vehicles' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Cars',                      'vehicles-cars',                    @vehicles, 1,  1, 'other', @now, @now),
  ('SUVs',                      'vehicles-suvs',                    @vehicles, 2,  1, 'other', @now, @now),
  ('Hatchbacks',                'vehicles-hatchbacks',              @vehicles, 3,  1, 'other', @now, @now),
  ('Sedans',                    'vehicles-sedans',                  @vehicles, 4,  1, 'other', @now, @now),
  ('Coupes',                    'vehicles-coupes',                  @vehicles, 5,  1, 'other', @now, @now),
  ('Convertibles',              'vehicles-convertibles',            @vehicles, 6,  1, 'other', @now, @now),
  ('Pickup Trucks',             'vehicles-pickup-trucks',           @vehicles, 7,  1, 'other', @now, @now),
  ('Minivans',                  'vehicles-minivans',                @vehicles, 8,  1, 'other', @now, @now),
  ('Micro Cars',                'vehicles-micro-cars',              @vehicles, 9,  1, 'other', @now, @now),
  ('Electric Cars',             'vehicles-electric-cars',           @vehicles, 10, 1, 'other', @now, @now),
  ('Hybrid Cars',               'vehicles-hybrid-cars',             @vehicles, 11, 1, 'other', @now, @now),
  ('Motorcycles',               'vehicles-motorcycles',             @vehicles, 12, 1, 'other', @now, @now),
  ('Scooters',                  'vehicles-scooters',                @vehicles, 13, 1, 'other', @now, @now),
  ('Three Wheelers',            'vehicles-three-wheelers',          @vehicles, 14, 1, 'other', @now, @now),
  ('Bicycles',                  'vehicles-bicycles',                @vehicles, 15, 1, 'other', @now, @now),
  ('Trucks',                    'vehicles-trucks',                  @vehicles, 16, 1, 'other', @now, @now),
  ('Buses',                     'vehicles-buses',                   @vehicles, 17, 1, 'other', @now, @now),
  ('Heavy Equipment',           'vehicles-heavy-equipment',         @vehicles, 18, 1, 'other', @now, @now),
  ('Boats',                     'vehicles-boats',                   @vehicles, 19, 1, 'other', @now, @now),
  ('Rowing Boats',              'vehicles-rowing-boats',            @vehicles, 20, 1, 'other', @now, @now),
  ('Jet Skis',                  'vehicles-jet-skis',                @vehicles, 21, 1, 'other', @now, @now),
  ('Vehicle Parts & Accessories','vehicles-parts-accessories',      @vehicles, 22, 1, 'other', @now, @now),
  ('Auto Tools & Equipment',    'vehicles-auto-tools',              @vehicles, 23, 1, 'other', @now, @now),
  ('Tyres & Rims',              'vehicles-tyres-rims',              @vehicles, 24, 1, 'other', @now, @now),
  ('Vehicle Electronics',       'vehicles-electronics',             @vehicles, 25, 1, 'other', @now, @now),
  ('Vehicle Rentals',           'vehicles-rentals',                 @vehicles, 26, 1, 'other', @now, @now),
  ('Other Vehicles',            'vehicles-other',                   @vehicles, 27, 1, 'other', @now, @now);

-- Property
SET @property = (SELECT id FROM categories WHERE slug = 'property' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Houses for Sale',       'property-houses-sale',         @property, 1,  1, 'other', @now, @now),
  ('Houses for Rent',       'property-houses-rent',         @property, 2,  1, 'other', @now, @now),
  ('Apartments for Sale',   'property-apartments-sale',     @property, 3,  1, 'other', @now, @now),
  ('Apartments for Rent',   'property-apartments-rent',     @property, 4,  1, 'other', @now, @now),
  ('Rooms for Rent',        'property-rooms-rent',          @property, 5,  1, 'other', @now, @now),
  ('Land for Sale',         'property-land-sale',           @property, 6,  1, 'other', @now, @now),
  ('Commercial Property',   'property-commercial',          @property, 7,  1, 'other', @now, @now),
  ('Office Space',          'property-office-space',        @property, 8,  1, 'other', @now, @now),
  ('Shops for Rent',        'property-shops-rent',          @property, 9,  1, 'other', @now, @now),
  ('Warehouses',            'property-warehouses',          @property, 10, 1, 'other', @now, @now),
  ('Short Term Rentals',    'property-short-term-rentals',  @property, 11, 1, 'other', @now, @now),
  ('Vacation Rentals',      'property-vacation-rentals',    @property, 12, 1, 'other', @now, @now),
  ('Property Services',     'property-services',            @property, 13, 1, 'other', @now, @now),
  ('Other Property',        'property-other',               @property, 14, 1, 'other', @now, @now);

-- Electronics
SET @electronics = (SELECT id FROM categories WHERE slug = 'electronics' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Mobile Phones',             'electronics-mobile-phones',        @electronics, 1,  1, 'other', @now, @now),
  ('Smartphones',               'electronics-smartphones',          @electronics, 2,  1, 'other', @now, @now),
  ('Tablets',                   'electronics-tablets',              @electronics, 3,  1, 'other', @now, @now),
  ('Laptops',                   'electronics-laptops',              @electronics, 4,  1, 'other', @now, @now),
  ('Desktop Computers',         'electronics-desktops',             @electronics, 5,  1, 'other', @now, @now),
  ('Computer Accessories',      'electronics-computer-accessories', @electronics, 6,  1, 'other', @now, @now),
  ('Printers & Scanners',       'electronics-printers-scanners',    @electronics, 7,  1, 'other', @now, @now),
  ('TVs',                       'electronics-tvs',                  @electronics, 8,  1, 'other', @now, @now),
  ('Home Audio Systems',        'electronics-home-audio',           @electronics, 9,  1, 'other', @now, @now),
  ('Headphones & Earphones',    'electronics-headphones',           @electronics, 10, 1, 'other', @now, @now),
  ('Cameras',                   'electronics-cameras',              @electronics, 11, 1, 'other', @now, @now),
  ('Video Cameras',             'electronics-video-cameras',        @electronics, 12, 1, 'other', @now, @now),
  ('Smart Watches',             'electronics-smart-watches',        @electronics, 13, 1, 'other', @now, @now),
  ('Gaming Consoles',           'electronics-gaming-consoles',      @electronics, 14, 1, 'other', @now, @now),
  ('Video Games',               'electronics-video-games',          @electronics, 15, 1, 'other', @now, @now),
  ('Networking Equipment',      'electronics-networking',           @electronics, 16, 1, 'other', @now, @now),
  ('Power Banks',               'electronics-power-banks',          @electronics, 17, 1, 'other', @now, @now),
  ('Phone Accessories',         'electronics-phone-accessories',    @electronics, 18, 1, 'other', @now, @now),
  ('Electronic Parts',          'electronics-parts',                @electronics, 19, 1, 'other', @now, @now),
  ('Other Electronics',         'electronics-other',                @electronics, 20, 1, 'other', @now, @now);

-- Home & Garden
SET @home = (SELECT id FROM categories WHERE slug = 'home-garden' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Furniture',             'home-furniture',               @home, 1,  1, 'other', @now, @now),
  ('Sofas & Couches',       'home-sofas-couches',           @home, 2,  1, 'other', @now, @now),
  ('Beds & Mattresses',     'home-beds-mattresses',         @home, 3,  1, 'other', @now, @now),
  ('Tables & Chairs',       'home-tables-chairs',           @home, 4,  1, 'other', @now, @now),
  ('Wardrobes',             'home-wardrobes',               @home, 5,  1, 'other', @now, @now),
  ('Kitchen Appliances',    'home-kitchen-appliances',      @home, 6,  1, 'other', @now, @now),
  ('Home Decor',            'home-decor',                   @home, 7,  1, 'other', @now, @now),
  ('Lighting',              'home-lighting',                @home, 8,  1, 'other', @now, @now),
  ('Garden Tools',          'home-garden-tools',            @home, 9,  1, 'other', @now, @now),
  ('Garden Furniture',      'home-garden-furniture',        @home, 10, 1, 'other', @now, @now),
  ('Home Storage',          'home-storage',                 @home, 11, 1, 'other', @now, @now),
  ('Home Improvement Tools','home-improvement-tools',       @home, 12, 1, 'other', @now, @now),
  ('Air Conditioners',      'home-air-conditioners',        @home, 13, 1, 'other', @now, @now),
  ('Water Heaters',         'home-water-heaters',           @home, 14, 1, 'other', @now, @now),
  ('Other Home Items',      'home-other',                   @home, 15, 1, 'other', @now, @now);

-- Fashion & Beauty
SET @fashion = (SELECT id FROM categories WHERE slug = 'fashion-beauty' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Men''s Clothing',       'fashion-mens-clothing',        @fashion, 1,  1, 'other', @now, @now),
  ('Women''s Clothing',     'fashion-womens-clothing',      @fashion, 2,  1, 'other', @now, @now),
  ('Children''s Clothing',  'fashion-childrens-clothing',   @fashion, 3,  1, 'other', @now, @now),
  ('Shoes',                 'fashion-shoes',                @fashion, 4,  1, 'other', @now, @now),
  ('Bags & Handbags',       'fashion-bags-handbags',        @fashion, 5,  1, 'other', @now, @now),
  ('Watches',               'fashion-watches',              @fashion, 6,  1, 'other', @now, @now),
  ('Jewelry',               'fashion-jewelry',              @fashion, 7,  1, 'other', @now, @now),
  ('Beauty Products',       'fashion-beauty-products',      @fashion, 8,  1, 'other', @now, @now),
  ('Hair Products',         'fashion-hair-products',        @fashion, 9,  1, 'other', @now, @now),
  ('Perfumes',              'fashion-perfumes',             @fashion, 10, 1, 'other', @now, @now),
  ('Makeup',                'fashion-makeup',               @fashion, 11, 1, 'other', @now, @now),
  ('Fashion Accessories',   'fashion-accessories',          @fashion, 12, 1, 'other', @now, @now),
  ('Other Fashion',         'fashion-other',                @fashion, 13, 1, 'other', @now, @now);

-- Jobs
SET @jobs = (SELECT id FROM categories WHERE slug = 'jobs' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Accounting Jobs',       'jobs-accounting',              @jobs, 1,  1, 'other', @now, @now),
  ('Administrative Jobs',   'jobs-administrative',          @jobs, 2,  1, 'other', @now, @now),
  ('Customer Service Jobs', 'jobs-customer-service',        @jobs, 3,  1, 'other', @now, @now),
  ('Driver Jobs',           'jobs-driver',                  @jobs, 4,  1, 'other', @now, @now),
  ('Engineering Jobs',      'jobs-engineering',             @jobs, 5,  1, 'other', @now, @now),
  ('Healthcare Jobs',       'jobs-healthcare',              @jobs, 6,  1, 'other', @now, @now),
  ('IT & Software Jobs',    'jobs-it-software',             @jobs, 7,  1, 'other', @now, @now),
  ('Marketing Jobs',        'jobs-marketing',               @jobs, 8,  1, 'other', @now, @now),
  ('Sales Jobs',            'jobs-sales',                   @jobs, 9,  1, 'other', @now, @now),
  ('Construction Jobs',     'jobs-construction',            @jobs, 10, 1, 'other', @now, @now),
  ('Hospitality Jobs',      'jobs-hospitality',             @jobs, 11, 1, 'other', @now, @now),
  ('Teaching Jobs',         'jobs-teaching',                @jobs, 12, 1, 'other', @now, @now),
  ('Security Jobs',         'jobs-security',                @jobs, 13, 1, 'other', @now, @now),
  ('Part-Time Jobs',        'jobs-part-time',               @jobs, 14, 1, 'other', @now, @now),
  ('Internships',           'jobs-internships',             @jobs, 15, 1, 'other', @now, @now),
  ('Remote Jobs',           'jobs-remote',                  @jobs, 16, 1, 'other', @now, @now),
  ('Other Jobs',            'jobs-other',                   @jobs, 17, 1, 'other', @now, @now);

-- Services
SET @services = (SELECT id FROM categories WHERE slug = 'services' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Home Repair',           'services-home-repair',         @services, 1,  1, 'other', @now, @now),
  ('Plumbing Services',     'services-plumbing',            @services, 2,  1, 'other', @now, @now),
  ('Electrical Services',   'services-electrical',          @services, 3,  1, 'other', @now, @now),
  ('Cleaning Services',     'services-cleaning',            @services, 4,  1, 'other', @now, @now),
  ('Moving & Relocation',   'services-moving-relocation',   @services, 5,  1, 'other', @now, @now),
  ('Delivery Services',     'services-delivery',            @services, 6,  1, 'other', @now, @now),
  ('Car Repair',            'services-car-repair',          @services, 7,  1, 'other', @now, @now),
  ('Computer Repair',       'services-computer-repair',     @services, 8,  1, 'other', @now, @now),
  ('Phone Repair',          'services-phone-repair',        @services, 9,  1, 'other', @now, @now),
  ('Beauty Services',       'services-beauty',              @services, 10, 1, 'other', @now, @now),
  ('Catering Services',     'services-catering',            @services, 11, 1, 'other', @now, @now),
  ('Event Planning',        'services-event-planning',      @services, 12, 1, 'other', @now, @now),
  ('Photography',           'services-photography',         @services, 13, 1, 'other', @now, @now),
  ('Graphic Design',        'services-graphic-design',      @services, 14, 1, 'other', @now, @now),
  ('Digital Marketing',     'services-digital-marketing',   @services, 15, 1, 'other', @now, @now),
  ('Web Development',       'services-web-development',     @services, 16, 1, 'other', @now, @now),
  ('Legal Services',        'services-legal',               @services, 17, 1, 'other', @now, @now),
  ('Financial Services',    'services-financial',           @services, 18, 1, 'other', @now, @now),
  ('Education & Tutoring',  'services-education-tutoring',  @services, 19, 1, 'other', @now, @now),
  ('Other Services',        'services-other',               @services, 20, 1, 'other', @now, @now);

-- Pets & Animals
SET @pets = (SELECT id FROM categories WHERE slug = 'pets-animals' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Dogs',                  'pets-dogs',                    @pets, 1,  1, 'other', @now, @now),
  ('Cats',                  'pets-cats',                    @pets, 2,  1, 'other', @now, @now),
  ('Birds',                 'pets-birds',                   @pets, 3,  1, 'other', @now, @now),
  ('Fish',                  'pets-fish',                    @pets, 4,  1, 'other', @now, @now),
  ('Pet Food',              'pets-food',                    @pets, 5,  1, 'other', @now, @now),
  ('Pet Accessories',       'pets-accessories',             @pets, 6,  1, 'other', @now, @now),
  ('Pet Grooming',          'pets-grooming',                @pets, 7,  1, 'other', @now, @now),
  ('Farm Animals',          'pets-farm-animals',            @pets, 8,  1, 'other', @now, @now),
  ('Animal Equipment',      'pets-animal-equipment',        @pets, 9,  1, 'other', @now, @now),
  ('Other Pets',            'pets-other',                   @pets, 10, 1, 'other', @now, @now);

-- Baby & Kids
SET @baby = (SELECT id FROM categories WHERE slug = 'baby-kids' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Baby Clothing',         'baby-clothing',                @baby, 1, 1, 'other', @now, @now),
  ('Baby Toys',             'baby-toys',                    @baby, 2, 1, 'other', @now, @now),
  ('Strollers',             'baby-strollers',               @baby, 3, 1, 'other', @now, @now),
  ('Car Seats',             'baby-car-seats',               @baby, 4, 1, 'other', @now, @now),
  ('Baby Furniture',        'baby-furniture',               @baby, 5, 1, 'other', @now, @now),
  ('Baby Feeding',          'baby-feeding',                 @baby, 6, 1, 'other', @now, @now),
  ('Diapers & Care',        'baby-diapers-care',            @baby, 7, 1, 'other', @now, @now),
  ('Kids Furniture',        'baby-kids-furniture',          @baby, 8, 1, 'other', @now, @now),
  ('Other Baby Items',      'baby-other',                   @baby, 9, 1, 'other', @now, @now);

-- Sports & Hobbies
SET @sports = (SELECT id FROM categories WHERE slug = 'sports-hobbies' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Fitness Equipment',     'sports-fitness-equipment',     @sports, 1,  1, 'other', @now, @now),
  ('Bicycles',              'sports-bicycles',              @sports, 2,  1, 'other', @now, @now),
  ('Camping Gear',          'sports-camping-gear',          @sports, 3,  1, 'other', @now, @now),
  ('Fishing Equipment',     'sports-fishing',               @sports, 4,  1, 'other', @now, @now),
  ('Sports Equipment',      'sports-equipment',             @sports, 5,  1, 'other', @now, @now),
  ('Musical Instruments',   'sports-musical-instruments',   @sports, 6,  1, 'other', @now, @now),
  ('Board Games',           'sports-board-games',           @sports, 7,  1, 'other', @now, @now),
  ('Art Supplies',          'sports-art-supplies',          @sports, 8,  1, 'other', @now, @now),
  ('Collectibles',          'sports-collectibles',          @sports, 9,  1, 'other', @now, @now),
  ('Books & Magazines',     'sports-books-magazines',       @sports, 10, 1, 'other', @now, @now),
  ('Other Hobbies',         'sports-other-hobbies',         @sports, 11, 1, 'other', @now, @now);

-- Business & Industrial
SET @business = (SELECT id FROM categories WHERE slug = 'business-industrial' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Office Equipment',          'business-office-equipment',        @business, 1,  1, 'other', @now, @now),
  ('Office Furniture',          'business-office-furniture',        @business, 2,  1, 'other', @now, @now),
  ('Restaurant Equipment',      'business-restaurant-equipment',    @business, 3,  1, 'other', @now, @now),
  ('Industrial Machinery',      'business-industrial-machinery',    @business, 4,  1, 'other', @now, @now),
  ('Agricultural Equipment',    'business-agricultural-equipment',  @business, 5,  1, 'other', @now, @now),
  ('Manufacturing Equipment',   'business-manufacturing-equipment', @business, 6,  1, 'other', @now, @now),
  ('Medical Equipment',         'business-medical-equipment',       @business, 7,  1, 'other', @now, @now),
  ('Construction Equipment',    'business-construction-equipment',  @business, 8,  1, 'other', @now, @now),
  ('Retail Equipment',          'business-retail-equipment',        @business, 9,  1, 'other', @now, @now),
  ('Other Business Equipment',  'business-other',                   @business, 10, 1, 'other', @now, @now);

-- Food & Agriculture
SET @food = (SELECT id FROM categories WHERE slug = 'food-agriculture' AND deleted_at IS NULL);
INSERT INTO categories (name, slug, parent_id, position, status, type, created_at, updated_at) VALUES
  ('Fruits & Vegetables',   'food-fruits-vegetables',       @food, 1, 1, 'other', @now, @now),
  ('Grains & Cereals',      'food-grains-cereals',          @food, 2, 1, 'other', @now, @now),
  ('Seafood',               'food-seafood',                 @food, 3, 1, 'other', @now, @now),
  ('Meat & Poultry',        'food-meat-poultry',            @food, 4, 1, 'other', @now, @now),
  ('Dairy Products',        'food-dairy-products',          @food, 5, 1, 'other', @now, @now),
  ('Processed Foods',       'food-processed-foods',         @food, 6, 1, 'other', @now, @now),
  ('Organic Foods',         'food-organic-foods',           @food, 7, 1, 'other', @now, @now),
  ('Farming Supplies',      'food-farming-supplies',        @food, 8, 1, 'other', @now, @now),
  ('Agricultural Products', 'food-agricultural-products',   @food, 9, 1, 'other', @now, @now);

-- ────────────────────────────────────────────────────────────
-- VERIFY
-- ────────────────────────────────────────────────────────────
SELECT
  p.name AS parent,
  COUNT(c.id) AS subcategory_count
FROM categories p
LEFT JOIN categories c ON c.parent_id = p.id AND c.deleted_at IS NULL
WHERE p.parent_id IS NULL AND p.deleted_at IS NULL
GROUP BY p.id, p.name
ORDER BY p.position;
