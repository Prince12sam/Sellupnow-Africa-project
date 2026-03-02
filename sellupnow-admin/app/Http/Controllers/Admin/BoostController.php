<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boost;

class BoostController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.boosts.index')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $items = Boost::with('listing')->orderByDesc('id')->paginate(20);
        return view('admin.boosts.index', compact('items'));
    }

    public function create()
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.boosts.create')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        return view('admin.boosts.create');
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.boosts.create')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $data = $request->validate([
            'listing_id' => 'required|integer|exists:listings,id',
            'shop_id' => 'nullable|integer',
            'type' => 'required|string',
            'priority' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        Boost::create($data);

        return to_route('admin.boosts.index')->with('success', 'Boost created');
    }

    public function edit(Boost $boost)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.boosts.edit')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        return view('admin.boosts.edit', compact('boost'));
    }

    public function update(Request $request, Boost $boost)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.boosts.edit')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $data = $request->validate([
            'listing_id' => 'required|integer|exists:listings,id',
            'shop_id' => 'nullable|integer',
            'type' => 'required|string',
            'priority' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $boost->update($data);
        return to_route('admin.boosts.index')->with('success', 'Boost updated');
    }

    public function destroy(Boost $boost)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.boosts.delete')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $boost->delete();
        return back()->with('success', 'Deleted');
    }
}
