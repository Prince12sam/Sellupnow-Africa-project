<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\Category;
use App\Models\Backend\IdentityVerification;
use App\Models\Backend\Listing;
use App\Models\Backend\ListingTag;
use App\Models\Backend\MediaUpload;
use App\Models\Backend\Page;
use App\Models\Backend\SubCategory;
use App\Models\Frontend\AccountDeactivate;
use App\Models\Frontend\GuestListing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Blog\app\Models\Tag;
use Modules\Brand\app\Models\Brand;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use Modules\Membership\app\Models\UserMembership;
use Modules\Wallet\app\Models\Wallet;
use Modules\Membership\app\Http\Services\MembershipService;

class GuestListingController extends Controller
{
    protected $membershipService;

    public function __construct()
    {
        if (moduleExists("Membership")) {
            if (membershipModuleExistsAndEnable('Membership')) {
                $this->membershipService = app()->make(MembershipService::class);
            }
        }
    }

    public function guestRequestCheck(Request $request){
        if (!empty($request->guest_register_request)) {
            // if name, phone or email already exits
            $user_check = User::where('email', $request->guest_email)
                ->orWhere('phone', $request->guest_phone)
                ->orWhere(function($query) use ($request) {
                    $query->where('first_name', $request->guest_first_name)
                        ->where('last_name', $request->guest_last_name);
                })
                ->first();

            if (!empty($user_check)) {
                $errors = [];
                if ($user_check->email === $request->guest_email) {
                    $errors[] = __('Your email already exists.');
                }
                if ($user_check->phone === $request->guest_phone) {
                    $errors[] = __('Your phone already exists.');
                }

                if ($user_check->first_name === $request->guest_first_name && $user_check->last_name === $request->guest_last_name) {
                    $errors[] = __('Your name already exists.');
                }
                return response()->json([
                    'status' => 'error',
                    'errors' => $errors
                ]);
            }
        }
    }

