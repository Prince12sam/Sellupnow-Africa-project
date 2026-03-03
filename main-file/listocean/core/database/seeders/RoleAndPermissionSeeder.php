<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'admin';

        // ── Permissions ──────────────────────────────────────────────────────
        $permissions = [
            'admin-dashboard',
            'user-listing-list', 'user-listing-approved', 'user-listing-published-status-change',
            'user-listing-status-change', 'user-listing-delete', 'user-listing-bulk-delete',
            'guest-listing-list', 'guest-listing-all-approved', 'guest-listing-delete', 'guest-listing-bulk-delete',
            'admin-listing-list', 'admin-listing-add', 'admin-listing-edit', 'admin-listing-delete',
            'admin-listing-bulk-delete', 'admin-listing-published-status-change', 'admin-listing-status-change',
            'report-reason-list', 'report-reason-edit', 'report-reason-delete', 'report-reason-bulk-delete',
            'listing-report-list', 'listing-report-edit', 'listing-report-delete', 'listing-report-bulk-delete',
            'advertisement-list', 'advertisement-add', 'advertisement-edit', 'advertisement-status-change', 'advertisement-delete',
            'user-list', 'user-add', 'user-edit', 'user-status-change', 'user-verify-status', 'user-verify-decline',
            'user-password', 'user-delete', 'user-permanent-delete', 'user-deactivated-list',
            'category-list', 'category-add', 'category-edit', 'category-status-change', 'category-delete', 'category-bulk-delete',
            'subcategory-list', 'subcategory-add', 'subcategory-edit', 'subcategory-status-change', 'subcategory-delete', 'subcategory-bulk-delete',
            'child-category-list', 'child-category-add', 'child-category-edit', 'child-category-status-change', 'child-category-delete', 'child-category-bulk-delete',
            'dynamic-page-list', 'dynamic-page-add', 'dynamic-page-edit', 'dynamic-page-delete', 'dynamic-page-bulk-delete',
            'membership-type-list', 'membership-type-edit', 'membership-type-delete', 'membership-type-bulk-delete',
            'membership-list', 'membership-add', 'membership-edit', 'membership-status-change', 'membership-delete', 'membership-bulk-delete', 'membership-settings',
            'user-membership-list', 'user-membership-add', 'user-membership-edit', 'user-membership-status-change',
            'user-membership-active', 'user-membership-inactive', 'user-membership-manual', 'user-membership-manual-payment-status-change',
            'enquiry-form-list', 'enquiry-form-delete', 'enquiry-form-bulk-delete',
            'country-list', 'country-edit', 'country-status-change', 'country-csv-file-import', 'country-delete', 'country-bulk-delete',
            'state-list', 'state-edit', 'state-status-change', 'state-csv-file-import', 'state-delete', 'state-bulk-delete',
            'city-list', 'city-edit', 'city-status-change', 'city-csv-file-import', 'city-delete', 'city-bulk-delete',
            'brand-list', 'brand-edit', 'brand-status-change', 'brand-delete', 'brand-bulk-delete',
            'newsletter-list', 'newsletter-add', 'newsletter-single', 'newsletter-delete', 'newsletter-bulk-delete', 'newsletter-newsletter-verify-mail-send',
            'blog-list', 'blog-add', 'blog-edit', 'blog-clone', 'blog-delete', 'blog-bulk-delete', 'blog-settings',
            'blog-trashed-list', 'blog-trashed-restore', 'blog-trashed-delete', 'blog-trashed-bulk-delete',
            'tag-list', 'tag-add', 'tag-edit', 'tag-bulk-delete',
            'department-list', 'department-add', 'department-edit', 'department-status-change', 'department-bulk-delete',
            'support-ticket-list', 'support-ticket-status-change', 'support-ticket-details', 'support-ticket-delete', 'support-ticket-bulk-delete',
            'plugins-list', 'plugins-add', 'plugins-status-change', 'plugins-delete',
            'payment-currency-settings',
            'sms-gateway-settings', 'sms-gateway-status-change', 'sms-options-settings',
            'integration-list', 'live-chat-settings',
            'deposit-settings', 'deposit-list', 'complete-manual-deposit-status', 'deposit-history-details',
            'notifications-list', 'notifications-settings',
            'notice-list', 'notice-add', 'notice-edit', 'notice-delete', 'notice-status-change',
            'google-map-settings',
            'navbar-global-variant', 'footer-global-variant',
            'color-settings', 'typography-settings', 'typography-single-settings', 'font-add-settings', 'custom-font-delete', 'custom-font-status-change',
            'widgets-list', 'widgets-add', 'widgets-delete',
            'menu-list', 'menu-add', 'menu-edit', 'menu-delete',
            'form-builder-list', 'form-builder-edit', 'form-builder-delete', 'form-builder-bulk.delete',
            'media-upload', 'media-upload-delete',
            '404-page-settings', 'maintains-page-settings', 'login-register-page-settings',
            'listing-create-page-settings', 'listing-details-page-settings', 'listing-guest-page-settings',
            'user-public-profile-page-settings',
            'smtp-settings', 'reading-settings', 'site-identity-settings', 'basic-settings',
            'seo-settings', 'scripts-settings', 'custom-css-settings', 'custom-js-settings',
            'sitemap-settings', 'sitemap-delete',
            'gdpr-settings', 'license-setting', 'cache-setting', 'database-upgrade-setting',
            'license-key-generate', 'update-version-check', 'software-update-settings',
            'languages-list', 'languages-words-edit', 'languages-add', 'languages-delete', 'languages-clone',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
        }

        // ── Roles ─────────────────────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['id' => 1, 'name' => 'Super Admin', 'guard_name' => $guard]);
        $admin      = Role::firstOrCreate(['id' => 2, 'name' => 'Admin',       'guard_name' => $guard]);
        $editor     = Role::firstOrCreate(['id' => 6, 'name' => 'Editor',      'guard_name' => $guard]);
        Role::firstOrCreate(['id' => 9, 'name' => 'Manager', 'guard_name' => $guard]);

        // ── Role → Permission assignments ──────────────────────────────────────
        // Super Admin and Admin get all permissions
        $allPermissions = Permission::where('guard_name', $guard)->pluck('name');
        $superAdmin->syncPermissions($allPermissions);
        $admin->syncPermissions($allPermissions);

        // Editor gets a limited set
        $editorPermissions = [
            'admin-dashboard',
            'admin-listing-list', 'admin-listing-add', 'admin-listing-edit', 'admin-listing-delete',
            'admin-listing-bulk-delete', 'admin-listing-published-status-change', 'admin-listing-status-change',
            'advertisement-list', 'advertisement-add', 'advertisement-edit', 'advertisement-status-change', 'advertisement-delete',
            'blog-list', 'blog-add', 'blog-edit', 'blog-clone', 'blog-delete', 'blog-bulk-delete', 'blog-settings',
            'navbar-global-variant', 'footer-global-variant',
            'color-settings', 'typography-settings', 'typography-single-settings', 'font-add-settings', 'custom-font-delete', 'custom-font-status-change',
            'widgets-list', 'widgets-add', 'widgets-delete',
            'menu-list', 'menu-add', 'menu-edit', 'menu-delete',
            'form-builder-list', 'form-builder-edit', 'form-builder-delete', 'form-builder-bulk.delete',
            'media-upload', 'media-upload-delete',
            '404-page-settings', 'maintains-page-settings',
        ];
        $editor->syncPermissions($editorPermissions);
    }
}
