<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\AdminNotification;
use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\IdentityVerification;
use App\Models\Backend\Listing;
use App\Models\Backend\ListingTag;
use App\Models\Backend\MetaData;
use App\Models\Backend\Page;
use App\Models\Backend\SubCategory;
use App\Models\Common\ListingReport;
use App\Models\Frontend\ListingFavorite;
use App\Models\User;
use App\Services\BoostService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Blog\app\Models\Tag;
use Modules\Brand\app\Models\Brand;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use App\Models\Backend\AdVideo;
use Illuminate\Support\Facades\Storage;
use Modules\Membership\app\Models\UserMembership;
use App\Services\MembershipService;

class ListingController extends Controller
{
    protected MembershipService $membershipService;
    protected BoostService $boostService;

    public function __construct(MembershipService $membershipService, BoostService $boostService)
    {
        $this->membershipService = $membershipService;
        $this->boostService      = $boostService;
    }

    private function ownedListingOrFail(int $id): Listing
    {
        return Listing::where('id', $id)
            ->where('user_id', Auth::guard('web')->id())
            ->firstOrFail();
    }

    private function locationTable(string $table)
    {
        try {
            if (config('database.connections.listocean')) {
                return DB::connection('listocean')->table($table);
            }
        } catch (\Throwable $th) {
            // fallback to default connection
        }

        return DB::table($table);
    }


    public function allListing(Request $request)
    {
        $userId    = Auth::guard('web')->id();
        $listings  = Listing::where('user_id', $userId)->latest()->paginate(5);
        $boostPrice       = $this->boostService->boostPrice();
        $freeBoostsLeft   = $this->boostService->freeBoostsRemaining($userId);
        return view('frontend.user.listings.all-listings', compact('listings', 'boostPrice', 'freeBoostsLeft'));
    }

    // add listing page
    public function addListing(Request $request)
    {

        // Check Membership Status
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
            $user_membership_check = UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();
            if ($user_membership_check && $user_membership_check->status === 0 || $user_membership_check->payment_status == 'pending') {
                toastr_error(__('Your membership plan is inactive. Please activate your plan before creating listings.'));
                return redirect()->back();
            }
        }

