<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class GlobalSearchController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function routeOrUrl(?string $routeName, array $params = [], ?string $fallbackPath = null): string
    {
        try {
            if ($routeName && Route::has($routeName)) {
                return route($routeName, $params);
            }
        } catch (\Throwable $th) {
            // ignore
        }

        return $fallbackPath ? url($fallbackPath) : url('/admin');
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '' || mb_strlen($q) < 2) {
            return response()->json([
                'q' => $q,
                'items' => [],
            ]);
        }

        $qLike = '%'.$q.'%';

        $items = [];

        // Customer Web users
        try {
            $users = $this->listocean()->table('users')
                ->whereNull('deleted_at')
                ->where(function ($nested) use ($qLike) {
                    $nested->where('email', 'like', $qLike)
                        ->orWhere('username', 'like', $qLike)
                        ->orWhere('first_name', 'like', $qLike)
                        ->orWhere('last_name', 'like', $qLike);
                })
                ->orderByDesc('id')
                ->limit(8)
                ->get(['id', 'email', 'username', 'first_name', 'last_name']);

            foreach ($users as $u) {
                $name = trim(trim((string) ($u->first_name ?? '')).' '.trim((string) ($u->last_name ?? '')));
                $name = $name !== '' ? $name : ((string) ($u->username ?? 'User'));

                $items[] = [
                    'type' => 'Customer Web User',
                    'label' => sprintf('#%d — %s (%s)', (int) $u->id, $name, (string) ($u->email ?? '')), 
                    'url' => $this->routeOrUrl('admin.siteCustomer.show', ['id' => (int) $u->id], '/admin/customer-web/'.(int) $u->id),
                ];

                $items[] = [
                    'type' => 'Wallet',
                    'label' => sprintf('#%d — Wallet', (int) $u->id),
                    'url' => $this->routeOrUrl('admin.siteWallet.index', ['user_id' => (int) $u->id], '/admin/customer-web-wallet?user_id='.(int) $u->id),
                ];
            }
        } catch (\Throwable $th) {
            // ignore
        }

        // Customer Web listings (moderation)
        try {
            $listings = $this->listocean()->table('listings')
                ->where('title', 'like', $qLike)
                ->orderByDesc('id')
                ->limit(8)
                ->get(['id', 'title']);

            foreach ($listings as $l) {
                $items[] = [
                    'type' => 'Listing',
                    'label' => sprintf('#%d — %s', (int) $l->id, (string) ($l->title ?? 'Listing')),
                    'url' => $this->routeOrUrl('admin.listingModeration.show', ['id' => (int) $l->id], '/admin/listing-moderation/'.(int) $l->id),
                ];
            }
        } catch (\Throwable $th) {
            // ignore
        }

        // Banner ad requests (advertisements)
        try {
            $ads = $this->listocean()->table('advertisements')
                ->whereNotNull('user_id')
                ->where('title', 'like', $qLike)
                ->orderByDesc('id')
                ->limit(8)
                ->get(['id', 'title']);

            foreach ($ads as $a) {
                $items[] = [
                    'type' => 'Banner Ad Request',
                    'label' => sprintf('#%d — %s', (int) $a->id, (string) ($a->title ?? 'Banner Ad')),
                    'url' => $this->routeOrUrl('admin.bannerAdRequests.edit', ['id' => (int) $a->id], '/admin/banner-ad-requests/'.(int) $a->id.'/edit'),
                ];
            }
        } catch (\Throwable $th) {
            // ignore
        }

        // Promo videos
        try {
            $videos = $this->listocean()->table('ad_videos')
                ->where(function ($nested) use ($qLike) {
                    $nested->where('caption', 'like', $qLike)
                        ->orWhere('cta_url', 'like', $qLike);
                })
                ->orderByDesc('id')
                ->limit(8)
                ->get(['id', 'caption']);

            foreach ($videos as $v) {
                $cap = trim((string) ($v->caption ?? ''));
                $cap = $cap !== '' ? $cap : 'Promo video';
                $items[] = [
                    'type' => 'Promo Video',
                    'label' => sprintf('#%d — %s', (int) $v->id, $cap),
                    'url' => $this->routeOrUrl('admin.promoVideoAds.edit', ['id' => (int) $v->id], '/admin/promo-video-ads/'.(int) $v->id.'/edit'),
                ];
            }
        } catch (\Throwable $th) {
            // ignore
        }

        // De-dup by URL to avoid spam
        $seen = [];
        $items = array_values(array_filter($items, function ($row) use (&$seen) {
            $url = (string) ($row['url'] ?? '');
            if ($url === '' || isset($seen[$url])) {
                return false;
            }
            $seen[$url] = true;
            return true;
        }));

        return response()->json([
            'q' => $q,
            'items' => array_slice($items, 0, 15),
        ]);
    }
}
