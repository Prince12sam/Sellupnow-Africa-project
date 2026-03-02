<?php

namespace App\Http\Middleware;

use App\Models\UserNonPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if ($user->hasRole('root')) {
            return $next($request);
        }

        // Customer web membership plan management (paid membership catalog)
        // This admin feature is required for the customer web purchase flow.
        if ($request->is('admin/membership-plans*')) {
            return $next($request);
        }

        // Customer web wallet management (manual credit/debit + ledger)
        if ($request->is('admin/customer-web-wallet*')) {
            return $next($request);
        }

        if ($request->is('shop/*', 'shop') && $user->hasRole('shop')) {
            return $next($request);
        }
        if ($user->hasRole('shop') && $request->is('admin/laravel-filemanager*')) {
            return $next($request);
        }


        $roleNames = [];
        try {
            $roleNames = $user->getRoleNames()->toArray();
        } catch (\Throwable $throwable) {
            $roleNames = [];
        }

        $userRole = $roleNames[0] ?? null;
        if (! $userRole) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your account has no role assigned.'
                ], 403);
            }

            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Your account has no role assigned. Please contact admin.'
            ]);
        }

        $role = Cache::remember('role_'.$userRole, 60 * 24 * 60, function () use ($userRole) {
            return Role::where('name', $userRole)->first();
        });

        if (! $role) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role not found.'
                ], 403);
            }

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Role not found. Please contact admin.'
            ]);
        }

        $rolePermissions = Cache::remember('role_permissions_'.$role->id, 60 * 24 * 30, function () use ($role) {
            return $role->getPermissionNames()->toArray();
        });

        $userPermissions = Cache::remember('user_permissions_'.$user->id, 60 * 24 * 30, function () use ($user) {
            return $user->getPermissionNames()->toArray();
        });

        $userNonPermissions = Cache::remember('user_non_permissions_'.$user->id, 60 * 24 * 30, function () use ($user) {
            return UserNonPermission::where('user_id', $user->id)->pluck('name')->toArray();
        });

        $customPermissions = [
            'admin.dashboard.index',
            'shop.dashboard.index',
            'admin.new.notification',
            'admin.logout',
            'shop.logout',
            'admin.globalSearch',
            'shop.pos.invoice',
            'shop.pos.product',
            'shop.pos.addToCart',
            'shop.pos.getCart',
            'shop.pos.updateCart',
            'shop.pos.removeCart',
            'shop.pos.applyCoupon',
            'shop.pos.removeCoupon',
            'shop.pos.submitOrder',
            'shop.pos.customerStore',

            // Customer web membership plans (paid membership catalog)
            'admin.membershipPlan.index',
            'admin.membershipPlan.create',
            'admin.membershipPlan.store',
            'admin.membershipPlan.edit',
            'admin.membershipPlan.update',
            'admin.membershipPlan.destroy',

            // Customer web wallet management
            'admin.siteWallet.index',
            'admin.siteWallet.adjust',

            // Customer web featured ads packages + reports
            'admin.featuredAdPackage.index',
            'admin.featuredAdPackage.create',
            'admin.featuredAdPackage.store',
            'admin.featuredAdPackage.edit',
            'admin.featuredAdPackage.update',
            'admin.featuredAdPackage.destroy',
            'admin.featuredAdReports.purchases',
            'admin.featuredAdReports.activations',
        ];

        if (in_array('admin.business-setting.update', $rolePermissions)) {
            $customPermissions[] = 'admin.business-setting.shop';
            $customPermissions[] = 'admin.business-setting.withdraw';
        }

        if (in_array('admin.generale-setting.update', $rolePermissions) || in_array('admin.generale-setting.update', $userPermissions)) {
            $customPermissions[] = 'admin.generale-setting.listocean-general-settings';
        }

        if (
            in_array('admin.footer.update', $rolePermissions) ||
            in_array('admin.footer.index', $rolePermissions) ||
            in_array('admin.footer.update', $userPermissions) ||
            in_array('admin.footer.index', $userPermissions)
        ) {
            $customPermissions[] = 'admin.footer.listocean-logos';
            $customPermissions[] = 'admin.footer.listocean-content';
            $customPermissions[] = 'admin.footer.listocean-logos.get';
            $customPermissions[] = 'admin.footer.listocean-content.get';
        }

        if (in_array('admin.supportTicket.index', $rolePermissions)) {
            $customPermissions[] = 'admin.chatOversight.index';
            $customPermissions[] = 'admin.chatOversight.show';
            $customPermissions[] = 'admin.chatOversight.markSeen';
        }

        // Mail configuration is managed from the single SellUpNow mail config page.

        // Listocean: allow identity verification request moderation if user list is allowed.
        if (in_array('admin.customer.index', $rolePermissions) || in_array('admin.customer.index', $userPermissions)) {
            $customPermissions[] = 'admin.identityVerification.index';
            $customPermissions[] = 'admin.identityVerification.show';
            $customPermissions[] = 'admin.identityVerification.approve';
            $customPermissions[] = 'admin.identityVerification.decline';

            // Listocean: customer-web user management
            $customPermissions[] = 'admin.siteCustomer.create';
            $customPermissions[] = 'admin.siteCustomer.store';
            $customPermissions[] = 'admin.siteCustomer.show';
            $customPermissions[] = 'admin.siteCustomer.edit';
            $customPermissions[] = 'admin.siteCustomer.update';
            $customPermissions[] = 'admin.siteCustomer.reset-password';
            $customPermissions[] = 'admin.siteCustomer.subscription.update';

            // Listocean: membership plans
            $customPermissions[] = 'admin.membershipPlan.index';
            $customPermissions[] = 'admin.membershipPlan.create';
            $customPermissions[] = 'admin.membershipPlan.store';
            $customPermissions[] = 'admin.membershipPlan.edit';
            $customPermissions[] = 'admin.membershipPlan.update';
            $customPermissions[] = 'admin.membershipPlan.destroy';

            // Listocean: wallet management
            $customPermissions[] = 'admin.siteWallet.index';
            $customPermissions[] = 'admin.siteWallet.adjust';
        }

        // Customer web: allow membership plan management if payment gateways are manageable
        if (in_array('admin.paymentGateway.index', $rolePermissions) || in_array('admin.paymentGateway.index', $userPermissions)) {
            $customPermissions[] = 'admin.membershipPlan.index';
            $customPermissions[] = 'admin.membershipPlan.create';
            $customPermissions[] = 'admin.membershipPlan.store';
            $customPermissions[] = 'admin.membershipPlan.edit';
            $customPermissions[] = 'admin.membershipPlan.update';
            $customPermissions[] = 'admin.membershipPlan.destroy';
        }

        // Listocean homepage hero (PageBuilder HeaderStyleOne background image) can be granted explicitly,
        // but is also treated as part of Appearance/Home Screen.
        if (
            in_array('admin.homepageHero.edit', $rolePermissions) ||
            in_array('admin.homepageHero.edit', $userPermissions) ||
            in_array('admin.themeColor.index', $rolePermissions) ||
            in_array('admin.themeColor.index', $userPermissions)
        ) {
            $customPermissions[] = 'admin.homepageHero.edit';
            $customPermissions[] = 'admin.homepageHero.update';

            $customPermissions[] = 'admin.flashSaleWidget.edit';
            $customPermissions[] = 'admin.flashSaleWidget.update';
        }

        // Listocean: allow video moderation if listing moderation is allowed.
        if (in_array('admin.listingModeration.index', $rolePermissions) || in_array('admin.listingModeration.index', $userPermissions)) {
            $customPermissions[] = 'admin.videoModeration.index';
            $customPermissions[] = 'admin.videoModeration.create';
            $customPermissions[] = 'admin.videoModeration.store';
            $customPermissions[] = 'admin.videoModeration.show';
            $customPermissions[] = 'admin.videoModeration.edit';
            $customPermissions[] = 'admin.videoModeration.update';
            $customPermissions[] = 'admin.videoModeration.approve';
            $customPermissions[] = 'admin.videoModeration.removeVideo';

            $customPermissions[] = 'admin.promoVideoAds.index';
            $customPermissions[] = 'admin.promoVideoAds.edit';
            $customPermissions[] = 'admin.promoVideoAds.update';

            $customPermissions[] = 'admin.bannerAdRequests.index';
            $customPermissions[] = 'admin.bannerAdRequests.edit';
            $customPermissions[] = 'admin.bannerAdRequests.update';
            $customPermissions[] = 'admin.bannerAdRequests.approve';
            $customPermissions[] = 'admin.bannerAdRequests.deactivate';
        }

        if (in_array('admin.order.show', $rolePermissions)) {
            $customPermissions[] = 'admin.order.download-invoice';
            $customPermissions[] = 'admin.order.payment-slip';
        }

        // Listocean: allow configuring Paystack keys from SellUpNow payment gateway page
        if (
            in_array('admin.paymentGateway.index', $rolePermissions) ||
            in_array('admin.paymentGateway.index', $userPermissions)
        ) {
            $customPermissions[] = 'admin.paymentGateway.listocean.paystack.update';
        }

        $allPermissions = array_merge($userPermissions, $rolePermissions, $customPermissions);
        $allPermissions = array_unique($allPermissions);

        $allPermissions = array_diff($allPermissions, $userNonPermissions);

        $requestName = $request->route()->getName();

        if (! in_array($requestName, $allPermissions)) {
            if (str_ends_with($requestName, '.store')) {
                $requestName = str_replace('.store', '.create', $requestName);
            } elseif (str_ends_with($requestName, '.update')) {
                $requestName = str_replace('.update', '.edit', $requestName);
            }

            if (str_ends_with($requestName, '.gallery.create')) {
                $requestName = str_replace('.gallery.create', '.gallery.store', $requestName);
            }
        }

        if (! in_array($requestName, $allPermissions)) {
            if ($request->expectsJson() || $request->ajax() || str_starts_with($requestName, 'unisharp.lfm')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }
            return redirect()->back();
        }


        if (in_array($requestName, $allPermissions)) {
            return $next($request);
        }

        return abort(403);
    }
}
