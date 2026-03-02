<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportReason;
use App\Services\ListOceanAdminAdapter;
use Illuminate\Http\Request;

class ReportReasonAdminController extends Controller
{
    public function __construct(private readonly ListOceanAdminAdapter $listOceanAdminAdapter) {}

    public function index(Request $request)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'GET', '/report-reasons');
        }

        $query = ReportReason::query()
            ->when($request->filled('search'), fn ($builder) => $builder->where('name', 'like', '%'.$request->query('search').'%'))
            ->orderByDesc('id');

        return $this->json('report reasons', [
            'reasons' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'POST', '/report-reasons', $data);
        }

        $reason = ReportReason::query()->create([
            'name' => $data['name'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $this->json('report reason created successfully', [
            'reason' => $reason,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'PUT', '/report-reasons/'.$id, $data);
        }

        $reason = ReportReason::query()->findOrFail($id);
        $reason->update($data);

        return $this->json('report reason updated successfully', [
            'reason' => $reason,
        ]);
    }

    public function destroy(int $id)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward(request(), 'DELETE', '/report-reasons/'.$id);
        }

        $reason = ReportReason::query()->findOrFail($id);
        $reason->delete();

        return $this->json('report reason deleted successfully');
    }
}
