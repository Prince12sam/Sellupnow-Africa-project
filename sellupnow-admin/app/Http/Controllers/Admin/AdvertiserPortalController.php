<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdvertiserPortalController extends Controller
{
    public function index()
    {
        try {
            if (! auth()->user()?->hasPermissionTo('admin.advertiser.index')) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        return view('admin.advertiser-portal.index');
    }

    public function create()
    {
        try {
            if (! auth()->user()?->hasPermissionTo('admin.advertiser.create')) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        return view('admin.advertiser-portal.create');
    }

    public function store(Request $request)
    {
        try {
            if (! auth()->user()?->hasPermissionTo('admin.advertiser.create')) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'budget' => 'required|numeric|min:0',
            'target_categories' => 'nullable|string',
        ]);

        // Minimal scaffold: in future, create records and charge wallets

        return to_route('admin.advertiserPortal.index')->with('success', 'Advertiser campaign created (scaffold)');
    }

    public function purchases()
    {
        try {
            if (! auth()->user()?->hasPermissionTo('admin.advertiser.index')) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        return view('admin.advertiser-portal.purchases');
    }
}
