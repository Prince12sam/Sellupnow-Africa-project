<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MarketplaceBannerController extends Controller
{
    public function index()
    {
        $banner = MarketplaceBanner::first();
        return view('admin.marketplace_banner.index', compact('banner'));
    }

    public function create()
    {
        return view('admin.marketplace_banner.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'redirect_url' => 'nullable|url|max:2048',
            'image' => 'required|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('marketplace_banners', 'public');
            $data['image_path'] = $path;
        }

        $data['status'] = !empty($data['status']);

        try {
            MarketplaceBanner::truncate(); // keep single-row managed banner
            $banner = MarketplaceBanner::create($data);
        } catch (\Throwable $e) {
            Log::error('Failed to store marketplace banner: ' . $e->getMessage());
            return back()->withErrors(__('Failed to save banner'));
        }

        return to_route('admin.marketplaceBanner.index')->withSuccess(__('Marketplace banner saved'));
    }

    public function edit(MarketplaceBanner $marketplaceBanner)
    {
        return view('admin.marketplace_banner.edit', ['banner' => $marketplaceBanner]);
    }

    public function update(Request $request, MarketplaceBanner $marketplaceBanner)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'redirect_url' => 'nullable|url|max:2048',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('marketplace_banners', 'public');
            $data['image_path'] = $path;
        }

        $data['status'] = !empty($data['status']);

        try {
            $marketplaceBanner->update($data);
        } catch (\Throwable $e) {
            Log::error('Failed to update marketplace banner: ' . $e->getMessage());
            return back()->withErrors(__('Failed to update banner'));
        }

        return to_route('admin.marketplaceBanner.index')->withSuccess(__('Marketplace banner updated'));
    }

    public function destroy(MarketplaceBanner $marketplaceBanner)
    {
        try {
            $marketplaceBanner->delete();
        } catch (\Throwable $e) {
            Log::error('Failed to delete marketplace banner: ' . $e->getMessage());
            return back()->withErrors(__('Failed to delete banner'));
        }

        return to_route('admin.marketplaceBanner.index')->withSuccess(__('Marketplace banner deleted'));
    }

    public function toggle(MarketplaceBanner $marketplaceBanner)
    {
        $marketplaceBanner->update(['status' => !$marketplaceBanner->status]);
        return to_route('admin.marketplaceBanner.index')->withSuccess(__('Marketplace banner status updated'));
    }
}
