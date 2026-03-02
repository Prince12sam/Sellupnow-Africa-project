<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommissionRule;

class CommissionRuleController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.commissionRules.index')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $rules = CommissionRule::orderByDesc('id')->paginate(20);
        return view('admin.commission-rules.index', compact('rules'));
    }

    public function create()
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.commissionRules.create')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        return view('admin.commission-rules.create');
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.commissionRules.create')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'nullable|string|max:191',
            'scope' => 'required|string',
            'scope_id' => 'nullable|integer',
            'percentage' => 'required|numeric|min:0',
            'fixed' => 'nullable|numeric|min:0',
            'is_active' => 'nullable',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        CommissionRule::create($data);

        return to_route('admin.commissionRules.index')->with('success', 'Commission rule created');
    }

    public function edit(CommissionRule $commissionRule)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.commissionRules.edit')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        return view('admin.commission-rules.edit', compact('commissionRule'));
    }

    public function update(Request $request, CommissionRule $commissionRule)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.commissionRules.edit')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'nullable|string|max:191',
            'scope' => 'required|string',
            'scope_id' => 'nullable|integer',
            'percentage' => 'required|numeric|min:0',
            'fixed' => 'nullable|numeric|min:0',
            'is_active' => 'nullable',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $commissionRule->update($data);

        return to_route('admin.commissionRules.index')->with('success', 'Commission rule updated');
    }

    public function destroy(CommissionRule $commissionRule)
    {
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || ($user?->hasPermissionTo('admin.commissionRules.delete')))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $commissionRule->delete();
        return back()->with('success', 'Deleted');
    }
}
