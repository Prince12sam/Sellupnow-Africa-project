<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageSettingsController extends Controller
{
   public function loginRegisterPageSettings(Request $request)
    {
         if($request->isMethod('post')){

            $this->validate($request, [
                'login_form_title' => 'nullable|string',
                'register_page_title' => 'nullable|string',
                'select_terms_condition_page' => 'nullable|string',
                'register_page_description' => 'nullable|string',
                'register_page_image' => 'nullable|string',
                'recaptcha_2_site_key' => 'nullable',
            ]);

            $all_fields = [
                'login_form_title',
                'register_page_title',
                'register_page_description',
                'register_page_image',
                'select_terms_condition_page',
                'register_page_social_login_show_hide',
                'recaptcha_2_site_key',
            ];
            foreach ($all_fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::settings_update());
        }
        return view('backend.pages.page-settings.login-register-settings');
    }

    public function listingCreateSettings(Request $request)
    {
         if($request->isMethod('post')){

            $this->validate($request, [
                'listing_create_settings' => 'nullable|string',
                'listing_create_status_settings' => 'nullable|string'
            ]);

            $all_fields = [
                'listing_create_settings',
                'listing_create_status_settings'
            ];
            foreach ($all_fields as $field) {
                update_static_option($field, $request->$field);
            }
            return redirect()->back()->with(FlashMsg::settings_update());
        }

        return view('backend.pages.page-settings.listing-create-page-settings');
    }

    public function listingDetailsSettings(Request $request)
    {
         if($request->isMethod('post')){
                 $this->validate($request, [
                     'safety_tips_info' => 'nullable|string',
                     'listing_default_phone_number_title' => 'nullable|string',
                     'listing_phone_number_show_hide_button_title' => 'nullable|string',
                     'listing_report_button_title' => 'nullable|string',
                     'listing_share_button_title' => 'nullable|string',
                     'listing_show_phone_number_title' => 'nullable|string',
                     'listing_safety_tips_title' => 'nullable|string',
                     'listing_location_title' => 'nullable|string',
                     'listing_description_title' => 'nullable|string',
                     'listing_tag_title' => 'nullable|string',
                     'listing_relevant_title' => 'nullable|string',

                     'left_listing_details_page_advertisement_type' => 'nullable',
                     'left_listing_details_page_advertisement_size' => 'nullable',
                     'left_listing_details_page_advertisement_alignment' => 'nullable',

                     'right_listing_details_page_advertisement_type' => 'nullable',
                     'right_listing_details_page_advertisement_size' => 'nullable',
                     'right_listing_details_page_advertisement_alignment' => 'nullable',
                 ]);
                 $all_fields = [
                     'safety_tips_info',
                     'listing_default_phone_number_title',
                     'listing_phone_number_show_hide_button_title',
                     'listing_report_button_title',
                     'listing_share_button_title',
                     'listing_show_phone_number_title',
                     'listing_safety_tips_title',
                     'listing_location_title',
                     'listing_description_title',
                     'listing_tag_title',
                     'listing_relevant_title',

                     'left_listing_details_page_advertisement_type',
                     'left_listing_details_page_advertisement_size',
                     'left_listing_details_page_advertisement_alignment',

                     'right_listing_details_page_advertisement_type',
                     'right_listing_details_page_advertisement_size',
                     'right_listing_details_page_advertisement_alignment',
                 ];

                 foreach ($all_fields as $field) {
                     update_static_option($field, $request->$field);
                 }

            return redirect()->back()->with(FlashMsg::settings_update());
        }

        return view('backend.pages.page-settings.listing-details-page-settings');
    }

    public function listingGuestSettings(Request $request)
    {
         if($request->isMethod('post')){
             $this->validate($request, [
                 'guest_listing_gallery_image_upload_limit' => 'nullable',
                 'guest_add_listing_info_section_title' => 'nullable',
                 'guest_registration_agreement_title' => 'nullable',
                 'guest_listing_allowed_disallowed' => 'nullable',
                 'guest_listing_expire_limit' => 'nullable',
             ]);
             $all_fields = [
                 'guest_listing_gallery_image_upload_limit',
                 'guest_add_listing_info_section_title',
                 'guest_registration_agreement_title',
                 'guest_listing_allowed_disallowed',
                 'guest_listing_expire_limit',
             ];
             foreach ($all_fields as $field) {
                 update_static_option($field, $request->$field);
             }

            return redirect()->back()->with(FlashMsg::settings_update());
        }

        return view('backend.pages.page-settings.guest-listing-settings');
    }

    public function userPublicProfileSettings(Request $request)
    {
         if($request->isMethod('post')){
             $this->validate($request, [
                 'user_public_profile_page_advertisement_type' => 'nullable',
                 'user_public_profile_page_advertisement_size' => 'nullable',
                 'user_public_profile_page_advertisement_alignment' => 'nullable',
             ]);
             $all_fields = [
                 'user_public_profile_page_advertisement_type',
                 'user_public_profile_page_advertisement_size',
                 'user_public_profile_page_advertisement_alignment',
             ];
             foreach ($all_fields as $field) {
                 update_static_option($field, $request->$field);
             }

            return redirect()->back()->with(FlashMsg::settings_update());
        }

        return view('backend.pages.page-settings.user-public-profile-settings');
    }

}
