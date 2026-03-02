<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Models\Backend\Advertisement;
use App\Models\Backend\Listing;
use App\Models\User;
use Illuminate\Http\Request;

class FrontendUserProfileController extends Controller
{
    public function frontendUserProfileView($slug=null)
    {
        $user = User::with('listings', 'reviews', 'user_country', 'user_state', 'identity_verify')->where('username', $slug)->first();

        if (empty($user)){
           return redirect_404_page();
        }

        $averageRating = $user->reviews?->avg('rating');
        $user_review_count = $user->reviews?->count();
        $userListings = Listing::where('user_id', $user->id)
            ->where('status', 1)
            ->where('is_published', 1)
            ->latest()
            ->paginate(10);

        $userVideos = Listing::where('user_id', $user->id)
            ->where('status', 1)
            ->where('is_published', 1)
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->latest()
            ->get();

        // google ads left start
        $add_query = Advertisement::query();
        if (!empty(get_static_option('user_public_profile_page_advertisement_type'))){
            $add_query = $add_query->where('type',get_static_option('user_public_profile_page_advertisement_type'));
        }
        if (!empty(get_static_option('user_public_profile_page_advertisement_size'))){
            $add_query = $add_query->where('size',get_static_option('user_public_profile_page_advertisement_size'));
        }
        $add = $add_query->where('status',1)->inRandomOrder()->first();
        $image_markup = '';
        $redirect_url = '';
        $slot = '';
        $embed_code = '';
        $add_markup = '';
        $add_id = $add->id ?? '';
        $custom_container = get_static_option('user_public_profile_page_advertisement_alignment');
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

        return view('frontend.pages.user.profile',compact(
            'user',
            'averageRating',
            'user_review_count',
            'userListings',
            'userVideos',
            'add_markup',
            'add_id',
            'custom_container',
        ));

    }
}
