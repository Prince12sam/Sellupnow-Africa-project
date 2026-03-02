<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index()
    {
        // Listocean listing-creation uses Brands from the Listocean DB.
        // Keep Sellupnow as the single admin by managing Listocean brands here.
        $brands = DB::connection('listocean')
            ->table('brands')
            ->select(['id', 'title', 'status', 'created_at', 'updated_at'])
            ->orderByDesc('id')
            ->paginate(20)
            ->through(function ($row) {
                // Map Listocean schema (title/status) into the fields this view expects.
                $row->name = $row->title;
                $row->is_active = (int) ($row->status ?? 0) === 1;
                return $row;
            });

        return view('admin.brand.index', compact('brands'));
    }

    /**
     * store a new brand
     */
    public function store(BrandRequest $request)
    {
        $name = trim((string) $request->name);

        DB::connection('listocean')->table('brands')->insert([
            'title' => $name,
            'url' => Str::slug($name),
            'image' => '',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('admin.brand.index')->withSuccess(__('Brand created successfully'));
    }

    /**
     * update a brand
     */
    public function update(BrandRequest $request, int $brand)
    {
        $name = trim((string) $request->name);

        DB::connection('listocean')->table('brands')->where('id', $brand)->update([
            'title' => $name,
            'url' => Str::slug($name),
            'updated_at' => now(),
        ]);

        return to_route('admin.brand.index')->withSuccess(__('Brand updated successfully'));
    }

    /**
     * status toggle a brand
     */
    public function statusToggle(int $brand)
    {
        $row = DB::connection('listocean')->table('brands')->where('id', $brand)->first();
        if (! $row) {
            return to_route('admin.brand.index')->withError(__('Brand not found'));
        }

        $current = (int) ($row->status ?? 0) === 1;
        DB::connection('listocean')->table('brands')->where('id', $brand)->update([
            'status' => $current ? 0 : 1,
            'updated_at' => now(),
        ]);

        return to_route('admin.brand.index')->withSuccess(__('Brand status updated'));
    }
}
