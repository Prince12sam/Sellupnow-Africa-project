<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        $nextOrder = (Faq::max('sort_order') ?? 0) + 1;

        return view('admin.faq.create', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string|max:5000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? (Faq::max('sort_order') + 1);
        $data['is_active']  = $request->boolean('is_active', true);

        Faq::create($data);

        return redirect()->route('admin.faq.index')
            ->with('success', __('FAQ created successfully.'));
    }

    public function edit(Faq $faq)
    {
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string|max:5000',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $faq->update($data);

        return redirect()->route('admin.faq.index')
            ->with('success', __('FAQ updated successfully.'));
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', __('FAQ deleted.'));
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->update(['is_active' => ! $faq->is_active]);

        return back()->with('success', __('Status updated.'));
    }

    /**
     * Persist a new sort order sent as an ordered JSON array of IDs.
     * Called via Ajax from the drag-and-drop table.
     */
    public function sort(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $order => $id) {
            Faq::where('id', $id)->update(['sort_order' => $order + 1]);
        }

        return response()->json(['ok' => true]);
    }
}