        if ($request->isMethod('post')) {
            //user Verify check
            if (get_static_option('listing_create_settings') == 'verified_user'){
                $user_identity = IdentityVerification::select('user_id','status')->where('user_id',Auth::guard('web')->user()->id)->first();
                $user_verified_status = $user_identity?->status ?? 0;
                if($user_verified_status != 1 ){
                    toastr_error(__('You are not verified. to add listings you must have to verify your account first'));
                    return redirect()->back();
                }
            }

            //check membership
            if(moduleExists('Membership')){
                if(membershipModuleExistsAndEnable('Membership')){
                    $user_membership = UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();
                    // if user membership is null
                    if(is_null($user_membership)){
                        toastr_error(__('you have to membership a package to create listings'));
                        return redirect()->back();
                    }

                    $user_total_listing_count = Listing::where('user_id', Auth::guard('web')->user()->id)->count();


                    // check user membership all listing limit
                    if ($user_membership->listing_limit == 0 && $user_membership->expire_date <= Carbon::now()){
                        session()->flash('message', __('Your Membership is expired'));
                        return redirect()->back();
                    }elseif ($user_membership->listing_limit === 0){
                        toastr_error(__('Your membership listing limit is over!. please renew it'));
                        return redirect()->back();
                    }elseif ($user_membership->expire_date <= Carbon::now()){
                        toastr_error(__('Your Membership is expired'));
                        return redirect()->back();
                    }

                    // Check if the user has exceeded the allowed number of gallery images
                    $initial_gallery_images = $user_membership->initial_gallery_images;
                    $gallery_images = $request->gallery_images;
                    $gallery_images_input = explode('|', $gallery_images);
                    $gallery_images_input_count = count($gallery_images_input);

                    if ($gallery_images_input_count > $initial_gallery_images) {
                        toastr_error(__('You have exceeded the maximum number of gallery images allowed by your membership package.'));
                        return redirect()->back();
                    }

                    // Check featured listing
                    if (!empty($request->is_featured)){
                        if ($user_membership->initial_featured_listing != 0){
                            if ($user_membership->featured_listing === 0) {
                                toastr_error(__('You have exceeded the maximum number of featured listings allowed by your membership package.'));
                                return redirect()->back();
                            }
                        }
                    }

                }
            }

            // Check our native membership quota
            if (! $this->membershipService->canPostListing(Auth::id())) {
                toastr_error(__('You have reached your listing limit. Please upgrade your membership plan to post more listings.'));
                return redirect()->route('user.membership.plans');
            }

            // Validation start
            $request->validate([
                'category_id' => 'required',
                'title' => 'required|max:191',
                'description' => 'required|min:150',
                'slug' => 'required|max:255',
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

            $user = User::where('id', Auth::guard('web')->user()->id)->first();
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
                $slug = $request->slug . uniqid() . random_int(1000, 9999);


            }
            $slug = !empty($slug) ? $slug : $request->title;

            if(get_static_option('listing_create_status_settings') == 'approved'){
                $status = 1;
            }else{
                $status = 0;
            }

            // video file — attached as AdVideo after listing save
            $userId     = Auth::guard('web')->id();
            $videoQuota = $this->membershipService->getVideoQuota($userId);
            $listing = new Listing();
            $listing->user_id = $user->id;
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
            $listing->phone = $request->phone;
            $listing->phone_hidden = $request->phone_hidden ?? 0;
            $listing->image = $request->image;
            $listing->gallery_images = $request->gallery_images;
            $listing->address = $request->address;
            $listing->lat = $request->latitude;
            $listing->lon = $request->longitude;
            $listing->is_featured =  $request->is_featured ?? 0;
            $listing->escrow_enabled = $request->boolean('escrow_enabled');
            $listing->status = $status;


            $tags_name = '';
            if (!empty($request->tags)) {
                $tags_name = Tag::whereIn('id', $request->tags)->pluck('name')->implode(', ');
            }
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

            // Attach uploaded video (if any) as AdVideo linked to this listing
            if ($request->hasFile('listing_video_file') && $videoQuota !== 0) {
                $videoUsed = AdVideo::where('user_id', $userId)->count();
                if ($videoQuota === -1 || $videoUsed < $videoQuota) {
                    $vPath = $request->file('listing_video_file')->store('ad-videos', 'public');
                    $tPath = null;
                    $b64   = $request->input('listing_video_thumb');
                    if ($b64 && str_starts_with($b64, 'data:image/')) {
                        $imgData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $b64));
                        if ($imgData !== false && strlen($imgData) > 500) {
                            $tFile = 'ad-video-thumbs/' . uniqid('thumb_', true) . '.jpg';
                            Storage::disk('public')->put($tFile, $imgData);
                            $tPath = $tFile;
                        }
                    }
                    AdVideo::create([
                        'user_id'       => $userId,
                        'listing_id'    => $listing->id,
                        'video_url'     => Storage::disk('public')->url($vPath),
                        'thumbnail_url' => $tPath ? Storage::disk('public')->url($tPath) : null,
                        'is_approved'   => 0,
                        'is_rejected'   => 0,
                        'is_sponsored'  => 0,
                    ]);
                    // Sync to listings.video_url so the organic Reels feed can display it
                    $listing->video_url         = Storage::disk('public')->url($vPath);
                    $listing->video_is_approved = 0;
                    $listing->save();
                }
            }

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

            $user_id = Auth::guard('web')->user()->id;

            // if membership system decrement listing limit
            if(moduleExists('Membership')){
                if (membershipModuleExistsAndEnable('Membership')) {
                    // listing limit
                     UserMembership::where('user_id', $user_id)->update([
                        'listing_limit' => DB::raw(sprintf("listing_limit - %s", (int)strip_tags(1))),
                    ]);

                    // is featured listing
                    $user_membership_check = UserMembership::where('user_id', $user_id)->first();
                    if ($user_membership_check->initial_featured_listing != 0){
                        if (!empty($request->is_featured)){
                            UserMembership::where('user_id', $user_id)->update([
                                'featured_listing' => DB::raw(sprintf("featured_listing - %s", (int)strip_tags(1))),
                            ]);
                        }
                    }
                }
            }

