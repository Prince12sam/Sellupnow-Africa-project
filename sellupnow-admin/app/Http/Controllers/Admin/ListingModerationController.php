<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ListingModerationController extends Controller
{
    public function index(Request $request)
    {
        $customerWebUrl = rtrim((string) config('app.customer_web_url', ''), '/');

        $queue = (string) $request->get('queue', 'all');

        $query = $this->listocean()->table('listings as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->leftJoin('categories as c', 'c.id', '=', 'l.category_id')
            ->select([
                'l.*',
                'c.name as category_name',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->selectRaw('(SELECT COUNT(*) FROM listing_reports lr WHERE lr.listing_id = l.id) as reports_count')
            ->when($queue === 'removed', fn ($builder) => $builder->whereNotNull('l.deleted_at'))
            ->when($queue !== 'removed', fn ($builder) => $builder->whereNull('l.deleted_at'))
            // New listings waiting approval: never published before.
            ->when($queue === 'new', fn ($builder) => $builder->where('l.status', 0)->whereNull('l.published_at'))
            // Update listings waiting approval: previously published, now back to pending.
            ->when($queue === 'update', fn ($builder) => $builder->where('l.status', 0)->whereNotNull('l.published_at'))
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = (string) $request->search;
                $builder->where(function ($nested) use ($search) {
                    $nested->where('l.title', 'like', "%{$search}%")
                        ->orWhere('l.description', 'like', "%{$search}%")
                        ->orWhere('l.slug', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($builder) => $builder->where('l.status', (int) $request->status))
            ->when($request->filled('is_published'), fn ($builder) => $builder->where('l.is_published', (int) $request->is_published))
            ->orderByDesc('l.id');

        /** @var LengthAwarePaginator $listings */
        $listings = $query->paginate(15);

        $listings->setCollection(
            $listings->getCollection()->map(fn ($row) => $this->presentListoceanListingRow($row, $customerWebUrl))
        );

        return view('admin.listing-moderation.index', compact('listings', 'queue'));
    }

    public function show(int $id)
    {
        $customerWebUrl = rtrim((string) config('app.customer_web_url', ''), '/');

        $listing = $this->listocean()->table('listings as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->leftJoin('categories as c', 'c.id', '=', 'l.category_id')
            ->leftJoin('sub_categories as sc', 'sc.id', '=', 'l.sub_category_id')
            ->leftJoin('child_categories as cc', 'cc.id', '=', 'l.child_category_id')
            ->leftJoin('countries as co', 'co.id', '=', 'l.country_id')
            ->select([
                'l.*',
                'c.name as category_name',
                'sc.name as sub_category_name',
                'cc.name as child_category_name',
                'co.country as country_name',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->selectRaw('(SELECT COUNT(*) FROM listing_favorites lf WHERE lf.listing_id = l.id) as favorites_count')
            ->selectRaw('(SELECT COUNT(*) FROM listing_reports lr WHERE lr.listing_id = l.id) as reports_count')
            ->where('l.id', $id)
            ->first();

        if (! $listing) {
            abort(404);
        }

        $listing = $this->presentListoceanListingRow($listing, $customerWebUrl, true);

        return view('admin.listing-moderation.show', compact('listing'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|boolean',
        ]);

        $listing = $this->listocean()->table('listings')->where('id', $id)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        $newStatus = (bool) $data['status'];

        // Copy Listocean admin behavior: approving also publishes and sets published_at.
        if ($newStatus) {
            $this->listocean()->table('listings')->where('id', $id)->update([
                'status' => 1,
                'is_published' => 1,
                'published_at' => $listing->published_at ?: now(),
                'updated_at' => now(),
            ]);

            return back()->withSuccess('Listing approved and published successfully');
        }

        // If set inactive, also unpublish so it disappears from the customer web.
        $this->listocean()->table('listings')->where('id', $id)->update([
            'status' => 0,
            'is_published' => 0,
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Listing set to inactive successfully');
    }

    public function updatePublishStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'is_published' => 'required|boolean',
        ]);

        $listing = $this->listocean()->table('listings')->where('id', $id)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        $newPublish = (bool) $data['is_published'];

        // Do not allow publish when not approved.
        if ($newPublish && (int) $listing->status !== 1) {
            return back()->withError('Listing is not approved yet. Approve it before publishing.');
        }

        $this->listocean()->table('listings')->where('id', $id)->update([
            'is_published' => $newPublish ? 1 : 0,
            'published_at' => $newPublish ? ($listing->published_at ?: now()) : $listing->published_at,
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Listing publish status updated successfully');
    }

    public function updateFeaturedStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'is_featured' => 'required|boolean',
        ]);

        $listing = $this->listocean()->table('listings')->where('id', $id)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        if (! empty($listing->deleted_at)) {
            return back()->withError('Removed listings cannot be featured.');
        }

        $this->listocean()->table('listings')->where('id', $id)->update([
            'is_featured' => ((bool) $data['is_featured']) ? 1 : 0,
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Listing featured status updated successfully');
    }

    public function destroy(int $id)
    {
        $listing = $this->listocean()->table('listings')->where('id', $id)->first();
        if (! $listing) {
            return back()->withError('Listing not found');
        }

        $now = now();

        // Match Listocean deletion behavior but keep soft-delete semantics.
        $this->listocean()->transaction(function () use ($id, $now) {
            $this->listocean()->table('listing_tags')->where('listing_id', $id)->delete();
            $this->listocean()->table('listing_favorites')->where('listing_id', $id)->delete();
            $this->listocean()->table('listing_reports')->where('listing_id', $id)->delete();
            $this->listocean()->table('guest_listings')->where('listing_id', $id)->delete();

            $this->listocean()->table('listings')->where('id', $id)->update([
                'deleted_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return back()->withSuccess('Listing deleted successfully');
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function presentListoceanListingRow(object $row, string $customerWebUrl, bool $withDetails = false): object
    {
        // Provide relationship-like objects so existing Blade views keep working with minimal changes.
        $row->user = (object) ['name' => $row->user_name ?? null];
        $row->category = (object) ['name' => $row->category_name ?? null];

        if (property_exists($row, 'sub_category_name')) {
            $row->subCategory = (object) ['name' => $row->sub_category_name ?? null];
        }
        if (property_exists($row, 'child_category_name')) {
            $row->childCategory = (object) ['name' => $row->child_category_name ?? null];
        }
        if (property_exists($row, 'country_name')) {
            $row->country = (object) ['name' => $row->country_name ?? null];
        }

        if (! property_exists($row, 'favorites_count')) {
            $row->favorites_count = null;
        }

        $row->thumbnail = $this->listoceanListingThumbnailUrl((string) ($row->image ?? ''), $customerWebUrl);

        if ($withDetails) {
            $row->created_at = $this->maybeCarbon($row->created_at ?? null);
            $row->published_at = $this->maybeCarbon($row->published_at ?? null);
        }

        return $row;
    }

    private function maybeCarbon($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function listoceanListingThumbnailUrl(string $imageValue, string $customerWebUrl): string
    {
        $fallback = asset('default/default.jpg');
        $imageValue = trim($imageValue);
        if ($imageValue === '') {
            return $fallback;
        }

        if (preg_match('~^https?://~i', $imageValue)) {
            return $imageValue;
        }

        if (str_starts_with($imageValue, 'storage/')) {
            return $customerWebUrl . '/' . ltrim($imageValue, '/');
        }

        if (str_starts_with($imageValue, 'listings/')) {
            return $customerWebUrl . '/storage/' . ltrim($imageValue, '/');
        }

        if (str_starts_with($imageValue, 'assets/uploads/')) {
            return $customerWebUrl . '/' . ltrim($imageValue, '/');
        }

        // Listocean stores listing image as a media_uploads id (string) in many installs.
        if (ctype_digit($imageValue)) {
            try {
                $path = (string) ($this->listocean()->table('media_uploads')->where('id', (int) $imageValue)->value('path') ?? '');
                if ($path !== '') {
                    return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($path, '/');
                }
            } catch (\Throwable $e) {
                return $fallback;
            }
        }

        // Otherwise treat as a stored filename/path.
        return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($imageValue, '/');
    }
}
