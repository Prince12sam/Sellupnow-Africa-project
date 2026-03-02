<?php

namespace Modules\RolePermission\database\seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolePermissionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        // admin dashboard manage
        $admin_dashboard_permissions = [
            'admin-dashboard',
        ];
        foreach ($admin_dashboard_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Admin Dashboard',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // User listing manage
        $user_listing_permissions = [
            'user-listing-list',
            'user-listing-approved',
            'user-listing-published-status-change',
            'user-listing-status-change',
            'user-listing-delete',
            'user-listing-bulk-delete',
        ];
        foreach ($user_listing_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'User Listing Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Guest listing manage
        $guest_listing_permissions = [
            'guest-listing-list',
            'guest-listing-all-approved',
            'guest-listing-delete',
            'guest-listing-bulk-delete',
        ];
        foreach ($guest_listing_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Guest Listing Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Admin listing manage
        $admin_listing_permissions = [
            'admin-listing-list',
            'admin-listing-add',
            'admin-listing-edit',
            'admin-listing-delete',
            'admin-listing-bulk-delete',
            'admin-listing-published-status-change',
            'admin-listing-status-change',
        ];
        foreach ($admin_listing_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Admin Listing Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Report manage
        $report_reason_permissions = [
            'report-reason-list',
            'report-reason-edit',
            'report-reason-delete',
            'report-reason-bulk-delete',
        ];
        foreach ($report_reason_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Report Reason',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // listings Report manage
        $listing_report_permissions = [
            'listing-report-list',
            'listing-report-edit',
            'listing-report-delete',
            'listing-report-bulk-delete',
        ];
        foreach ($listing_report_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Listing Report',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // advertisement manage
        $advertisement_permissions = [
            'advertisement-list',
            'advertisement-add',
            'advertisement-edit',
            'advertisement-status-change',
            'advertisement-delete',
        ];

        foreach ($advertisement_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Advertisement',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // user manage
        $user_permissions = [
            'user-list',
            'user-add',
            'user-edit',
            'user-status-change',
            'user-verify-status',
            'user-verify-decline',
            'user-password',
            'user-delete',
            'user-permanent-delete',
            'user-deactivated-list',
            'user-deactivated-list',
        ];
        foreach ($user_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'User Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // category manage
        $category_permissions = [
            'category-list',
            'category-add',
            'category-edit',
            'category-status-change',
            'category-delete',
            'category-bulk-delete',
        ];
        foreach ($category_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Category',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // subcategory manage
        $subcategory_permissions = [
            'subcategory-list',
            'subcategory-add',
            'subcategory-edit',
            'subcategory-status-change',
            'subcategory-delete',
            'subcategory-bulk-delete',
        ];
        foreach ($subcategory_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Sub Category',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // child category manage
        $child_category_permissions = [
            'child-category-list',
            'child-category-add',
            'child-category-edit',
            'child-category-status-change',
            'child-category-delete',
            'child-category-bulk-delete',
        ];
        foreach ($child_category_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Child Category',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Dynamic Page manage
        $dynamic_page_permissions = [
            'dynamic-page-list',
            'dynamic-page-add',
            'dynamic-page-edit',
            'dynamic-page-delete',
            'dynamic-page-bulk-delete',
        ];
        foreach ($dynamic_page_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Dynamic Page',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Membership Type
        $membership_type_permissions = [
            'membership-type-list',
            'membership-type-edit',
            'membership-type-delete',
            'membership-type-bulk-delete',
        ];
        foreach ($membership_type_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Membership Type',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }



        // Membership Plan
        $membership_permissions = [
            'membership-list',
            'membership-add',
            'membership-edit',
            'membership-status-change',
            'membership-delete',
            'membership-bulk-delete'
        ];
        foreach ($membership_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Membership',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }



        // Membership Settings
        $membership_settings_permissions = [
            'membership-settings'
        ];
        foreach ($membership_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Membership Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }



     // User Membership manage
        $user_membership_permissions = [
            'user-membership-list',
            'user-membership-add',
            'user-membership-edit',
            'user-membership-status-change',
            'user-membership-active',
            'user-membership-inactive',
            'user-membership-manual',
            'user-membership-manual-payment-status-change'
        ];
        foreach ($user_membership_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'User Membership',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // User Membership Enquiry form manage
        $membership_enquiry_form_permissions = [
            'enquiry-form-list',
            'enquiry-form-delete',
            'enquiry-form-bulk-delete',
        ];
        foreach ($membership_enquiry_form_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Membership Enquiry Form',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // Country Manage
        $country_permissions = [
            'country-list',
            'country-edit',
            'country-status-change',
            'country-csv-file-import',
            'country-delete',
            'country-bulk-delete',
        ];
        foreach ($country_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Country',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        //state Manage
        $state_permissions = [
            'state-list',
            'state-edit',
            'state-status-change',
            'state-csv-file-import',
            'state-delete',
            'state-bulk-delete',
        ];
        foreach ($state_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'State',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // City Manage
        $city_permissions = [
            'city-list',
            'city-edit',
            'city-status-change',
            'city-csv-file-import',
            'city-delete',
            'city-bulk-delete',
        ];
        foreach ($city_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'City',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Brand Manage
        $brand_permissions = [
            'brand-list',
            'brand-edit',
            'brand-status-change',
            'brand-delete',
            'brand-bulk-delete',
        ];
        foreach ($brand_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Brand Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Brand Manage
        $brand_permissions = [
            'brand-list',
            'brand-edit',
            'brand-status-change',
            'brand-delete',
            'brand-bulk-delete',
        ];
        foreach ($brand_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Brand Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Newsletter Manage
        $newsletter_permissions = [
            'newsletter-list',
            'newsletter-add',
            'newsletter-single',
            'newsletter-delete',
            'newsletter-bulk-delete',
            'newsletter-newsletter-verify-mail-send',
        ];
        foreach ($newsletter_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Newsletter Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // Blog manage
        $blog_permissions = [
            'blog-list',
            'blog-add',
            'blog-edit',
            'blog-clone',
            'blog-delete',
            'blog-bulk-delete',
            'blog-settings',
        ];

        foreach ($blog_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Blog Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // Blog trashed manage
        $blog_trashed_permissions = [
            'blog-trashed-list',
            'blog-trashed-restore',
            'blog-trashed-delete',
            'blog-trashed-bulk-delete'
        ];

        foreach ($blog_trashed_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Blog Trashed Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Tags manage
        $tags_permissions = [
            'tag-list',
            'tag-add',
            'tag-edit',
            'tag-bulk-delete'
        ];

        foreach ($tags_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Tags Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // department manage
        $department_permissions = [
            'department-list',
            'department-add',
            'department-edit',
            'department-status-change',
            'department-bulk-delete'
        ];
        foreach ($department_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Department Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // Support ticket manage
        $support_ticket_permissions = [
            'support-ticket-list',
            'support-ticket-status-change',
            'support-ticket-details',
            'support-ticket-delete',
            'support-ticket-bulk-delete'
        ];
        foreach ($support_ticket_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Support Ticket',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // All Plugins manage
        $plugins_permissions = [
            'plugins-list',
            'plugins-add',
            'plugins-status-change',
            'plugins-delete',
        ];
        foreach ($plugins_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Plugins Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // All Plugins manage
        $payment_gateway_settings_permissions = [
            'payment-currency-settings'
        ];
        foreach ($payment_gateway_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Payment Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }



        // SMS Gateway Module
        $sms_gateway_settings_permissions = [
            'sms-gateway-settings',
            'sms-gateway-status-change',
            'sms-options-settings',
        ];
        foreach ($sms_gateway_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'SMS Gateway Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Integrations Module
        $integration_settings_permissions = [
            'integration-list',
        ];
        foreach ($integration_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Integrations Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Live Chat Module
        $live_chat_settings_permissions = [
            'live-chat-settings'
        ];
        foreach ($live_chat_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Chat Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Wallet Module
        $wallet_permissions = [
            'deposit-settings',
            'deposit-list',
            'complete-manual-deposit-status',
            'deposit-history-details',
        ];

        foreach ($wallet_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Chat Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Admin All Notifications
        $admin_notifications_permissions = [
            'notifications-list',
            'notifications-settings',
        ];

        foreach ($admin_notifications_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Admin Notifications',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Admin Notice
        $admin_notice_permissions = [
            'notice-list',
            'notice-add',
            'notice-edit',
            'notice-delete',
            'notice-status-change'
        ];

        foreach ($admin_notice_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Notice Manage',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Google Map Settings
        $google_map_settings_permissions = [
            'google-map-settings',
        ];

        foreach ($google_map_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Google Map Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // appearance Settings
        $appearance_settings_permissions = [
            'navbar-global-variant',
            'footer-global-variant',
            'color-settings',
            'typography-settings',
            'typography-single-settings',
            'font-add-settings',
            'custom-font-delete',
            'custom-font-status-change',
            'widgets-list',
            'widgets-add',
            'widgets-delete',
            'menu-list',
            'menu-add',
            'menu-edit',
            'menu-delete',
            'form-builder-list',
            'form-builder-edit',
            'form-builder-delete',
            'form-builder-bulk.delete',
            'media-upload',
            'media-upload-delete',
            '404-page-settings',
            'maintains-page-settings',
        ];

        foreach ($appearance_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Appearance Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // page settings
        $any_page_settings_permissions = [
            'login-register-page-settings',
            'listing-create-page-settings',
            'listing-details-page-settings',
            'listing-guest-page-settings',
            'user-public-profile-page-settings',
          ];

        foreach ($any_page_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Page Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // SMTP settings
        $smtp_settings_permissions = [
            'smtp-settings',
          ];

        foreach ($smtp_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'SMTP Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // General Settings
        $general_settings_permissions = [
            'reading-settings',
            'site-identity-settings',
            'basic-settings',
            'seo-settings',
            'scripts-settings',
            'custom-css-settings',
            'custom-js-settings',
            'sitemap-settings',
            'sitemap-delete',
            'gdpr-settings',
            'license-setting',
            'cache-setting',
            'database-upgrade-setting',
            'license-key-generate',
            'update-version-check',
            'software-update-settings',
          ];

        foreach ($general_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'General Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }


        // languages Settings
        $languages_settings_permissions = [
            'languages-list',
            'languages-words-edit',
            'languages-add',
            'languages-delete',
            'languages-clone',
          ];

        foreach ($languages_settings_permissions as $permission) {
            Permission::updateOrCreate([
                'menu_name' => 'Languages Settings',
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }



    }
}
