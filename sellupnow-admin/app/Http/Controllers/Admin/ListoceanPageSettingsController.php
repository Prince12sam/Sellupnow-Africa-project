<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanPageSettingsController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function getOptions(array $keys): array
    {
        $rows = $this->listocean()->table('static_options')
            ->whereIn('option_name', $keys)
            ->get(['option_name', 'option_value']);

        $map = array_fill_keys($keys, '');
        foreach ($rows as $row) {
            $map[$row->option_name] = (string) ($row->option_value ?? '');
        }
        return $map;
    }

    private function setOptions(Request $request, array $fields): void
    {
        foreach ($fields as $field) {
            $this->listocean()->table('static_options')->updateOrInsert(
                ['option_name' => $field],
                ['option_value' => (string) ($request->input($field) ?? ''), 'updated_at' => now()]
            );
        }
    }

    // ── Login / Register page ─────────────────────────────────────────────
    public function loginRegister(Request $request)
    {
        $fields = [
            'login_form_title',
            'register_page_title',
            'register_page_description',
            'register_page_image',
            'select_terms_condition_page',
            'register_page_social_login_show_hide',
            'recaptcha_2_site_key',
        ];

        if ($request->isMethod('post')) {
            $request->validate([
                'login_form_title'        => 'nullable|string|max:255',
                'register_page_title'     => 'nullable|string|max:255',
                'register_page_description' => 'nullable|string',
                'recaptcha_2_site_key'    => 'nullable|string|max:512',
            ]);
            $this->setOptions($request, $fields);
            return back()->withSuccess(__('Login/Register page settings updated'));
        }

        return view('admin.page-settings.login-register', [
            'settings' => $this->getOptions($fields),
        ]);
    }

    // ── Listing create page ────────────────────────────────────────────────
    public function listingCreate(Request $request)
    {
        $fields = [
            'listing_create_settings',
            'listing_create_status_settings',
        ];

        if ($request->isMethod('post')) {
            $this->setOptions($request, $fields);
            return back()->withSuccess(__('Listing create page settings updated'));
        }

        return view('admin.page-settings.listing-create', [
            'settings' => $this->getOptions($fields),
        ]);
    }

    // ── Listing details page ───────────────────────────────────────────────
    public function listingDetails(Request $request)
    {
        $fields = [
            'safety_tips_info',
            'safety_tips_color',
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

        if ($request->isMethod('post')) {
            $this->setOptions($request, $fields);
            return back()->withSuccess(__('Listing details page settings updated'));
        }

        return view('admin.page-settings.listing-details', [
            'settings' => $this->getOptions($fields),
        ]);
    }

    // ── Guest listing page ─────────────────────────────────────────────────
    public function guestListing(Request $request)
    {
        $fields = [
            'guest_listing_gallery_image_upload_limit',
            'guest_add_listing_info_section_title',
            'guest_registration_agreement_title',
            'guest_listing_allowed_disallowed',
            'guest_listing_expire_limit',
        ];

        if ($request->isMethod('post')) {
            $this->setOptions($request, $fields);
            return back()->withSuccess(__('Guest listing page settings updated'));
        }

        return view('admin.page-settings.guest-listing', [
            'settings' => $this->getOptions($fields),
        ]);
    }

    // ── User public profile page ───────────────────────────────────────────
    public function userPublicProfile(Request $request)
    {
        $fields = [
            'user_public_profile_page_advertisement_type',
            'user_public_profile_page_advertisement_size',
            'user_public_profile_page_advertisement_alignment',
        ];

        if ($request->isMethod('post')) {
            $this->setOptions($request, $fields);
            return back()->withSuccess(__('User public profile page settings updated'));
        }

        return view('admin.page-settings.user-public-profile', [
            'settings' => $this->getOptions($fields),
        ]);
    }
}
