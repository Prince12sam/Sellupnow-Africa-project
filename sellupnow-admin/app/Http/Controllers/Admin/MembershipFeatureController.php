<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipFeatureController extends Controller
{
    public function index(): View
    {
        $items = MembershipFeature::orderByDesc('id')->paginate(20);
        return view('admin.membership-features.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.membership-features.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'key' => 'required|string|max:191|unique:membership_features,key',
            'label' => 'required|string|max:191',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        MembershipFeature::create($data);
        return redirect()->route('admin.membershipFeature.index')->with('success', 'Feature created');
    }

    public function edit(int $id): View
    {
        $item = MembershipFeature::findOrFail($id);
        return view('admin.membership-features.edit', compact('item'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = MembershipFeature::findOrFail($id);
        $data = $request->validate([
            'key' => 'required|string|max:191|unique:membership_features,key,'.$item->id,
            'label' => 'required|string|max:191',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $item->update($data);
        return redirect()->route('admin.membershipFeature.index')->with('success', 'Feature updated');
    }

    public function destroy(int $id): RedirectResponse
    {
        MembershipFeature::where('id', $id)->delete();
        return redirect()->route('admin.membershipFeature.index')->with('success', 'Feature deleted');
    }
}
