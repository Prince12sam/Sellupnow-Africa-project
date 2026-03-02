<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\IdentityVerificationAudit;
use Illuminate\View\View;

class ListoceanIdentityVerificationController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    public function index(Request $request): View
    {
        $customerWebUrl = rtrim((string) env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');

        $filter = (string) $request->query('status', 'queue');
        $allowed = ['queue', 'pending', 'approved', 'declined', 'all'];
        if (!in_array($filter, $allowed, true)) {
            $filter = 'queue';
        }

        $requestsQuery = $this->listocean()
            ->table('identity_verifications as iv')
            ->join('users as u', 'u.id', '=', 'iv.user_id')
            ->select([
                'iv.id',
                'iv.user_id',
                'iv.identification_type',
                'iv.identification_number',
                'iv.front_document',
                'iv.back_document',
                'iv.country_id',
                'iv.state_id',
                'iv.city_id',
                'iv.zip_code',
                'iv.address',
                'iv.verify_by',
                'iv.status',
                'iv.created_at',
                'iv.updated_at',
                'u.first_name',
                'u.last_name',
                'u.username',
                'u.email',
                'u.phone',
                'u.status as user_status',
                'u.verified_status as user_verified_status',
            ])
            ->whereNull('u.deleted_at')
            ->orderByDesc('iv.id');

        if ($filter === 'pending') {
            $requestsQuery->where('iv.status', 0);
        } elseif ($filter === 'approved') {
            $requestsQuery->where('iv.status', 1);
        } elseif ($filter === 'declined') {
            $requestsQuery->where('iv.status', 2);
        } elseif ($filter === 'queue') {
            // default queue: pending + declined
            $requestsQuery->whereIn('iv.status', [0, 2]);
        } elseif ($filter === 'all') {
            // no status filter
        }

        $requests = $requestsQuery
            ->paginate(20)
            ->appends(['status' => $filter])
            ->through(function ($row) use ($customerWebUrl) {
                $fullName = trim(trim((string) ($row->first_name ?? '')).' '.trim((string) ($row->last_name ?? '')));
                $fullName = $fullName !== '' ? $fullName : (string) ($row->username ?? 'User');

                $row->fullName = $fullName;

                $row->frontDocumentUrl = !empty($row->front_document)
                    ? $customerWebUrl.'/assets/uploads/verification/'.ltrim((string) $row->front_document, '/')
                    : null;
                $row->backDocumentUrl = !empty($row->back_document)
                    ? $customerWebUrl.'/assets/uploads/verification/'.ltrim((string) $row->back_document, '/')
                    : null;

                return $row;
            });

        return view('admin.identity-verification.index', [
            'requests' => $requests,
            'statusFilter' => $filter,
        ]);
    }

    public function show(int $requestId): View
    {
        $customerWebUrl = rtrim((string) env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');

        $row = $this->listocean()
            ->table('identity_verifications as iv')
            ->join('users as u', 'u.id', '=', 'iv.user_id')
            ->select([
                'iv.id',
                'iv.user_id',
                'iv.identification_type',
                'iv.identification_number',
                'iv.front_document',
                'iv.back_document',
                'iv.selfie_photo',
                'iv.country_id',
                'iv.state_id',
                'iv.city_id',
                'iv.zip_code',
                'iv.address',
                'iv.verify_by',
                'iv.decline_reason',
                'iv.status',
                'iv.created_at',
                'iv.updated_at',
                'u.first_name',
                'u.last_name',
                'u.username',
                'u.email',
                'u.phone',
                'u.status as user_status',
                'u.verified_status as user_verified_status',
            ])
            ->where('iv.id', $requestId)
            ->whereNull('u.deleted_at')
            ->first();

        abort_if(! $row, 404);

        $fullName = trim(trim((string) ($row->first_name ?? '')).' '.trim((string) ($row->last_name ?? '')));
        $fullName = $fullName !== '' ? $fullName : (string) ($row->username ?? 'User');

        $row->fullName = $fullName;
        $row->frontDocumentUrl = !empty($row->front_document)
            ? $customerWebUrl.'/assets/uploads/verification/'.ltrim((string) $row->front_document, '/')
            : null;
        $row->backDocumentUrl = !empty($row->back_document)
            ? $customerWebUrl.'/assets/uploads/verification/'.ltrim((string) $row->back_document, '/')
            : null;
        $row->selfiePhotoUrl = !empty($row->selfie_photo)
            ? $customerWebUrl.'/assets/uploads/verification/'.ltrim((string) $row->selfie_photo, '/')
            : null;

        // Load audit records from listocean (if any)
        $audits = [];
        try {
            $audits = IdentityVerificationAudit::where('verification_id', $requestId)
                ->orderByDesc('id')
                ->get()
                ->map(function ($a) {
                    $a->admin_name = null;
                    if ($a->admin_id) {
                        try {
                            $a->admin_name = DB::table('users')->where('id', $a->admin_id)->value('name');
                        } catch (\Throwable $_) {
                            // ignore
                        }
                    }
                    return $a;
                });
        } catch (\Throwable $_) {
            $audits = collect();
        }

        return view('admin.identity-verification.show', compact('row', 'audits'));
    }

    public function approve(Request $request, int $requestId): RedirectResponse
    {
        // Permission check
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || $user?->hasPermissionTo('admin.identityVerification.approve'))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $adminId = auth()->id();

        $reqRow = $this->listocean()->table('identity_verifications')->where('id', $requestId)->first();
        abort_if(! $reqRow, 404);

        $this->listocean()->transaction(function () use ($reqRow, $requestId, $adminId) {
            $this->listocean()->table('users')->where('id', (int) $reqRow->user_id)->update([
                'verified_status' => 1,
                'updated_at' => now(),
            ]);

            $this->listocean()->table('identity_verifications')->where('id', $requestId)->update([
                'status' => 1,
                'verify_by' => $adminId ? ('sellupnow:'.$adminId) : 'sellupnow',
                'updated_at' => now(),
            ]);
        });

        // Audit log (file)
        Log::info('Identity verification approved', [
            'admin_id' => $adminId,
            'verification_id' => $requestId,
            'user_id' => $reqRow->user_id,
        ]);

        // Audit record (DB on listocean)
        try {
            IdentityVerificationAudit::create([
                'verification_id' => $requestId,
                'user_id' => $reqRow->user_id,
                'admin_id' => $adminId,
                'action' => 'approved',
                'reason' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to write identity verification audit (approve)', [
                'error' => $e->getMessage(),
                'admin_id' => $adminId,
                'verification_id' => $requestId,
            ]);
        }

        return to_route('admin.identityVerification.show', $requestId)->withSuccess(__('User verified successfully'));
    }

    public function decline(Request $request, int $requestId): RedirectResponse
    {
        // Permission check
        try {
            $user = auth()->user();
            $isRoot = false;
            try { $isRoot = $user?->getRoleNames()?->contains('root'); } catch (\Throwable $_) { $isRoot = false; }
            if (! ($isRoot || $user?->hasPermissionTo('admin.identityVerification.decline'))) {
                abort(403);
            }
        } catch (\Throwable $_) {
            abort(403);
        }

        $adminId = auth()->id();
        $reason = trim((string) $request->input('decline_reason', ''));

        $reqRow = $this->listocean()->table('identity_verifications')->where('id', $requestId)->first();
        abort_if(! $reqRow, 404);

        $this->listocean()->transaction(function () use ($reqRow, $requestId, $adminId, $reason) {
            $this->listocean()->table('users')->where('id', (int) $reqRow->user_id)->update([
                'verified_status' => 0,
                'updated_at' => now(),
            ]);

            $this->listocean()->table('identity_verifications')->where('id', $requestId)->update([
                'status' => 2,
                'verify_by' => $adminId ? ('sellupnow:'.$adminId) : 'sellupnow',
                'decline_reason' => $reason !== '' ? $reason : null,
                'updated_at' => now(),
            ]);
        });

        // Audit log (file)
        Log::info('Identity verification declined', [
            'admin_id' => $adminId,
            'verification_id' => $requestId,
            'user_id' => $reqRow->user_id,
            'reason' => $reason,
        ]);

        // Audit record (DB on listocean)
        try {
            IdentityVerificationAudit::create([
                'verification_id' => $requestId,
                'user_id' => $reqRow->user_id,
                'admin_id' => $adminId,
                'action' => 'declined',
                'reason' => $reason !== '' ? $reason : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to write identity verification audit (decline)', [
                'error' => $e->getMessage(),
                'admin_id' => $adminId,
                'verification_id' => $requestId,
            ]);
        }

        return to_route('admin.identityVerification.show', $requestId)->withSuccess(__('Verification request declined'));
    }
}
