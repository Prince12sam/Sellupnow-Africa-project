<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketIssueType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketIssueTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Backfill: ensure existing issue types have a corresponding ListOcean support-ticket department.
        // The user-side Support Ticket dropdown reads from ListOcean `departments` (status=1).
        $this->syncMissingListoceanDepartments();

        $ticketIssueTypes = TicketIssueType::latest('id')->paginate(10);

        return view('admin.ticket-issue.index', compact('ticketIssueTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);

        $issueType = TicketIssueType::create([
            'name' => $request->name,
        ]);

        $deptId = $this->upsertListoceanDepartment($issueType->name, true, $issueType->listocean_department_id ?? null);
        if ($deptId) {
            $issueType->update(['listocean_department_id' => $deptId]);
        }

        return back()->withSuccess('Ticket issue type created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketIssueType $ticketIssueType)
    {
        $request->validate(['name' => 'required|string|max:50']);

        $ticketIssueType->update(['name' => $request->name]);

        $deptId = $this->upsertListoceanDepartment($ticketIssueType->name, (bool) $ticketIssueType->is_active, $ticketIssueType->listocean_department_id ?? null);
        if ($deptId && (int) ($ticketIssueType->listocean_department_id ?? 0) !== (int) $deptId) {
            $ticketIssueType->update(['listocean_department_id' => $deptId]);
        }

        return back()->withSuccess('Ticket issue type updated successfully');
    }

    /**
     * toggle the specified resource in storage.
     */
    public function toggleStatus(TicketIssueType $ticketIssueType)
    {
        $newActive = ! (bool) $ticketIssueType->is_active;
        $ticketIssueType->update(['is_active' => $newActive]);

        $deptId = $this->upsertListoceanDepartment($ticketIssueType->name, $newActive, $ticketIssueType->listocean_department_id ?? null);
        if ($deptId && (int) ($ticketIssueType->listocean_department_id ?? 0) !== (int) $deptId) {
            $ticketIssueType->update(['listocean_department_id' => $deptId]);
        }

        return back()->withSuccess('Status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketIssueType $ticketIssueType)
    {
        // Keep ListOcean departments but deactivate them so they stop showing in the user dropdown.
        $deptId = (int) ($ticketIssueType->listocean_department_id ?? 0);
        if ($deptId > 0) {
            $this->listocean()->table('departments')->where('id', $deptId)->update([
                'status' => 0,
                'updated_at' => now(),
            ]);
        }

        $ticketIssueType->delete();

        return back()->withSuccess('Issue type deleted successfully');
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function syncMissingListoceanDepartments(): void
    {
        $issueTypes = TicketIssueType::query()
            ->select(['id', 'name', 'is_active', 'listocean_department_id'])
            ->whereNull('listocean_department_id')
            ->orWhere('listocean_department_id', 0)
            ->orderBy('id')
            ->limit(200)
            ->get();

        foreach ($issueTypes as $issueType) {
            $deptId = $this->upsertListoceanDepartment((string) $issueType->name, (bool) $issueType->is_active, null);
            if ($deptId) {
                TicketIssueType::query()->where('id', $issueType->id)->update(['listocean_department_id' => $deptId]);
            }
        }
    }

    private function upsertListoceanDepartment(string $name, bool $active, ?int $preferredId = null): ?int
    {
        $name = trim($name);
        if ($name === '') return null;

        try {
            // 1) If we already have a mapped department id, update it.
            $preferredId = (int) ($preferredId ?? 0);
            if ($preferredId > 0) {
                $exists = $this->listocean()->table('departments')->where('id', $preferredId)->exists();
                if ($exists) {
                    $this->listocean()->table('departments')->where('id', $preferredId)->update([
                        'name' => $name,
                        'status' => $active ? 1 : 0,
                        'updated_at' => now(),
                    ]);
                    return $preferredId;
                }
            }

            // 2) Try to find by exact name to prevent duplicates.
            $existingId = (int) ($this->listocean()->table('departments')->where('name', $name)->value('id') ?? 0);
            if ($existingId > 0) {
                $this->listocean()->table('departments')->where('id', $existingId)->update([
                    'status' => $active ? 1 : 0,
                    'updated_at' => now(),
                ]);
                return $existingId;
            }

            // 3) Create new department.
            return (int) $this->listocean()->table('departments')->insertGetId([
                'name' => $name,
                'status' => $active ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // If ListOcean connection/table isn't available, don't block admin UI.
            return null;
        }
    }
}
