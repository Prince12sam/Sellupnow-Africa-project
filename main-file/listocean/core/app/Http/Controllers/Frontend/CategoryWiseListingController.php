<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\Listing;
use App\Models\Backend\SubCategory;
use Illuminate\Http\Request;

class CategoryWiseListingController extends Controller
{

    public function showListingsByCategory($slug = null)
    {

        $category = Category::where('slug',$slug)->first();

        if (empty($category)){
            return redirect_404_page();
        }

        $subcategory_under_category = Subcategory::where('category_id',$category->id)->orderBy('name','asc')->take(20)->get()->transform(function($item) {
            $item->total_listings = Listing::where('sub_category_id',$item->id)->count();
            return $item;
        });

        $all_listings = collect([]);
        $listings_query = Listing::query();
        $listings_query->with('user');

        $memberIds = [0];
        // get all users ids from the users table according to listing table datas
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')){
            $memberIds = Listing::query()->select('listings.user_id')
                ->join('user_memberships', 'user_memberships.user_id','=','listings.user_id')
                ->whereNot('listings.user_id', 0)
                ->where('user_memberships.expire_date','>=',date('Y-m-d'))
                ->distinct()
                ->pluck('user_id')->push(0)->toArray(); // this gives us the user ids

            if(!is_null($category)){
                $all_listings = $listings_query
                    ->leftJoin('boosts as b', function($join) {
                        $join->on('b.listing_id', '=', 'listings.id')
                             ->where('b.status', 'active')
                             ->where('b.expires_at', '>', now());
                    })
                    ->select('listings.*')
                    ->where(function ($query) use ($memberIds){
                        return $query->whereIn('listings.user_id', $memberIds)
                            ->orWhereNotNull('admin_id');
                    })
                    ->where(['listings.category_id' => $category->id, 'listings.status' => 1, 'listings.is_published' => 1])
                    ->orderByRaw('CASE WHEN b.expires_at > NOW() AND b.status = "active" THEN 1 ELSE 0 END DESC')
                    ->orderByDesc('listings.created_at')
                    ->paginate(10);
            }
        }else{
            if(!is_null($category)){
                $all_listings = $listings_query
                    ->leftJoin('boosts as b', function($join) {
                        $join->on('b.listing_id', '=', 'listings.id')
                             ->where('b.status', 'active')
                             ->where('b.expires_at', '>', now());
                    })
                    ->select('listings.*')
                    ->where(['listings.category_id' => $category->id, 'listings.status' => 1, 'listings.is_published' => 1])
                    ->orderByRaw('CASE WHEN b.expires_at > NOW() AND b.status = "active" THEN 1 ELSE 0 END DESC')
                    ->orderByDesc('listings.created_at')
                    ->paginate(10);
            }
        }


