<?php


namespace plugins\PageBuilder\Addons\Listing;

use App\Models\Backend\Category;
use App\Models\Backend\ListingTag;
use App\Models\Backend\SubCategory;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\Listing;
use Billplz\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\URL;
use Modules\Blog\app\Models\Tag;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use Modules\Membership\app\Models\UserMembership;
use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Number;
use plugins\PageBuilder\Fields\Select;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Switcher;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use plugins\PageBuilder\PageBuilderBase;

class ListingsOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'listings/listings_1.jpg';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();


        $output .= Select::get([
            "name" => "order_by",
            "label" => __("Order By"),
            "options" => [
                "id" => __("ID"),
                "created_at" => __("Date"),
            ],
            "value" => $widget_saved_values["order_by"] ?? null,
            "info" => __("set order by"),
        ]);
        $output .= Select::get([
            "name" => "order",
            "label" => __("Order"),
            "options" => [
                "asc" => __("Accessing"),
                "desc" => __("Decreasing"),
            ],
            "value" => $widget_saved_values["order"] ?? null,
            "info" => __("set order"),
        ]);
        $output .= Number::get([
            "name" => "items",
            "label" => __("Items"),
            "value" => $widget_saved_values["items"] ?? null,
            "info" => __("enter how many item you want to show in frontend"),
        ]);

        $output .= Select::get([
            "name" => "columns",
            "label" => __("Column"),
            "options" => [
                "col-lg-3" => __("04 Column"),
                "col-lg-4" => __("03 Column"),
                "col-lg-6" => __("02 Column"),
                "col-lg-12" => __("01 Column"),
            ],
            "value" => $widget_saved_values["columns"] ?? null,
            "info" => __("set column"),
        ]);

        $output .= Slider::get([
            "name" => "padding_top",
            "label" => __("Padding Top"),
            "value" => $widget_saved_values["padding_top"] ?? 110,
            "max" => 200,
        ]);
        $output .= Slider::get([
            "name" => "padding_bottom",
            "label" => __("Padding Bottom"),
            "value" => $widget_saved_values["padding_bottom"] ?? 110,
            "max" => 200,
        ]);

        // listing filtering option on/off start
        $output .= Switcher::get([
            "name" => "location_on_off",
            "label" => __("Location"),
            "value" => $widget_saved_values["location_on_off"] ?? null,
            "info" => __("Location wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "price_range_on_off",
            "label" => __("Price range"),
            "value" => $widget_saved_values["price_range_on_off"] ?? null,
            "info" => __("Price range wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "country_on_off",
            "label" => __("Country"),
            "value" => $widget_saved_values["country_on_off"] ?? null,
            "info" => __("Country wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "state_on_off",
            "label" => __("State"),
            "value" => $widget_saved_values["state_on_off"] ?? null,
            "info" => __("State wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "city_on_off",
            "label" => __("City"),
            "value" => $widget_saved_values["city_on_off"] ?? null,
            "info" => __("City wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "listing_search_by_text_on_off",
            "label" => __("listing search"),
            "value" =>
                $widget_saved_values["listing_search_by_text_on_off"] ?? null,
            "info" => __("listing search Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "category_on_off",
            "label" => __("Category"),
            "value" => $widget_saved_values["category_on_off"] ?? null,
            "info" => __("Category wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "subcategory_on_off",
            "label" => __("SubCategory"),
            "value" => $widget_saved_values["subcategory_on_off"] ?? null,
            "info" => __("SubCategory wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "child_category_on_off",
            "label" => __("Child Category"),
            "value" => $widget_saved_values["child_category_on_off"] ?? null,
            "info" => __("Child Category wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "rating_on_off",
            "label" => __("Rating Star"),
            "value" => $widget_saved_values["rating_on_off"] ?? null,
            "info" => __("Rating Star wise listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "sort_by_on_off",
            "label" => __("Sort By Star"),
            "value" => $widget_saved_values["sort_by_on_off"] ?? null,
            "info" => __("Sort By listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "date_posted",
            "label" => __("Date Posted"),
            "value" => $widget_saved_values["date_posted"] ?? null,
            "info" => __("Date Posted listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "listing_condition",
            "label" => __("Condition"),
            "value" => $widget_saved_values["listing_condition"] ?? null,
            "info" => __("Condition listing Filtering Hide/Show"),
        ]);

        $output .= Switcher::get([
            "name" => "listing_type_preferences",
            "label" => __("Listing Features Types"),
            "value" => $widget_saved_values["listing_type_preferences"] ?? null,
            "info" => __("Listing Features Types Filtering Hide/Show"),
        ]);
        // listing filtering option on/off end

        $output .= Text::get([
            "name" => "country",
            "label" => __("Country Title Text"),
            "value" => $widget_saved_values["country"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "state",
            "label" => __("State Title Text"),
            "value" => $widget_saved_values["state"] ?? null,
        ]);
        $output .= Text::get([
            "name" => "city",
            "label" => __("City Title Text"),
            "value" => $widget_saved_values["city"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "listing_search_by_text",
            "label" => __("Search Title Text"),
            "value" => $widget_saved_values["listing_search_by_text"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "category",
            "label" => __("Category Title Text"),
            "value" => $widget_saved_values["category"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "subcategory",
            "label" => __("Subcategory Title Text"),
            "value" => $widget_saved_values["subcategory"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "child_category",
            "label" => __("Child Category Title Text"),
            "value" => $widget_saved_values["child_category"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "listing_type_preferences_title",
            "label" => __("Listing Features Types Title Text"),
            "value" => $widget_saved_values["listing_type_preferences_title"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "listing_condition_title",
            "label" => __("Condition Title Text"),
            "value" => $widget_saved_values["listing_condition_title"] ?? null,
        ]);

        $output .= Text::get([
            "name" => "date_posted_title",
            "label" => __("Date Posted Title Text"),
            "value" => $widget_saved_values["date_posted_title"] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'google_map_maker_icon',
            'label' => __('Google Map Marker Icon'),
            'value' => $widget_saved_values['google_map_maker_icon'] ?? null,
            'dimensions' => '100x100'
        ]);


        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }


    public function frontend_render() : string
    {

        $settings = $this->get_settings();
        $order_by = $settings["order_by"] ?? "";
        $IDorDate = $settings["order"] ?? "";
        $items = $settings["items"] ?? "";
        $columns = $settings["columns"] ?? "";
        $padding_top = $settings["padding_top"] ?? "";
        $padding_bottom = $settings["padding_bottom"] ?? "";

        // google map icon
        $google_map_maker_icon = render_image_markup_by_attachment_id($settings['google_map_maker_icon']) ?? '';
        $pattern = '/<img[^>]+src="([^"]+)"/';
        if (preg_match($pattern, $google_map_maker_icon, $matches)) {
            $imageUrl = $matches[1];
            $google_map_maker_icon = $imageUrl;
        }


        //listing Filtering Hide/Show
        $location_on_off = $settings["location_on_off"] ?? "";
        $price_range_on_off = $settings["price_range_on_off"] ?? "";

        $country_on_off = $settings["country_on_off"] ?? "";
        $state_on_off = $settings["state_on_off"] ?? "";
        $city_on_off = $settings["city_on_off"] ?? "";

        $listing_search_by_text_on_off =  $settings["listing_search_by_text_on_off"] ?? "";
        $category_on_off = $settings["category_on_off"] ?? "";
        $subcategory_on_off = $settings["subcategory_on_off"] ?? "";
        $child_category_on_off = $settings["child_category_on_off"] ?? "";
        $rating_star_on_off = $settings["rating_on_off"] ?? "";
        $sort_by_on_off = $settings["sort_by_on_off"] ?? "";

        $country_text = $settings["country"] ?? __("Select Country");
        $state_text = $settings["state"] ?? __("Select State");
        $city_text = $settings["city"] ?? __("Select City");
        $search_placeholder = $settings["listing_search_by_text"] ??  __("What are you looking for?");

        $category_text = $settings["category"] ?? __("Select Category");
        $subcategory_text = $settings["subcategory"] ?? __("Select Subcategory");
        $child_category_text = $settings["child_category"] ?? __("Select Child Category");

        $date_posted_title = $settings["date_posted_title"] ?? __("Date Posted");
        $date_posted = $settings["date_posted"] ?? "";
        $listing_condition_title = $settings["listing_condition_title"] ?? __("Condition");
        $listing_condition = $settings["listing_condition"] ?? "";
        $listing_type_preferences_title = $settings["listing_type_preferences_title"] ?? __("Listing Type");
        $listing_type_preferences = $settings["listing_type_preferences"] ?? "";

        $text_search_value = request()->get("q") ?? request()->get("home_search");
        $listing_query = Listing::query()->where("status", 1);

        // google map autocomplete address, current location wise filter
        $remote_task_title = '';
        $all_button_filter_value = '';


        $autocomplete_address = request()->get('autocomplete_address');
        $location_city_name = request()->get('location_city_name');

        // lat long wise filter
        $latitude = request()->get('latitude');
        $longitude = request()->get('longitude');

        $radius = 150;
        $distance_radius_km_get = 50;
        if(!empty(get_static_option("google_map_settings_on_off"))){
            if(!empty(request()->get('latitude')) && !empty(request()->get('longitude'))){
                // Calculate the radius in kilometers (adjust as needed)
                $distance_radius_km_get = request()->get('distance_kilometers_value');
                $distance_radius_km = (int) $distance_radius_km_get;

                if($distance_radius_km == 0){
                    $radius = 50;
                }else{
                    $radius = $distance_radius_km;
                }

                $listing_query->selectRaw(
                    "listings.*,
                        (6371 * acos(
                            cos(radians(?)) * cos(radians(listings.lat)) * cos(radians(listings.lon) - radians(?)) +
                            sin(radians(?)) * sin(radians(listings.lat))
                        )) AS distance",
                    [$latitude, $longitude, $latitude])
                    ->havingRaw('distance <= ?', [$radius])
                    ->orderBy('distance', 'asc');

            }
        }

        if (!empty(request()->get("q")) || !empty(request()->get("home_search"))) {
            $search_text = request()->get("home_search", request()->get("q"));
            $listing_query->where(function ($query) use ($search_text) {
                $query->where("title", "LIKE", "%" . $search_text . "%")
                    ->orWhere("description", "LIKE", "%" . $search_text . "%");
            });
        }

        // Filter by price range value
        $listing_min_main_price_set = Listing::select('id', 'price')->get();

        $prices = $listing_min_main_price_set->pluck('price')->map(function ($price) {
            return intval($price);
        })->toArray();

        $max_price = !empty($prices) ? max($prices) : 10000;

        $max_price_start_value = $max_price ?? '10000';
        $min_price = '1';
        $max_price = $max_price ?? '10000';
        if (!empty(request()->get('price_range_value'))) {
            $priceRange = request()->get('price_range_value');
            list($minPrice, $maxPrice) = explode(',', $priceRange);
            $listing_query->whereBetween('price', [$minPrice, $maxPrice]);
            $min_price = $minPrice;
            $max_price = $maxPrice;
        }

        // get country
        if (!empty(request()->get("country"))) {
            $listings_country = Country::find(request()->get("country"));
            $listings_country_ids = [];
            if ($listings_country) {
                $listings_country_ids = $listings_country->states->pluck("id")->toArray();
            }
           $listing_query->whereIn("state_id", $listings_country_ids)->get();
        }

        // get state
        if (!empty(request()->get("state"))) {
            $listing_query->where("state_id", request()->get("state"));
        }

        // get city
        if (!empty(request()->get("city"))) {
            $listing_query->where("city_id", request()->get("city"))->get();
        }

        if (!empty(request()->get("cat"))) {
            $listing_query->where("category_id", request()->get("cat"));
        }

        if (!empty(request()->get("subcat"))) {
            $listing_query->where("sub_category_id", request()->get("subcat"));
        }

        if (!empty(request()->get("child_cat"))) {
            $listing_query->where(
                "child_category_id",
                request()->get("child_cat")
            );
        }


        $tagIds = request()->get("tag_id");
        if (!empty($tagIds)) {
            // Ensure $tagIds is always an array
            $tagIds = is_array($tagIds) ? $tagIds : [$tagIds];
            $listing_tag_wise_ids = ListingTag::whereIn('tag_id', $tagIds)->pluck('listing_id');
            $listing_query->whereIn("id", $listing_tag_wise_ids);
        }

        if (!empty(request()->get("rating"))) {
            $rating = (int) request()->get("rating");
            $listing_query->whereHas("reviews", function ($q) use ($rating) {
                $q->groupBy("reviews.id")
                    ->havingRaw("AVG(reviews.rating) >= ?", [$rating])
                    ->havingRaw("AVG(reviews.rating) < ?", [$rating + 1]);
            });
        }

        // no remove
        $rating_stars = [
            "1" => __("One Star"),
            "2" => __("Two Star"),
            "3" => __("Three Star"),
            "4" => __("Four Star"),
            "5" => __("Five Star"),
        ];

        if (!empty(request()->get("sortby"))) {
            if (request()->get("sortby") == "latest_listing") {
                $listing_query->orderBy("id", "Desc");
            }
            if (request()->get("sortby") == "lowest_price") {
                $listing_query->orderBy("price", "Asc");
            }
            if (request()->get("sortby") == "highest_price") {
                $listing_query->orderBy("price", "Desc");
            }
        }

        // listing condition
        $listing_type_preferences_value = request()->get("listing_type_preferences");
        if (!empty(request()->get("listing_type_preferences"))) {
            if (request()->get("listing_type_preferences") == "featured") {
                $listing_query->where('is_featured', 1);
            }
            if (request()->get("listing_type_preferences") == "top_listing") {
                $listing_query->orderBy('view', 'desc');
            }
        }

        // listing condition
        $listing_condition_value = request()->get("listing_condition");
        if (!empty(request()->get("listing_condition"))) {
            if (request()->get("listing_condition") == "new") {
                $listing_query->where('condition', 'new');
            }
            if (request()->get("listing_condition") == "used") {
                $listing_query->where('condition', 'used');
            }
        }

        // date posted
          $date_posted_value = request()->get("date_posted_listing");
        if (!empty(request()->get("date_posted_listing"))) {
            if (request()->get("date_posted_listing") == "yesterday") {
                $listing_query->whereDate('published_at', now()->subDays(1));
            }
            if (request()->get("date_posted_listing") == "last_week") {
                $listing_query->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }
            if (request()->get("date_posted_listing") == "today") {
                $listing_query->whereDate('published_at', today());
            }
        }

        // no remove
        $sortby_search = [
            "latest_listing" => __("Latest listing"),
            "lowest_price" => __("Lowest Price"),
            "highest_price" => __("Highest Price"),
        ];

        $memberIds = [0];
        // get all users ids from the users table according to listing table datas
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')){
            $memberIds = Listing::query()->select('listings.user_id')
                ->join('user_memberships', 'user_memberships.user_id','=','listings.user_id')
                ->whereNot('listings.user_id', 0)
                ->where('user_memberships.expire_date','>=', date('Y-m-d'))
                ->distinct()
                ->pluck('user_id')
                ->push(0)
                ->toArray(); // this gives us the user ids

            // add filter for check user has set his zone or not
            $all_listings = $listing_query->where(function ($query) use ($memberIds){
                return $query->whereIn('listings.user_id', $memberIds)
                    ->orWhereNotNull('admin_id');
            })
                ->where('status', 1)
                ->where('is_published', 1)
                ->orderBy($order_by,$IDorDate)
                ->paginate($items);

        }else{
            // add filter for check user has set his zone or not
            $all_listings = $listing_query->where('status', 1)
                ->where('is_published', 1)
                ->orderBy($order_by,$IDorDate)
                ->paginate($items);
        }


        $countries = Country::select("id", "country")
            ->where("status", 1)
            ->get();

        $categories = Category::select("id", "name")
            ->where("status", 1)
            ->get();

        $static_text = static_text();
        // no remove
        $google_map_style_class = "";
        $map_showing_btn = "";
        if (!empty(get_static_option("google_map_settings"))) {
            $google_map_style_class = "listing-map-style";
            $map_showing_btn = "d-none";
        }

        // no remove need for city
        if(request()->get("country")){
            $country_id = request()->get("country");
        }else{
            $find_country_id = State::where('id', request()->get("city"))->first();
            $country_id = $find_country_id->country_id ?? 0;
        }

        // if country is disable/off
        if(!empty(request()->get("state"))){
            if(empty($country_on_off)){
                $listing_state_id = request()->get("state");
                $country_find = State::find($listing_state_id);
                $country_id = $country_find->country_id;
            }
        }

        $listings_state = State::where("status", 1)->where("country_id", $country_id)->get();

        //no remove need for city
        $listing_state_id = request()->get("state");
        $listings_city = City::where("status", 1)->where("state_id", $listing_state_id)->get();

        // first check if country and city is disable get area list
        if (empty($country_on_off) && empty($state_on_off)){
            $listings_city = City::where("status", 1)->get();
        }

        //no remove sub category
        $category_id = request()->get("cat");
        $sub_categories = SubCategory::where("status", 1)->where("category_id", $category_id)->get();

        // no remove child category
        $sub_category_id = request()->get("subcat");
        $child_categories = ChildCategory::select("id", "name")
            ->where("status", 1)
            ->where("sub_category_id", $sub_category_id)
            ->get();

        $current_page_url = URL::current();


        // google map section start
        if (!empty(get_static_option("google_map_settings_on_off"))) {
            $listings = $all_listings;
            $map_listings = $listings->makeHidden(["created_at", "updated_at"]);

            // listing return with image and price render
            $map_listings_with_image_url = $map_listings->map(function (
                $listing
            ) {
                $imageUrl = render_image_markup_by_attachment_id(
                    $listing->image
                );
                $listing_main_price = custom_amount_with_currency_symbol(
                    $listing->price
                );


                $is_featured = '';
                if ($listing->is_featured === 1) {
                    $featured_title = __('FEATURED');
                    $is_featured = '<span class="pro-btn2">
                                    <svg width="7" height="10" viewBox="0 0 7 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 0V3.88889H7L3 10V6.11111H0L4 0Z" fill="white"/>
                                    </svg>' . $featured_title . '
                                </span>';
                    }

                if (!empty($listing->published_at)){
                    $listing_published_at = \Carbon\Carbon::parse($listing->published_at)->format('j M Y');
                }else{
                    $listing_published_at = '';
                }

                $listing->image_url = $imageUrl;
                $listing->listing_main_price = $listing_main_price;
                $listing->is_featured_item = $is_featured;
                $listing->listing_published_at = $listing_published_at;

                return $listing;
            });

            $all_listings_list_json = $map_listings_with_image_url;
            $google_api_key = get_static_option("google_map_api_key");
            $listing_details_route = route("frontend.listing.details", "");


            // KM Filter
            $countryCodes = Country::where('status', 1)->pluck('country_code')->toArray();
            $countryCodesStr = implode(',', $countryCodes);

        }else{
            $all_listings_list_json = '';
            $google_api_key = '';
            $listing_details_route = '';
            $latitude = '';
            $longitude = '';
            $countryCodesStr = '';
        }


        // filter with listing card
        $listing_grid_and_list_view = 'grid';
        if(!empty(request()->get("listing_grid_and_list_view"))){
           $listing_grid_and_list_view = request()->get("listing_grid_and_list_view");
        }

        // listing list page url
        $url_search_listings_list = get_static_option('select_home_page_search_listing_page_url') ?? '/listings';

        return $this->renderBlade('listing.listing-one',[
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'columns' => $columns,
            'listing_grid_and_list_view' => $listing_grid_and_list_view,
            'url_search_listings_list' => $url_search_listings_list,

            'state_on_off' => $state_on_off,
            'city_on_off' => $city_on_off,
            'country_on_off' => $country_on_off,
            'country_text' => $country_text,
            'state_text' => $state_text,
            'city_text' => $city_text,

            'listing_search_by_text_on_off' => $listing_search_by_text_on_off,
            'category_on_off' => $category_on_off,
            'subcategory_on_off' => $subcategory_on_off,
            'child_category_on_off' => $child_category_on_off,
            'rating_star_on_off' => $rating_star_on_off,
            'sort_by_on_off' => $sort_by_on_off,
            'google_map_style_class' => $google_map_style_class,
            'map_showing_btn' => $map_showing_btn,
            'static_text' => $static_text,
            'countries' => $countries,
            'categories' => $categories,
            'search_placeholder' => $search_placeholder,
            'text_search_value' => $text_search_value,
            'category_text' => $category_text,
            'subcategory_text' => $subcategory_text,
            'sub_categories' => $sub_categories,
            'child_category_text' => $child_category_text,
            'child_categories' => $child_categories,

            'date_posted_title' => $date_posted_title,
            'date_posted_value' => $date_posted_value,
            'date_posted' => $date_posted,
            'listing_condition_title' => $listing_condition_title,
            'listing_condition_value' => $listing_condition_value,
            'listing_condition' => $listing_condition,
            'listing_type_preferences_title' => $listing_type_preferences_title,
            'listing_type_preferences_value' => $listing_type_preferences_value,
            'listing_type_preferences' => $listing_type_preferences,

            'rating_stars' => $rating_stars,
            'sortby_search' => $sortby_search,
            'listings_state' => $listings_state,
            'listings_city' => $listings_city,
            'current_page_url' => $current_page_url,

            // google map
            'all_listings_list_json' => $all_listings_list_json,
            'google_api_key' => $google_api_key,
            'listing_details_route' => $listing_details_route,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'all_listings' => $all_listings,
            'google_map_maker_icon' => $google_map_maker_icon,
            'distance_radius_km_get' => $distance_radius_km_get,

            // for google map filter
            'autocomplete_address' => $autocomplete_address,
            'location_city_name' => $location_city_name,
            'radius' => $radius,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'location_on_off' => $location_on_off,
            'price_range_on_off' => $price_range_on_off,
            'max_price_start_value' => $max_price_start_value,
            'countryCodesStr' => $countryCodesStr
        ]);

    }

    public function addon_title()
    {
        return __('Listings: 01');
    }
}