            // Track listing usage against native membership quota
            $this->membershipService->incrementListingUsage($user_id);

            //create listing notification to admin
            AdminNotification::create([
                'identity'=> $last_listing_id,
                'user_id'=> $user_id,
                'type'=>'Create Listing',
                'message'=>__('A new project has been created'),
            ]);

            // sent email to admin
            if (get_static_option('listing_create_status_settings') == 'pending'){
                try {
                    $subject = get_static_option('listing_approve_subject') ?? __('New Listing Approve Request');
                    $message = get_static_option('listing_approve_message');
                    $message = str_replace(["@listing_id"], [$last_listing_id], $message);
                    Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                        'subject' => $subject,
                        'message' => $message
                    ]));
                } catch (\Exception $e) {
                    //
                }
            }

            return redirect()->route('user.all.listing')->with(toastr_success(__('Listing Added Success')));
        }


        //check membership
        if(moduleExists('Membership')){
            if(membershipModuleExistsAndEnable('Membership')){
                $user_membership = UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();
                if(is_null($user_membership)){
                    toastr_error(__('you have to membership a package to create listings'));
                    return redirect()->back();
                }
            }
        }

        $categories = Category::where('status', 1)->get();
        $sub_categories = SubCategory::where('status', 1);
        $all_countries = $this->locationTable('countries')->where('status', 1)->orderBy('country')->get();
        $all_states = $this->locationTable('states')->where('status', 1)->orderBy('state')->get();
        $all_cities = $this->locationTable('cities')->where('status', 1)->orderBy('city')->get();
        $tags = Tag::where('status', 'publish')->get();
        $user = Auth::guard('web')->user();
        $brands = Brand::where('status', 1)->get();
        $user_identity_verifications = IdentityVerification::where('user_id', $user->id)->first();

        // if membership module exits
        $membership_page_url = get_static_option('membership_plan_page') ? Page::select('slug')->find(get_static_option('membership_plan_page'))->slug : 'membership';
        $user_featured_listing_enable = false;
        $user_listing_limit_check = false;

        // Use native membership service for quota display
        $nativeMembership = $this->membershipService->activeMembership($user->id);
        if ($nativeMembership && $nativeMembership->plan) {
            $user_featured_listing_enable = (int) $nativeMembership->plan->featured_listing_limit > 0;
            $user_listing_limit_check     = ! $nativeMembership->canPostListing();
        } elseif (! $this->membershipService->canPostListing($user->id)) {
            $user_listing_limit_check = true;
        }

        $videoQuota     = $this->membershipService->getVideoQuota($user->id);
        $videoUsed      = \App\Models\Backend\Listing::where('user_id', $user->id)
                            ->whereNotNull('video_url')->where('video_url', '!=', '')->count();
        $canUploadVideo = $videoQuota !== 0 && ($videoQuota === -1 || $videoUsed < $videoQuota);

        // Fallback: legacy module check (no-op when module not installed)
        if(moduleExists('Membership')){
            if(membershipModuleExistsAndEnable('Membership')){
                $user_membership = UserMembership::where('user_id', $user->id)->first();
                if ($user_membership->featured_listing != 0){
                    $user_featured_listing_enable = true;
                }
                if ($user_membership->listing_limit === 0){
                    $user_listing_limit_check = true;
                }
            }
        }

        return view('frontend.user.listings.add-listing', compact(
            'membership_page_url',
            'user_featured_listing_enable',
            'user_listing_limit_check',
            'canUploadVideo',
            'videoQuota',
            'videoUsed',
            'user',
            'brands',
            'categories',
            'sub_categories',
            'all_countries',
            'all_states',
            'all_cities',
            'tags',
            'user_identity_verifications'
        ));

    }

    // Edit listing page
    public function editListing(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            // Validation start
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
            $user = User::where('id', Auth::guard('web')->user()->id)->first();
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

            // video file — attached as AdVideo after listing save
            $userId     = Auth::guard('web')->id();
            $videoQuota = $this->membershipService->getVideoQuota($userId);
            $listing = $this->ownedListingOrFail((int) $id);
            $was_featured = (int) $listing->is_featured; // capture before any changes
            $listing->user_id = $user->id;
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
            $listing->phone = $request->phone;
            $listing->phone_hidden = $request->phone_hidden ?? 0;
            $listing->image = $request->image;
            $listing->gallery_images = $request->gallery_images;
            $listing->address = $request->address;
            $listing->lat = $request->latitude;
            $listing->lon = $request->longitude;
            $listing->is_featured = $request->is_featured ?? 0;
            $listing->escrow_enabled = $request->boolean('escrow_enabled');
            $listing->status = $status;


            $tags_name = '';
            if (!empty($request->tags)) {
                $tags_name = Tag::whereIn('id', $request->tags)->pluck('name')->implode(', ');
            }
           $Metas = [
                'meta_title'=> purify_html($request->meta_title),
                'meta_tags'=> purify_html($request->meta_tags),
                'meta_description'=> purify_html($request->meta_description),

                'facebook_meta_tags'=> purify_html($request->facebook_meta_tags ),
                'facebook_meta_description'=> purify_html($request->facebook_meta_description),
                'facebook_meta_image'=> $request->facebook_meta_image,

                'twitter_meta_tags'=> purify_html($request->twitter_meta_tags),
                'twitter_meta_description'=> purify_html($request->twitter_meta_description),
                'twitter_meta_image'=> $request->twitter_meta_image,
            ];
            $listing->save();

            // Attach uploaded video (if any) as AdVideo linked to this listing
            if ($request->hasFile('listing_video_file') && $videoQuota !== 0) {
                $existing  = AdVideo::where('listing_id', $listing->id)->where('user_id', $userId)->first();
                $videoUsed = AdVideo::where('user_id', $userId)->count();
                $canAdd    = $existing || $videoQuota === -1 || $videoUsed < $videoQuota;
                if ($canAdd) {
                    // Delete old video/thumb files and record if replacing
                    if ($existing) {
                        $urlToPath = fn(?string $u) => $u
                            ? (preg_replace('#^storage/#', '', ltrim(parse_url($u, PHP_URL_PATH) ?? '', '/')) ?: null)
                            : null;
                        if ($p = $urlToPath($existing->video_url))     Storage::disk('public')->delete($p);
                        if ($p = $urlToPath($existing->thumbnail_url)) Storage::disk('public')->delete($p);
                        $existing->delete();
                    }
                    $vPath = $request->file('listing_video_file')->store('ad-videos', 'public');
                    $tPath = null;
                    $b64   = $request->input('listing_video_thumb');
                    if ($b64 && str_starts_with($b64, 'data:image/')) {
                        $imgData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $b64));
                        if ($imgData !== false && strlen($imgData) > 500) {
                            $tFile = 'ad-video-thumbs/' . uniqid('thumb_', true) . '.jpg';
                            Storage::disk('public')->put($tFile, $imgData);
                            $tPath = $tFile;
                        }
                    }
                    AdVideo::create([
                        'user_id'       => $userId,
                        'listing_id'    => $listing->id,
                        'video_url'     => Storage::disk('public')->url($vPath),
                        'thumbnail_url' => $tPath ? Storage::disk('public')->url($tPath) : null,
                        'is_approved'   => 0,
                        'is_rejected'   => 0,
                        'is_sponsored'  => 0,
                    ]);
                    // Sync to listings.video_url so the organic Reels feed can display it
                    $listing->video_url         = Storage::disk('public')->url($vPath);
                    $listing->video_is_approved = 0;
                    $listing->save();
                }
            }

            // If listing was NOT featured before but is now being featured, consume a membership credit
            if ($was_featured === 0 && !empty($request->is_featured)) {
                if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
                    $user_id = Auth::guard('web')->id();
                    $user_membership_check = UserMembership::where('user_id', $user_id)->first();
                    if (!empty($user_membership_check) && $user_membership_check->initial_featured_listing != 0) {
                        UserMembership::where('user_id', $user_id)->update([
                            'featured_listing' => DB::raw(sprintf("GREATEST(0, featured_listing - %s)", 1)),
                        ]);
                    }
                }
            }

            $metaData=MetaData::where("meta_taggable_id",$listing->id)->first();
            if($metaData)
            {
                $listing->metaData()->update($Metas);
            }
            else
            {
                $listing->metaData()->create($Metas);
            }
           
            // Retrieve the last inserted ID
            $last_listing_id = $listing->id;

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

            // send email to admin
            try {
                $message = get_static_option('service_approve_message');
                $message = str_replace(["@service_id"], [$last_listing_id], $message);
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => get_static_option('service_approve_subject') ?? __('New Listing Approve Request'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
                //
            }

            return back()->with(toastr_success(__('Listing Updated Success')));
        }


        $listing = $this->ownedListingOrFail((int) $id);
        $categories = Category::where('status', 1)->get();
        $sub_categories = SubCategory::where('status', 1)->get();
        $child_categories = ChildCategory::where('status', 1)->get();
        $all_countries = $this->locationTable('countries')->where('status', 1)->orderBy('country')->get();
        $all_states = $this->locationTable('states')->where('status', 1)->orderBy('state')->get();
        $all_cities = $this->locationTable('cities')->where('status', 1)->orderBy('city')->get();
        $brands = Brand::where('status', 1)->get();
        $tags = Tag::where('status', 'publish')->get();

        // if membership module exits
        $membership_page_url = get_static_option('membership_plan_page') ? Page::select('slug')->find(get_static_option('membership_plan_page'))->slug : '';
        $user_featured_listing_enable = false;
        $user_listing_limit_check = false;
        if(moduleExists('Membership')){
            if(membershipModuleExistsAndEnable('Membership')){
                $user_membership = UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();
               if (!empty($user_membership)){
                   if ($user_membership->featured_listing != 0){
                       $user_featured_listing_enable = true;
                   }
                   if ($user_membership->listing_limit === 0){
                       $user_listing_limit_check = true;
                   }
               }

            }
        }

        $user = Auth::guard('web')->user();
        $videoQuota = $this->membershipService->getVideoQuota($user->id);
        $videoUsed  = \App\Models\Backend\Listing::where('user_id', $user->id)
                        ->whereNotNull('video_url')->where('video_url', '!=', '')
                        ->where('id', '!=', $id)->count();
        $canUploadVideo = $videoQuota !== 0 && ($videoQuota === -1 || $videoUsed < $videoQuota);

        return view('frontend.user.listings.edit-listing', compact(
            'membership_page_url',
            'user_featured_listing_enable',
            'user_listing_limit_check',
            'canUploadVideo',
            'videoQuota',
            'videoUsed',
            'listing',
            'brands',
            'categories',
            'sub_categories',
            'child_categories',
            'all_countries',
            'all_states',
            'all_cities',
            'tags'
        ));
    }

    public function deleteListing($id = null)
    {
        $listing = Listing::where('id', $id)
            ->where('user_id', Auth::guard('web')->id())
            ->first();

        if ($listing) {
            ListingTag::where('listing_id', $listing->id)->delete();
            ListingFavorite::where('listing_id', $listing->id)->delete();
            ListingReport::where('listing_id', $listing->id)->delete();

            // Delete the main Listing record
            $listing->delete();

            toastr_error(__('Listing Delete Success---'));
            return redirect()->back();
        } else {
            toastr_error(__('Listing not found or unauthorized'));
            return redirect()->back();
        }
    }

    public function listingPublishedStatus($id)
    {
        // First check if the listing exists
        $listing = Listing::where('id', $id)
            ->where('user_id', Auth::guard('web')->id())
            ->first();
        if (!$listing) {
            $message = __('Listing not found or unauthorized.');
            toastr()->error($message);
            return redirect()->back();
        }

        // Check listing approval status
        if ($listing->status === 0) {
            $message = __('This listing is not yet approved. It will be published after approval.');
            toastr()->warning($message);
            return redirect()->back();
        }

        // Toggle listing publication status
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

    public function boostListing(int $id)
    {
        try {
            $this->boostService->boost(Auth::guard('web')->id(), $id);
            toastr()->success(__('Your listing has been boosted! It will appear at the top of results for 48 hours.'));
        } catch (\RuntimeException $e) {
            toastr()->error($e->getMessage());
        }

        return redirect()->route('user.all.listing');
    }

}
