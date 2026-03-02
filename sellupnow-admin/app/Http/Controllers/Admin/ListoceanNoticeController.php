<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListoceanNoticeController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    public function index(Request $request)
    {
        $query = $this->listocean()->table('notices')->orderByDesc('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('title', 'like', "%{$s}%");
        }

        $notices = $query->paginate(15)->withQueryString();

        return view('admin.site-notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.site-notices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'notice_type' => 'required|in:info,warning,danger,success',
            'notice_for'  => 'required|in:user,guest,all',
            'expire_date' => 'required|date',
            'status'      => 'nullable',
        ]);

        $this->listocean()->table('notices')->insert([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'notice_type' => $validated['notice_type'],
            'notice_for'  => $validated['notice_for'],
            'expire_date' => $validated['expire_date'],
            'status'      => $request->boolean('status') ? 1 : 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return to_route('admin.siteNotice.index')->withSuccess(__('Notice created successfully'));
    }

    public function edit(int $id)
    {
        $notice = $this->listocean()->table('notices')->where('id', $id)->first();
        if (! $notice) {
            abort(404);
        }

        return view('admin.site-notices.edit', compact('notice'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'notice_type' => 'required|in:info,warning,danger,success',
            'notice_for'  => 'required|in:user,guest,all',
            'expire_date' => 'required|date',
            'status'      => 'nullable',
        ]);

        $exists = $this->listocean()->table('notices')->where('id', $id)->exists();
        if (! $exists) {
            abort(404);
        }

        $this->listocean()->table('notices')->where('id', $id)->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'notice_type' => $validated['notice_type'],
            'notice_for'  => $validated['notice_for'],
            'expire_date' => $validated['expire_date'],
            'status'      => $request->boolean('status') ? 1 : 0,
            'updated_at'  => now(),
        ]);

        return to_route('admin.siteNotice.index')->withSuccess(__('Notice updated successfully'));
    }

    public function toggleStatus(int $id)
    {
        $notice = $this->listocean()->table('notices')->where('id', $id)->first();
        if (! $notice) {
            abort(404);
        }

        $this->listocean()->table('notices')->where('id', $id)->update([
            'status'     => $notice->status ? 0 : 1,
            'updated_at' => now(),
        ]);

        return back()->withSuccess(__('Status updated'));
    }

    public function destroy(int $id)
    {
        $this->listocean()->table('notices')->where('id', $id)->delete();

        return back()->withSuccess(__('Notice deleted successfully'));
    }
}