    // add listing page
    public function guestAddListing(Request $request)
    {
        // first check guest add listing enable or disable
        if(empty(get_static_option('guest_listing_allowed_disallowed'))){
            return redirect()->route('user.login');
        }

        if ($request->isMethod('post')) {

            $galleryImagesAllowed = get_static_option('guest_listing_gallery_image_upload_limit');
            $galleryImages = $request->gallery_images;
            $galleryImagesInput = explode('|', $galleryImages);
            $galleryImagesInputCount = count($galleryImagesInput);
            if ($galleryImagesInputCount > $galleryImagesAllowed) {
                toastr()->error(__('You have exceeded the maximum number of gallery images allowed') . ' ' . $galleryImagesAllowed);
                return redirect()->back();
            }

            // Validation start
            $request->validate([
                'category_id' => 'required',
                'terms_conditions' => 'required',
                'title' => 'required|max:191',
                'description' => 'required|min:150',
                'slug' => 'required',
                'price' => 'required|numeric',
                'guest_first_name'=>'required|max:191',
                'guest_last_name'=>'required|max:191',
                'guest_email'=> 'required|email|unique:users,email|max:191',
                'guest_phone'=>'required|unique:users,phone|max:191'
            ],
                [
                'title.required' => __('The title field is required.'),
                'title.max' => __('The title must not exceed 191 characters.'),
                'description.required' => __('The description field is required.'),
                'description.min' => __('The description must be at least 150 characters.'),
                'slug.required' => __('The slug field is required.'),
                'price.required' => __('The price field is required.'),
                'price.numeric' => __('The price must be a numeric value.'),
                'guest_first_name.required'=> __('First name is required'),
                'guest_last_name.required'=> __('Last name is required'),
                'guest_email.required'=> __('Email is required'),
                'guest_phone.required'=> __('Phone is required'),
         ]);

            $guest_phone_number = $request->guest_country_code ?? $request->guest_phone;

            if (!empty($request->guest_register_request)) {
                // Normalize and check phone number
                $phone_number = Str::replace(['-', '(', ')', ' '], '', $guest_phone_number);
                if (!empty($phone_number)) {
                    $existingUser = User::where('phone', $phone_number)->first();
                    if ($existingUser) {
                        return redirect()->back()->withErrors(['phone' => __('Phone number is already taken')])->withInput();
                    }
                }

                // Generate a unique username slug
                $guest_username = Str::slug($request->guest_first_name . ' ' . $request->guest_last_name);
                // Generate random 6-character password
                $random_password = Str::random(6);
                // Create user
                    $user = User::create([
                    'first_name' => $request->guest_first_name,
                    'last_name' => $request->guest_last_name,
                    'username' => $guest_username,
                    'email' => $request->guest_email,
                    'phone' => $guest_phone_number,
                    'password' => Hash::make($random_password),
                    'terms_condition' => 1,
                ]);

                // find user
                $user = User::find($user->id);

                // Log in the user
                Auth::login($user);

                // if exists wallet module
                if(moduleExists("Wallet")){
                    Wallet::create([
                        'user_id' => $user->id,
                        'balance' => 0,
                        'remaining_balance' => 0,
                        'withdraw_amount' => 0,
                        'status' => 1
                    ]);
                }

                // Create membership
                if ($user) {
                    if (moduleExists("Membership")) {
                        if (membershipModuleExistsAndEnable('Membership')) {
                            $this->membershipService->createFreeMembership($user);
                        }
                    }
                }

                if($user){
                    //send register mail to admin
                    try {
                        $message = get_static_option('user_register_message_for_admin') ?? __('Hello Admin a new user just have registered');
                        $message = str_replace(["@name","@email","@username"],[$user->first_name.' '.$user->last_name, $user->email, $user->username], $message);
                        Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                            'subject' => get_static_option('user_register_subject') ?? __('New User Register Email'),
                            'message' => $message
                        ]));
                    }
                    catch (\Exception $e) {}

                    //send register mail to user
                    try {
                        $message = get_static_option('user_register_message') ?? __('Your registration successfully completed.');
                        $message = str_replace(["@name","@email","@username","@password"],[$user->first_name.' '.$user->last_name, $user->email, $user->username, $random_password], $message);
                        Mail::to($user->email)->send(new BasicMail([
                            'subject' => get_static_option('user_register_subject') ?? __('User Register Welcome Email'),
                            'message' => $message
                        ]));
                    }
                    catch (\Exception $e) {}

                }
            }

            if (!empty($request->guest_register_request) && !empty($user)) {
                    //check membership
                    if (moduleExists('Membership')) {
                        if (membershipModuleExistsAndEnable('Membership')) {
                            $user_membership = UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();

                            // if user membership is null
                            if (is_null($user_membership)) {
                                toastr_error(__('you have to membership a package to create listings'));
                                return redirect()->back();
                            }

                            $user_total_listing_count = Listing::where('user_id', Auth::guard('web')->user()->id)->count();

                            // check user membership all listing limit
                            if ($user_membership->listing_limit == 0 && $user_membership->expire_date <= Carbon::now()) {
                                session()->flash('message', __('Your Membership is expired'));
                                return redirect()->back();
                            } elseif ($user_membership->listing_limit === 0) {
                                toastr_error(__('Your membership listing limit is over!. please renew it'));
                                return redirect()->back();
                            } elseif ($user_membership->expire_date <= Carbon::now()) {
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
                            if (!empty($request->is_featured)) {
                                if ($user_membership->initial_featured_listing != 0) {
                                    if ($user_membership->featured_listing === 0) {
                                        toastr_error(__('You have exceeded the maximum number of featured listings allowed by your membership package.'));
                                        return redirect()->back();
                                    }
                                }
                            }

                        }
                    }
                }

            if (!empty($request->guest_register_request)) {
                $user = User::where('id', Auth::guard('web')->user()->id)->first();
            }
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

            // video url
            $video_url = null;
            if(!empty($request->video_url)){
                $video_url = getYoutubeEmbedUrl($request->video_url);
            }


            // listing phone number
            $listing_phone = $request->country_code ?? $request->phone;


            // Create a new listing
            $listing = new Listing();
            $listing->user_id = $user->id ?? 0;
            $listing->category_id = $request->category_id;
            $listing->sub_category_id = $request->sub_category_id;
            $listing->child_category_id = $request->child_category_id;
            $listing->country_id = $request->country_id;
            $listing->state_id = $request->state_id;
            $listing->city_id = $request->city_id;
            $listing->brand_id = $request->brand_id ?? 0;
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
            $listing->lat = $request->latitude;
            $listing->lon = $request->longitude;
            $listing->is_featured =  $request->is_featured ?? 0;
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
            $listing->metaData()->create($Metas);

            // if user exits update image user_id (Image and Gallery images)
            if (!empty($listing->user_id)) {
                // Combine the main image ID and gallery image IDs into a single array
                $imageIds = array_merge([$listing->image], explode('|', $listing->gallery_images));
                // Update the user_id for the uploaded images
                MediaUpload::whereIn('id', $imageIds)->update([
                    'user_id' => $listing->user_id
                ]);
            }

            // for Guest Reset image uploaded IDs
            Session::forget('uploaded_image_ids');
            Session::flash('uploaded_image_ids', []);

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


            // if guest listing create request
            if (!Auth::check() && empty($user)) {
                GuestListing::create([
                    'listing_id' => $last_listing_id,
                    'first_name' => $request->guest_first_name,
                    'last_name' => $request->guest_last_name,
                    'email' => $request->guest_email,
                    'phone' => $guest_phone_number,
                    'status' => 0,
                    'terms_condition' => 1,
                ]);
            }

            // if membership system decrement listing limit
            if (!empty($request->guest_register_request) && !empty($user)) {
                    if(moduleExists('Membership')){
                        if (membershipModuleExistsAndEnable('Membership')) {
                            $user_id = Auth::guard('web')->user()->id;
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
            }else{
                if (get_static_option('listing_create_status_settings') == 'pending'){
                    // sent email to admin
                    try {
                        $subject = get_static_option('guest_add_new_listing_subject') ?? __('A new listing has just been created and is awaiting for approval');
                        $message = get_static_option('guest_add_new_listing_message_for_admin');
                        $message = str_replace(["@listing_id"], [$last_listing_id], $message);
                        Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                            'subject' => $subject,
                            'message' => $message
                        ]));
                    } catch (\Exception $e) {
                        //
                    }

                    // sent email to Guest
                    try {
                        $subject = get_static_option('guest_add_new_listing_subject') ?? __('A new listing has been created by a guest and is awaiting your approval');
                        $message = get_static_option('guest_add_new_listing_message');
                        $message = str_replace(["@listing_id"], [$last_listing_id], $message);
                        Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                            'subject' => $subject,
                            'message' => $message
                        ]));
                    } catch (\Exception $e) {
                        //
                    }

                }
            }

            if (!empty($request->guest_register_request) && !empty($user)) {
                return redirect()->route('user.all.listing')->with(toastr_success(__('Listing Added Success')));
            }else{
                return redirect()->route('homepage')->with(toastr_success(__('A new listing has just been created and is awaiting for approval')));
            }


        }  // end


        $categories = Category::where('status', 1)->get();
        $sub_categories = SubCategory::where('status', 1);
        $all_countries = Country::all_countries();
        $all_states = State::all_states();
        $all_cities = City::all_cities();
        $tags = Tag::where('status', 'publish')->get();
        $user = Auth::guard('web')->user();
        $brands = Brand::where('status', 1)->get();

        // if membership module exits
        $membership_page_url = get_static_option('membership_plan_page') ? Page::select('slug')->find(get_static_option('membership_plan_page'))->slug : '';
        $user_featured_listing_enable = false;
        $user_listing_limit_check = false;

        return view('frontend.user.listings.guest-add-listing', compact(
            'membership_page_url',
            'user_featured_listing_enable',
            'user_listing_limit_check',
            'user',
            'brands',
            'categories',
            'sub_categories',
            'all_countries',
            'all_states',
            'all_cities',
            'tags',
        ));

    }

}
