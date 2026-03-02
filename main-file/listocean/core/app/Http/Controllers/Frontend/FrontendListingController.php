<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Models\Backend\Advertisement;
use App\Models\Backend\Listing;
use App\Models\Backend\ReportReason;
use Illuminate\Http\Request;

class FrontendListingController extends Controller
{
    //Listing Details
    public function frontendListingDetails($slug)
    {
        $listing = Listing::with('user','brand','tags','metaData')->where('slug', $slug)->firstOrFail();

        if (empty($listing)) {
            return redirect_404_page();
        }
        if ($listing->is_published === 0){
            return redirect_404_page();
        }

        $related_listings = Listing::where(['user_id' => $listing->user_id, 'status' => 1])
            ->when(membershipModuleExistsAndEnable('Membership'),function($q){
                // Check if the membership module exists and is enabled
                $q->whereHas('user_membership');
            })
            ->inRandomOrder()
            ->where('id', '!=', $listing->id)
            ->take(4)
            ->get();

        if ($listing->user) {
            $user_total_listings = Listing::where('user_id', $listing->user->id)->count();
        } else {
            $user_total_listings = 0;
        }


        $viewToIncrement = 1;
        $listing->where('id', $listing->id)->increment('view', $viewToIncrement);

        $report_reasons = ReportReason::where('status', 1)
            ->latest()
            ->take(500)
            ->get();


        // google ads left start
        $add_query = Advertisement::query();
        if (!empty(get_static_option('left_listing_details_page_advertisement_type'))){
            $add_query = $add_query->where('type',get_static_option('left_listing_details_page_advertisement_type'));
        }
        if (!empty(get_static_option('left_listing_details_page_advertisement_size'))){
            $add_query = $add_query->where('size',get_static_option('left_listing_details_page_advertisement_size'));
        }
        $add = $add_query->where('status',1)->inRandomOrder()->first();
        $image_markup = '';
        $redirect_url = '';
        $slot = '';
        $embed_code = '';
        $add_markup = '';
        $add_id = $add->id ?? '';
        $custom_container = get_static_option('left_listing_details_page_advertisement_alignment');
        if (!empty($add)){
            $image_markup = render_image_markup_by_attachment_id($add->image,null,'full');
            $redirect_url = SanitizeInput::esc_url($add->redirect_url);
            $slot = $add->slot;
            $embed_code = $add->embed_code;
            if ($add->type === 'image'){
                $add_markup.= '<a href="'.$redirect_url.'">'.$image_markup.'</a>';
            }elseif($add->type === 'google_adsense'){
                $add_markup.= $this->script_add($slot);
            }else{
                $add_markup.= '<div>'.$embed_code.'</div>';
            }
        }



        // google ads right start
        $right_add_query = Advertisement::query();
        if (!empty(get_static_option('right_listing_details_page_advertisement_type'))){
            $right_add_query = $right_add_query->where('type',get_static_option('right_listing_details_page_advertisement_type'));
        }
        if (!empty(get_static_option('right_listing_details_page_advertisement_size'))){
            $right_add_query = $right_add_query->where('size',get_static_option('right_listing_details_page_advertisement_size'));
        }
        $add_right = $right_add_query->where('status',1)->inRandomOrder()->first();

        $right_image_markup = '';
        $right_redirect_url = '';
        $right_slot = '';
        $right_embed_code = '';
        $right_add_markup = '';
        $right_add_id = $add_right->id ?? '';
        $right_custom_container = get_static_option('left_listing_details_page_advertisement_alignment');

        if (!empty($add_right)){
            $right_image_markup = render_image_markup_by_attachment_id($add_right->image,null,'full');
            $right_redirect_url = SanitizeInput::esc_url($add_right->redirect_url);
            $right_slot = $add_right->slot;
            $right_embed_code = $add_right->embed_code;
            if ($add_right->type === 'image'){
                $right_add_markup.= '<a href="'.$right_redirect_url.'">'.$right_image_markup.'</a>';
            }elseif($add_right->type === 'google_adsense'){
                $right_add_markup.= $this->script_add($right_slot);
            }else{
                $right_add_markup.= '<div>'.$right_embed_code.'</div>';
            }
        }

        $user_business_hour = false;
        $user_enquiry_form = false;
        $user_membership_badge = false;
       // Check if the Membership module exists and is enabled
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
            $membershipUser = optional($listing->user)->membershipUser;
            // Check if the user has business hours, enquiry form, or membership badge
            if ($membershipUser) {
                $user_business_hour = $membershipUser->business_hour === 1;
                $user_enquiry_form = $membershipUser->enquiry_form === 1;
                $user_membership_badge = $membershipUser->membership_badge === 1;
            }
        }


        return view('frontend.pages.listings.listing-details', compact(
            'listing',
            'related_listings',
            'user_total_listings',
            'report_reasons',
            'user_business_hour',
            'user_enquiry_form',
            'user_membership_badge',
            'add_markup',
            'add_id',
            'custom_container',
            'right_add_markup',
            'right_custom_container',
            'right_add_id'
        ));
    }

    public function loadMoreListing(Request $request)
    {
        $page = $request->input('page', 1);
        $adsPerPage = 4;
        $offset = ($page - 1) * $adsPerPage;
        $listingId = $request->input('listing_id');

        // Check if listing_id is provided
        if (!$listingId) {
            return response()->json(['error' => 'Listing ID is required'], 400);
        }

        // Find the listing or return a 404 error
        $listing = Listing::find($listingId);
        if (!$listing) {
            return response()->json(['error' => 'Listing not found'], 404);
        }

        $related_listings = Listing::where(['user_id' => $listing->user_id, 'status' => 1])
            ->inRandomOrder()
            ->where('id', '!=', $listing->id)
            ->skip($offset)
            ->take($adsPerPage)
            ->get();

        $total_relevant_items = $related_listings->count();

        if ($total_relevant_items > 0) {
            return response()->json([
                'html' => view('frontend.pages.listings.relevant-markup', compact('related_listings'))->render(),
                'total_relevant_items' => $total_relevant_items
            ]);
        } else {
            return response()->json([
                'html' => '',
                'total_relevant_items' => $total_relevant_items
            ]);
        }



    }


    private function script_add($slot){
        $google_adsense_publisher_id = get_static_option('google_adsense_publisher_id');
        return <<<HTML
            <div>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="{$google_adsense_publisher_id}"
                 data-ad-slot="{$slot}"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            </div>
    HTML;
    }

}
