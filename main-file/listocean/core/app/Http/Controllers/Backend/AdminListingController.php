<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\Admin;
use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\IdentityVerification;
use App\Models\Backend\Listing;
use App\Models\Backend\ListingTag;
use App\Models\Backend\SubCategory;
use App\Models\Frontend\GuestListing;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Blog\app\Models\Tag;
use Modules\Brand\app\Models\Brand;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use App\Models\Backend\MetaData;

class AdminListingController extends Controller
{

   public function adminAllListings(){
       $all_listings = Listing::adminListings()->latest()->paginate(10);
       return view('backend.pages.listings.admin-listings.admin-all-listings', compact('all_listings'));
   }

    public function adminChangeStatus($id){
        $listing = Listing::select('id','status')->where('id',$id)->first();
        if($listing->status==1){
            $status = 0;
        }else{
            $status = 1;
        }
        Listing::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new(__('Status Change Success')));
    }

    public function adminListingPublishedStatus($id)
    {
        // First check if the listing exists
        $listing = Listing::find($id);
        if (!$listing) {
            $message = __('Listing not found.');
            toastr()->error($message);
            return redirect()->back();
        }
        // listing publication status
        $listing->is_published = !$listing->is_published;
        $listing->save();

        // Show appropriate message
        if ($listing->is_published) {
            // Listing is published
            $message = __('Listing has been successfully published.');
            toastr()->success($message);
        } else {
            // Listing is unpublished
            $message = __('Listing has been successfully unpublished.');
            toastr()->warning($message);
        }

        return redirect()->back();
    }

    public function adminListingDelete($id){
        try {
            $listing = Listing::findOrFail($id);
            // Delete listing reports
            $listing->listingReports()->delete();
            // Delete listing tags
            $listing->listingTags()->delete();
            // Delete favorite listings
            $listing->listingFavorites()->delete();
            // Finally, delete the listing itself
            $listing->delete();

            return redirect()->back()->with(FlashMsg::item_delete(__('Listing Deleted Success')));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', __('Listing not found.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('An error occurred while deleting the listing.'));
        }
    }

    // search category
    public function adminSearchListing(Request $request)
    {
        $all_listings = Listing::adminListings()->where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->latest()->paginate(10);
        return $all_listings->total() >= 1 ? view('backend.pages.listings.admin-listings.search-listing',
            compact('all_listings'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function adminPaginate(Request $request)
    {
        if($request->ajax()){
            $all_listings = Listing::adminListings()->latest()->paginate(10);
            return view('backend.pages.listings.admin-listings.search-listing', compact('all_listings'))->render();
        }
    }

    public function bulkAction(Request $request){
        Listing::adminListings()->whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function adminAddListing(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'category_id' => 'required',
                'title' => 'required|max:191',
                'description' => 'required|min:150',
                'slug' => 'required',
                'price' => 'required|numeric'
            ], [
                'title.required' => __('The title field is required.'),
                'title.max' => __('The title must not exceed 191 characters.'),
                'description.required' => __('The description field is required.'),
                'description.min' => __('The description must be at least 150 characters.'),
                'slug.required' => __('The slug field is required.'),
                'price.required' => __('The price field is required.'),
                'price.numeric' => __('The price must be a numeric value.')
            ]);

            $admin = Admin::where('id', Auth::guard('admin')->user()->id)->first();
            $listing=Listing::all();
            $present=false;
            foreach($listing as $list)
            {
                if($request->slug == $list->slug)
                {
                    $present=true;
                    break;
                }
            }
            $slug=$request->slug;

            if($present==true)
            {
                $slug= $request->slug . uniqid() . random_int(1000, 9999);


            }
            $slug = !empty($slug) ? $slug : $request->title;

            if(get_static_option('listing_create_status_settings') == 'approved'){
                $status = 1;
            }else{
                $status = 0;
            }

            // video url
            $video_url = null;
            if(!empty($request->video_url)){
                $video_url = getYoutubeEmbedUrl($request->video_url);
            }


            // listing phone number
            $listing_phone = $request->country_code ?? $request->phone;;

            // Create a new listing
            $listing = new Listing();
            $listing->admin_id = $admin->id;
            $listing->category_id = $request->category_id;
            $listing->sub_category_id = $request->sub_category_id;
            $listing->child_category_id = $request->child_category_id;
            $listing->country_id = $request->country_id;
            $listing->state_id = $request->state_id;
            $listing->city_id = $request->city_id;
            $listing->brand_id = $request->brand_id;
            $listing->title = $request->title;
            $listing->slug = Str::slug(purify_html($slug),'-',null);
            $listing->description = $request->description;
            $listing->price = $request->price;
            $listing->negotiable = $request->negotiable ?? 0;
            $listing->phone = $listing_phone;
            $listing->phone_hidden = $request->phone_hidden ?? 0;
            $listing->condition = $request->condition;
            $listing->authenticity = $request->authenticity;
            $listing->image = $request->image;
            $listing->gallery_images = $request->gallery_images;
            $listing->video_url = $video_url;
            $listing->address = $request->address;
            $listing->is_featured =  $request->is_featured ?? 0;
            $listing->status = $status;

            $tags_name = '';
            if (!empty($request->tags)) {
                $tags_name = Tag::whereIn('id', $request->tags)->pluck('name')->implode(', ');
            }
             // listing meta data add
             $Metas = [
                'meta_title'=> purify_html($request->meta_title),
                'meta_tags'=> purify_html($request->meta_tags),
                'meta_description'=> purify_html($request->meta_description),

                'facebook_meta_tags'=> purify_html($request->facebook_meta_tags),
                'facebook_meta_description'=> purify_html($request->facebook_meta_description),
                'facebook_meta_image'=> $request->facebook_meta_image,

                'twitter_meta_tags'=> purify_html($request->twitter_meta_tags),
                'twitter_meta_description'=> purify_html($request->twitter_meta_description),
                'twitter_meta_image'=> $request->twitter_meta_image,
            ];
            $listing->save();
            $listing->metaData()->create($Metas);
            // Retrieve the last inserted ID
            $last_listing_id = $listing->id;

            // create tags
            if ($request->filled('tags')) {
                foreach ($request->tags as $tagId) {
                    ListingTag::create([
                        'listing_id' => $last_listing_id,
                        'tag_id' => $tagId,
                    ]);
                }
            }

            return back()->with(toastr_success(__('Listing Added Success')));
        }


        $categories = Category::where('status', 1)->get();
        $sub_categories = SubCategory::where('status', 1);
        $all_countries = Country::all_countries();
        $all_states = State::all_states();
        $all_cities = City::all_cities();
        $tags = Tag::where('status', 'publish')->get();
        $user = Auth::guard('admin')->user();
        $brands = Brand::where('status', 1)->get();

        return view('backend.pages.listings.admin-listings.add-listing', compact('user', 'brands','categories', 'sub_categories', 'all_countries', 'all_states', 'all_cities', 'tags'));

    }

    public function adminEditListing(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'category_id' => 'required',
                'title' => 'required|max:191',
                'description' => 'required|min:150',
                'slug' => 'required',
                'price' => 'required|numeric'
            ], [
                'title.required' => __('The title field is required.'),
                'title.max' => __('The title must not exceed 191 characters.'),
                'description.required' => __('The description field is required.'),
                'description.min' => __('The description must be at least 150 characters.'),
                'slug.required' => __('The slug field is required.'),
                'price.required' => __('The price field is required.'),
                'price.numeric' => __('The price must be a numeric value.')
            ]);

            // country, state, city
            $admin = Admin::where('id', Auth::guard('admin')->user()->id)->first();
            $listing=Listing::whereNot("id",$id)->get();
            $present=false;
            foreach($listing as $list)
            {
                if($request->slug == $list->slug)
                {
                    
                    $present=true;
                    break;
                }
            }
            $slug=$request->slug;

            if($present==true)
            {
                $slug = $request->slug . uniqid() . random_int(1000, 9999);


            }
            $slug = !empty($slug) ? $slug : $request->title;

            if(get_static_option('listing_create_status_settings') == 'approved'){
                $status = 1;
            }else{
                $status = 0;
            }

            // video url
            $video_url = null;
            if(!empty($request->video_url)){
                $video_url = getYoutubeEmbedUrl($request->video_url);
            }

            // listing phone number
            $listing_phone = $request->country_code ?? $request->phone;;

            // Edit listing
            $listing = Listing::findOrFail($id);
            $listing->admin_id = $admin->id;
            $listing->category_id = $request->category_id;
            $listing->sub_category_id = $request->sub_category_id;
            $listing->child_category_id = $request->child_category_id;
            $listing->country_id = $request->country_id;
            $listing->state_id = $request->state_id;
            $listing->city_id = $request->city_id;
            $listing->brand_id = $request->brand_id;
            $listing->title = $request->title;
            $listing->slug = Str::slug(purify_html($slug),'-',null);
            $listing->description = $request->description;
            $listing->price = $request->price;
            $listing->negotiable = $request->negotiable ?? 0;
            $listing->condition = $request->condition;
            $listing->authenticity = $request->authenticity;
            $listing->phone = $listing_phone;
            $listing->phone_hidden = $request->phone_hidden ?? 0;
            $listing->image = $request->image;
            $listing->gallery_images = $request->gallery_images;
            $listing->video_url = $video_url;
            $listing->address = $request->address;
            $listing->is_featured = $request->is_featured ?? 0;
            $listing->status = $status;


            $tags_name = '';
            if (!empty($request->tags)) {
                $tags_name = Tag::whereIn('id', $request->tags)->pluck('name')->implode(', ');
            }
            $Metas = [
                'meta_title'=> purify_html($request->meta_title),
                'meta_tags'=> purify_html($request->meta_tags ),
                'meta_description'=> purify_html($request->meta_description),

                'facebook_meta_tags'=> purify_html($request->facebook_meta_tags),
                'facebook_meta_description'=> purify_html($request->facebook_meta_description),
                'facebook_meta_image'=> $request->facebook_meta_image,

                'twitter_meta_tags'=> purify_html($request->twitter_meta_tags),
                'twitter_meta_description'=> purify_html($request->twitter_meta_description),
                'twitter_meta_image'=> $request->twitter_meta_image,
            ];
            $listing->save();
            // Retrieve the last inserted ID
            $last_listing_id = $listing->id;

            $metaData=MetaData::where("meta_taggable_id",$listing->id)->first();
            if($metaData)
            {
                $listing->metaData()->update($Metas);
            }
            else
            {
                $listing->metaData()->create($Metas);
            }

            // Edit tags
            if ($request->filled('tags')) {
                $listing->tags()->detach();
                foreach ($request->tags as $tagId) {
                    ListingTag::create([
                        'listing_id' => $last_listing_id,
                        'tag_id' => $tagId,
                    ]);
                }
            }

            return back()->with(toastr_success(__('Listing Updated Success')));
        }


        $listing = Listing::findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $sub_categories = SubCategory::where('status', 1)->get();
        $child_categories = ChildCategory::where('status', 1)->get();
        $all_countries = Country::all_countries();
        $all_states = State::all_states();
        $all_cities = City::all_cities();
        $brands = Brand::where('status', 1)->get();
        $tags = Tag::where('status', 'publish')->get();

        return view('backend.pages.listings.admin-listings.edit-listing', compact('listing', 'brands', 'categories', 'sub_categories', 'child_categories','all_countries', 'all_states', 'all_cities', 'tags'));

    }
}
