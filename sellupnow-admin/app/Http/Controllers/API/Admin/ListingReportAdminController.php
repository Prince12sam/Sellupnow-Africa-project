<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingReport;
use App\Services\ListOceanAdminAdapter;
use Illuminate\Http\Request;

class ListingReportAdminController extends Controller
{
    public function __construct(private readonly ListOceanAdminAdapter $listOceanAdminAdapter) {}

    public function index(Request $request)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'GET', '/listing-reports');
        }

        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 20), 1);
        $skip = ($page - 1) * $perPage;

        $query = ListingReport::query()
            ->with(['listing', 'reason', 'user'])
            ->when($request->filled('listing_id'), fn ($builder) => $builder->where('listing_id', $request->query('listing_id')))
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->query('status')))
            ->latest('id');

        $total = $query->count();
        $reports = $query->skip($skip)->take($perPage)->get();

        return $this->json('listing reports', [
            'total' => $total,
            'reports' => $reports,
        ]);
    }

    public function show(int $id)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward(request(), 'GET', '/listing-reports/'.$id);
        }

        $report = ListingReport::query()
            ->with(['listing', 'reason', 'user'])
            ->findOrFail($id);

        return $this->json('listing report details', [
            'report' => $report,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'reason_id' => 'sometimes|nullable|integer|exists:report_reasons,id',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:pending,resolved,rejected',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'PUT', '/listing-reports/'.$id, $data);
        }

        if (isset($data['status']) && $data['status'] === 'resolved') {
            $data['resolved_at'] = now();
        }

        if (isset($data['status']) && $data['status'] !== 'resolved') {
            $data['resolved_at'] = null;
        }

        $report = ListingReport::query()->findOrFail($id);
        $report->update($data);

        return $this->json('listing report updated successfully', [
            'report' => $report->fresh(['listing', 'reason', 'user']),
        ]);
    }

    public function destroy(int $id)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward(request(), 'DELETE', '/listing-reports/'.$id);
        }

        $report = ListingReport::query()->findOrFail($id);
        $report->delete();

        return $this->json('listing report deleted successfully');
    }
}
