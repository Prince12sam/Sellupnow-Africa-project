<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReportReasonController extends Controller
{
    public function index()
    {
        /** @var LengthAwarePaginator $reportReasons */
        $reportReasons = $this->listocean()
            ->table('report_reasons')
            ->orderByDesc('id')
            ->paginate(15);

        $reportReasons->setCollection(
            $reportReasons->getCollection()->map(function ($row) {
                // Keep the existing blade template unchanged.
                $row->name = $row->title ?? '';
                $row->is_active = (bool) ($row->status ?? 0);
                return $row;
            })
        );

        return view('admin.report-reason.index', compact('reportReasons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->listocean()->table('report_reasons')->insert([
            'title' => $data['name'],
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Report reason created successfully');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exists = $this->listocean()->table('report_reasons')->where('id', $id)->exists();
        if (! $exists) {
            return back()->withError('Report reason not found');
        }

        $this->listocean()->table('report_reasons')->where('id', $id)->update([
            'title' => $data['name'],
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Report reason updated successfully');
    }

    public function toggle(int $id)
    {
        $row = $this->listocean()->table('report_reasons')->where('id', $id)->first();
        if (! $row) {
            return back()->withError('Report reason not found');
        }

        $newStatus = ((int) ($row->status ?? 0)) === 1 ? 0 : 1;

        $this->listocean()->table('report_reasons')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Report reason status updated successfully');
    }

    public function destroy(int $id)
    {
        $exists = $this->listocean()->table('report_reasons')->where('id', $id)->exists();
        if (! $exists) {
            return back()->withError('Report reason not found');
        }

        $this->listocean()->table('report_reasons')->where('id', $id)->delete();

        return back()->withSuccess('Report reason deleted successfully');
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }
}
