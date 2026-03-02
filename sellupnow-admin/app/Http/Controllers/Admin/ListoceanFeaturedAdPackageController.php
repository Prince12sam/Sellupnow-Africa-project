<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ListoceanFeaturedAdPackageController extends Controller
{
    private const CONNECTION = 'listocean';

    public function index(): View
    {
        $hasTable = Schema::connection(self::CONNECTION)->hasTable('featured_ad_packages');

        $packages = collect();
        if ($hasTable) {
            $packages = DB::connection(self::CONNECTION)
                ->table('featured_ad_packages')
                ->orderByDesc('id')
                ->get();
        }

        return view('admin.listocean-featured-ad-packages.index', compact('hasTable', 'packages'));
    }

    public function create(): View
    {
        return view('admin.listocean-featured-ad-packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'advertisement_limit' => ['required', 'integer', 'min:1', 'max:100000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable'],
        ]);

        $currency = trim((string) $request->input('currency', ''));
        $currency = $currency !== '' ? strtoupper(substr($currency, 0, 3)) : null;

        DB::connection(self::CONNECTION)->table('featured_ad_packages')->insert([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'duration_days' => (int) $request->input('duration_days'),
            'advertisement_limit' => (int) $request->input('advertisement_limit'),
            'price' => (float) $request->input('price'),
            'currency' => $currency,
            'is_active' => $request->boolean('is_active'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.featuredAdPackage.index')->with('success', __('Featured ad package created'));
    }

    public function edit(int $id): View
    {
        $package = DB::connection(self::CONNECTION)->table('featured_ad_packages')->where('id', $id)->first();
        abort_if(!$package, 404);
        return view('admin.listocean-featured-ad-packages.edit', compact('package'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'advertisement_limit' => ['required', 'integer', 'min:1', 'max:100000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable'],
        ]);

        $currency = trim((string) $request->input('currency', ''));
        $currency = $currency !== '' ? strtoupper(substr($currency, 0, 3)) : null;

        DB::connection(self::CONNECTION)->table('featured_ad_packages')->where('id', $id)->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'duration_days' => (int) $request->input('duration_days'),
            'advertisement_limit' => (int) $request->input('advertisement_limit'),
            'price' => (float) $request->input('price'),
            'currency' => $currency,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.featuredAdPackage.index')->with('success', __('Featured ad package updated'));
    }

    public function destroy(int $id): RedirectResponse
    {
        // Guard: refuse deletion if there are still live activations for this package
        $activeCount = DB::connection(self::CONNECTION)
            ->table('featured_ad_activations')
            ->join('featured_ad_purchases', 'featured_ad_activations.purchase_id', '=', 'featured_ad_purchases.id')
            ->where('featured_ad_purchases.package_id', $id)
            ->where('featured_ad_activations.is_active', 1)
            ->where('featured_ad_activations.ends_at', '>=', now())
            ->count();

        if ($activeCount > 0) {
            return redirect()->route('admin.featuredAdPackage.index')
                ->with('error', __("Cannot delete: :count active featured listing(s) are still running under this package. Wait for them to expire first.", ['count' => $activeCount]));
        }

        DB::connection(self::CONNECTION)->table('featured_ad_packages')->where('id', $id)->delete();
        return redirect()->route('admin.featuredAdPackage.index')->with('success', __('Featured ad package deleted'));
    }
}
