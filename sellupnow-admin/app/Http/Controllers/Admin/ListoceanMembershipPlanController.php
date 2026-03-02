<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ListoceanMembershipPlanController extends Controller
{
    private const CONNECTION = 'listocean';

    public function __construct()
    {
        $this->middleware('permission:admin.membershipPlan.index')->only(['index']);
        $this->middleware('permission:admin.membershipPlan.create')->only(['create']);
        $this->middleware('permission:admin.membershipPlan.store')->only(['store']);
        $this->middleware('permission:admin.membershipPlan.edit')->only(['edit']);
        $this->middleware('permission:admin.membershipPlan.update')->only(['update']);
        $this->middleware('permission:admin.membershipPlan.destroy')->only(['destroy']);
    }

    public function index(): View
    {
        $hasTable = Schema::connection(self::CONNECTION)->hasTable('membership_plans');

        $plans = collect();
        if ($hasTable) {
            $plans = DB::connection(self::CONNECTION)
                ->table('membership_plans')
                ->orderByDesc('id')
                ->get();
        }

        return view('admin.listocean-membership-plans.index', compact('hasTable', 'plans'));
    }

    public function create(): View
    {
        return view('admin.listocean-membership-plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'               => ['required', 'string', 'max:191'],
            'description'        => ['nullable', 'string'],
            'duration_days'      => ['required', 'integer', 'min:1', 'max:3650'],
            'price'              => ['required', 'numeric', 'min:0'],
            'currency'           => ['nullable', 'string', 'max:10'],
            'listing_quota'      => ['nullable', 'integer', 'min:0'],
            'auto_feature_count' => ['nullable', 'integer', 'min:0'],
            'video_quota'        => ['nullable', 'integer', 'min:-1'],
            'banner_ad_quota'    => ['nullable', 'integer', 'min:-1'],
            'badge_label'        => ['nullable', 'string', 'max:100'],
            'badge_color'        => ['nullable', 'string', 'max:20'],
            'sort_order'         => ['nullable', 'integer', 'min:0'],
            'is_active'          => ['nullable'],
            'features'           => ['nullable', 'array'],
            'features.*'         => ['string'],
        ]);

        $currency = trim((string) $request->input('currency', ''));
        $currency = $currency !== '' ? strtoupper(substr($currency, 0, 3)) : null;

        $dataToInsert = [
            'name'         => $request->input('name'),
            'description'  => $request->input('description'),
            'duration_days'=> (int) $request->input('duration_days'),
            'price'        => (float) $request->input('price'),
            'currency'     => $currency,
            'is_active'    => $request->boolean('is_active'),
            'created_at'   => now(),
            'updated_at'   => now(),
        ];

        // Save optional listing/badge columns if they exist in the remote DB.
        $optionalInsert = [
            'listing_quota'      => (int) $request->input('listing_quota', 0),
            'auto_feature_count' => (int) $request->input('auto_feature_count', 0),
            'video_quota'        => max(-1, (int) $request->input('video_quota', 0)),
            'banner_ad_quota'    => max(-1, (int) $request->input('banner_ad_quota', 0)),
            'badge_label'        => trim((string) $request->input('badge_label', '')),
            'badge_color'        => trim((string) $request->input('badge_color', '')),
            'sort_order'         => (int) $request->input('sort_order', 0),
        ];
        foreach ($optionalInsert as $col => $val) {
            try {
                if (Schema::connection(self::CONNECTION)->hasColumn('membership_plans', $col)) {
                    $dataToInsert[$col] = $val;
                }
            } catch (\Throwable $th) { /* ignore */ }
        }

        // Save features as JSON if column exists in the ListOcean DB table.
        try {
            if (Schema::connection(self::CONNECTION)->hasColumn('membership_plans', 'features')) {
                $dataToInsert['features'] = json_encode($request->input('features', []));
            }
        } catch (\Throwable $th) {
            // ignore schema check errors
        }

        DB::connection(self::CONNECTION)->table('membership_plans')->insert($dataToInsert);

        return redirect()->route('admin.membershipPlan.index')->with('success', __('Membership plan created'));
    }

    public function edit(int $id): View
    {
        $plan = DB::connection(self::CONNECTION)->table('membership_plans')->where('id', $id)->first();
        abort_if(!$plan, 404);
        return view('admin.listocean-membership-plans.edit', compact('plan'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name'               => ['required', 'string', 'max:191'],
            'description'        => ['nullable', 'string'],
            'duration_days'      => ['required', 'integer', 'min:1', 'max:3650'],
            'price'              => ['required', 'numeric', 'min:0'],
            'currency'           => ['nullable', 'string', 'max:10'],
            'listing_quota'      => ['nullable', 'integer', 'min:0'],
            'auto_feature_count' => ['nullable', 'integer', 'min:0'],
            'video_quota'        => ['nullable', 'integer', 'min:-1'],
            'banner_ad_quota'    => ['nullable', 'integer', 'min:-1'],
            'badge_label'        => ['nullable', 'string', 'max:100'],
            'badge_color'        => ['nullable', 'string', 'max:20'],
            'sort_order'         => ['nullable', 'integer', 'min:0'],
            'is_active'          => ['nullable'],
            'features'           => ['nullable', 'array'],
            'features.*'         => ['string'],
        ]);

        $currency = trim((string) $request->input('currency', ''));
        $currency = $currency !== '' ? strtoupper(substr($currency, 0, 3)) : null;

        $dataToUpdate = [
            'name'         => $request->input('name'),
            'description'  => $request->input('description'),
            'duration_days'=> (int) $request->input('duration_days'),
            'price'        => (float) $request->input('price'),
            'currency'     => $currency,
            'is_active'    => $request->boolean('is_active'),
            'updated_at'   => now(),
        ];

        // Save optional listing/badge columns if they exist in the remote DB.
        $optionalUpdate = [
            'listing_quota'      => (int) $request->input('listing_quota', 0),
            'auto_feature_count' => (int) $request->input('auto_feature_count', 0),
            'video_quota'        => max(-1, (int) $request->input('video_quota', 0)),
            'banner_ad_quota'    => max(-1, (int) $request->input('banner_ad_quota', 0)),
            'badge_label'        => trim((string) $request->input('badge_label', '')),
            'badge_color'        => trim((string) $request->input('badge_color', '')),
            'sort_order'         => (int) $request->input('sort_order', 0),
        ];
        foreach ($optionalUpdate as $col => $val) {
            try {
                if (Schema::connection(self::CONNECTION)->hasColumn('membership_plans', $col)) {
                    $dataToUpdate[$col] = $val;
                }
            } catch (\Throwable $th) { /* ignore */ }
        }

        try {
            if (Schema::connection(self::CONNECTION)->hasColumn('membership_plans', 'features')) {
                $dataToUpdate['features'] = json_encode($request->input('features', []));
            }
        } catch (\Throwable $th) {
            // ignore
        }

        DB::connection(self::CONNECTION)->table('membership_plans')->where('id', $id)->update($dataToUpdate);

        return redirect()->route('admin.membershipPlan.index')->with('success', __('Membership plan updated'));
    }

    public function destroy(int $id): RedirectResponse
    {
        DB::connection(self::CONNECTION)->table('membership_plans')->where('id', $id)->delete();
        return redirect()->route('admin.membershipPlan.index')->with('success', __('Membership plan deleted'));
    }
}