        return view('frontend.pages.listings.category.category-wise-listings', compact(
            'all_listings',
            'category',
            'subcategory_under_category'
        ));
    }

    //sub category wise services
    public function showListingsBySubCategory($slug = null)
    {
        $subcategory = SubCategory::with('category')->where('slug',$slug)->first();

        if (empty($subcategory)){
            return redirect_404_page();
        }

        $child_category_under_category = ChildCategory::where('sub_category_id',$subcategory->id)->orderBy('name','asc')->take(20)->get()->transform(function($item) {
            $item->total_listings = Listing::where('child_category_id',$item->id)->count();
            return $item;
        });

        $all_listings = collect([]);
        $listing_query = Listing::query();
        $listing_query->with('user');

        $memberIds = [0];
        // get all users ids from the users table according to listing table datas
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')){
            $memberIds = Listing::query()->select('listings.user_id')
                ->join('user_memberships', 'user_memberships.user_id','=','listings.user_id')
                ->whereNot('listings.user_id', 0)
                ->where('user_memberships.expire_date','>=',date('Y-m-d'))
                ->distinct()
                ->pluck('user_id')->push(0)->toArray(); // this gives us the user ids
        }

        if(!is_null($subcategory)){
            $all_listings = $listing_query->leftJoin('boosts as b', function($join) {
                    $join->on('b.listing_id', '=', 'listings.id')
                         ->where('b.status', 'active')
                         ->where('b.expires_at', '>', now());
                })
                ->select('listings.*')
                ->where(function ($query) use ($memberIds){
                    return $query->whereIn('listings.user_id', $memberIds)
                        ->orWhereNotNull('listings.admin_id');
                })
                ->where(['listings.sub_category_id' => $subcategory->id, 'listings.status' => 1, 'listings.is_published' => 1])
                ->orderByRaw('CASE WHEN b.expires_at > NOW() AND b.status = "active" THEN 1 ELSE 0 END DESC')
                ->orderByDesc('listings.created_at')
                ->paginate(12);
        }

        // Featured listings for this subcategory
        $featured_listings = Listing::join('featured_ad_activations', 'listings.id', '=', 'featured_ad_activations.listing_id')
            ->where('featured_ad_activations.is_active', 1)
            ->where('featured_ad_activations.ends_at', '>=', now())
            ->where('listings.status', 1)
            ->where('listings.is_published', 1)
            ->where('listings.sub_category_id', $subcategory->id)
            ->select('listings.*')
            ->orderByDesc('featured_ad_activations.starts_at')
            ->limit(4)
            ->get();

        return view('frontend.pages.listings.category.sub-category-wise-listings', compact(
            'all_listings',
            'subcategory',
            'child_category_under_category',
            'featured_listings'
        ));
    }

    public function showListingsByChildCategory($slug = null)
    {
        $child_category = ChildCategory::with('category', 'subcategory')->where('slug',$slug)->first();

        if (empty($child_category)){
            return redirect_404_page();
        }

        $all_listings = collect([]);
        $listing_query = Listing::query();
        $listing_query->with('user');

        $memberIds = [0];
        // get all users ids from the users table according to listing table datas
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')){
            $memberIds = Listing::query()->select('listings.user_id')
                ->join('user_memberships', 'user_memberships.user_id','=','listings.user_id')
                ->whereNot('listings.user_id', 0)
                ->where('user_memberships.expire_date','>=',date('Y-m-d'))
                ->distinct()
                ->pluck('user_id')->push(0)->toArray(); // this gives us the user ids
        }

        if(!is_null($child_category)){
            $all_listings = $listing_query->leftJoin('boosts as b', function($join) {
                    $join->on('b.listing_id', '=', 'listings.id')
                         ->where('b.status', 'active')
                         ->where('b.expires_at', '>', now());
                })
                ->select('listings.*')
                ->where(function ($query) use ($memberIds){
                    return $query->whereIn('listings.user_id', $memberIds)
                        ->orWhereNotNull('listings.admin_id');
                })
                ->where(['listings.child_category_id' => $child_category->id, 'listings.status' => 1, 'listings.is_published' => 1])
                ->orderByRaw('CASE WHEN b.expires_at > NOW() AND b.status = "active" THEN 1 ELSE 0 END DESC')
                ->orderByDesc('listings.created_at')
                ->paginate(12);
        }

        // Featured listings for this child category
        $featured_listings = Listing::join('featured_ad_activations', 'listings.id', '=', 'featured_ad_activations.listing_id')
            ->where('featured_ad_activations.is_active', 1)
            ->where('featured_ad_activations.ends_at', '>=', now())
            ->where('listings.status', 1)
            ->where('listings.is_published', 1)
            ->where('listings.child_category_id', $child_category->id)
            ->select('listings.*')
            ->orderByDesc('featured_ad_activations.starts_at')
            ->limit(4)
            ->get();

        return view('frontend.pages.listings.category.child-category-wise-listings', compact(
            'all_listings',
            'child_category',
            'featured_listings'
        ));
    }



    public function loadMoreSubCategories(Request $request)
    {
        $subcategory_under_category = SubCategory::where('category_id',$request->catId)
            ->orderBy('name','asc')
            ->skip($request->total)
            ->take(12)
            ->get()
            ->transform(function($item) {
            $item->total_listing = Listing::where('sub_category_id',$item->id)->count();
            return $item;
        });
        $markup = '';
        if(!is_null($subcategory_under_category)){
            foreach($subcategory_under_category as $sub_cat){
                $markup .= '<div class="col-lg-3 col-sm-6 margin-top-30 category-child">
                            <div class="single-category style-02 wow fadeInUp" data-wow-delay=".2s">
                                <div class="icon category-bg-thumb-format" '.render_background_image_markup_by_attachment_id($sub_cat->image).'></div>
                                <div class="category-contents">
                                    <h4 class="category-title"> <a href="'. route('frontend.show.listing.by.subcategory',$sub_cat->slug) .'">'. $sub_cat->name.'</a> </h4>
                                    <span class="category-para">  '. sprintf(__('%s Listing'),$sub_cat->total_listing).' </span>
                                </div>
                            </div>
                        </div>';
            }
        }
        return response(['markup' => $markup ,'total' => $request->total + 12]);
    }

    // sub category wish service
    public function loadMoreChildCategories(Request $request)
    {
        $child_category_under_category = ChildCategory::where('sub_category_id',$request->catId)
            ->orderBy('name','asc')
            ->skip($request->total)
            ->take(12)
            ->get()
            ->transform(function($item) {
            $item->total_listing = Listing::where('child_category_id',$item->id)->count();
            return $item;
        });
        $markup = '';
        if(!is_null($child_category_under_category)){
            foreach($child_category_under_category as $child_cat){
                $markup .= '<div class="col-lg-3 col-sm-6 margin-top-30 category-child">
                            <div class="single-category style-02 wow fadeInUp" data-wow-delay=".2s">
                                <div class="icon category-bg-thumb-format" '.render_background_image_markup_by_attachment_id($child_cat->image).'></div>
                                <div class="category-contents">
                                    <h4 class="category-title"> <a href="'. route('frontend.show.listing.by.child.category',$child_cat->slug) .'">'. $child_cat->name.'</a> </h4>
                                    <span class="category-para">  '. sprintf(__('%s Listing'),$child_cat->total_listing).' </span>
                                </div>
                            </div>
                        </div>';
            }
        }
        return response(['markup' => $markup ,'total' => $request->total + 12]);
    }

    public function allListings(\Illuminate\Http\Request $request)
    {
        $listings_query = Listing::query()->with('user')
            ->leftJoin('boosts as b', function($join) {
                $join->on('b.listing_id', '=', 'listings.id')
                     ->where('b.status', 'active')
                     ->where('b.expires_at', '>', now());
            })
            ->select('listings.*')
            ->where('listings.status', 1)
            ->where('listings.is_published', 1);

        // Keyword search
        if (!empty($request->search)) {
            $listings_query->where(function($q) use ($request) {
                $q->where('listings.title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('listings.description', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Category filter
        if (!empty($request->cat)) {
            $listings_query->where('listings.category_id', $request->cat);
        }

        // Subcategory filter
        if (!empty($request->subcat)) {
            $listings_query->where('listings.sub_category_id', $request->subcat);
        }

        // Child category filter
        if (!empty($request->childcat)) {
            $listings_query->where('listings.child_category_id', $request->childcat);
        }

        // Price range filter (format: "min,max")
        if (!empty($request->price_range) && $request->price_range !== '0') {
            $priceParts = explode(',', $request->price_range);
            if (count($priceParts) === 2) {
                $minPrice = (float) $priceParts[0];
                $maxPrice = (float) $priceParts[1];
                if ($minPrice > 0) {
                    $listings_query->where('listings.price', '>=', $minPrice);
                }
                if ($maxPrice > 0) {
                    $listings_query->where('listings.price', '<=', $maxPrice);
                }
            }
        }

        // Listing type preferences
        if (!empty($request->listing_type_preferences)) {
            if ($request->listing_type_preferences === 'featured') {
                $listings_query->where('listings.is_featured', 1);
            } elseif ($request->listing_type_preferences === 'top_listing') {
                $listings_query->join('featured_ad_activations as faa', function($join) {
                    $join->on('faa.listing_id', '=', 'listings.id')
                         ->where('faa.is_active', 1)
                         ->where('faa.ends_at', '>=', now());
                });
            }
        }

        // Condition filter (new / used)
        if (!empty($request->listing_condition)) {
            $listings_query->where('listings.condition', $request->listing_condition);
        }

        // Date posted filter
        if (!empty($request->date_posted_listing)) {
            switch ($request->date_posted_listing) {
                case 'today':
                    $listings_query->whereDate('listings.created_at', today());
                    break;
                case 'yesterday':
                    $listings_query->whereDate('listings.created_at', today()->subDay());
                    break;
                case 'last_week':
                    $listings_query->where('listings.created_at', '>=', now()->subWeek());
                    break;
            }
        }

        // Sorting
        if (!empty($request->sort_by)) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $listings_query->orderBy('listings.price', 'asc');
                    break;
                case 'price_desc':
                    $listings_query->orderBy('listings.price', 'desc');
                    break;
                case 'oldest':
                    $listings_query->orderBy('listings.created_at', 'asc');
                    break;
                default:
                    $listings_query
                        ->orderByRaw('CASE WHEN b.expires_at > NOW() AND b.status = "active" THEN 1 ELSE 0 END DESC')
                        ->orderByDesc('listings.created_at');
            }
        } else {
            $listings_query
                ->orderByRaw('CASE WHEN b.expires_at > NOW() AND b.status = "active" THEN 1 ELSE 0 END DESC')
                ->orderByDesc('listings.created_at');
        }

        $all_listings = $listings_query->paginate(12);

        $all_categories = Category::where('status', 1)->orderBy('name')->get();

        // Subcategories for selected category
        $all_subcategories = collect();
        if (!empty($request->cat)) {
            $all_subcategories = SubCategory::where('category_id', $request->cat)
                ->where('status', 1)->orderBy('name')->get();
        }

        // Child categories for selected subcategory
        $all_child_categories = collect();
        if (!empty($request->subcat)) {
            $all_child_categories = ChildCategory::where('sub_category_id', $request->subcat)
                ->where('status', 1)->orderBy('name')->get();
        }

        // Max price for slider range
        $max_price = (int) Listing::where('status', 1)->where('is_published', 1)->max('price') ?: 10000;

        // Parse current price range for slider defaults
        $price_range_min = 0;
        $price_range_max = $max_price;
        if (!empty($request->price_range) && $request->price_range !== '0') {
            $priceParts = explode(',', $request->price_range);
            if (count($priceParts) === 2) {
                $price_range_min = (int) $priceParts[0];
                $price_range_max = (int) $priceParts[1];
            }
        }

        return view('frontend.pages.listings.all-listings', compact(
            'all_listings',
            'all_categories',
            'all_subcategories',
            'all_child_categories',
            'max_price',
            'price_range_min',
            'price_range_max'
        ));
    }

}
