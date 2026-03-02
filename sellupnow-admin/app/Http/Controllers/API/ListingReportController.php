<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingReport;
use App\Models\ReportReason;
use Illuminate\Http\Request;

class ListingReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'listing_id' => 'required|integer|exists:listings,id',
            'reason_id' => 'nullable|integer|exists:report_reasons,id',
            'reason' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'message' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $user = auth('api')->user();
        $reasonId = $data['reason_id'] ?? null;

        if (! $reasonId && ! empty($data['reason'])) {
            $reason = ReportReason::query()->firstOrCreate(
                ['name' => trim($data['reason'])],
                ['is_active' => true]
            );
            $reasonId = $reason->id;
        }

        $description = $data['description'] ?? $data['message'] ?? null;

        $listing = Listing::query()->findOrFail($data['listing_id']);

        $report = ListingReport::query()->create([
            'user_id' => $user?->id,
            'listing_id' => $listing->id,
            'reason_id' => $reasonId,
            'description' => $description,
        ]);

        return $this->json('listing report submitted successfully', [
            'report_id' => $report->id,
            'listing_id' => $listing->id,
            'reason_id' => $report->reason_id,
        ]);
    }

    public function reportUser(Request $request)
    {
        $data = $request->validate([
            'reported_user_id' => 'required|integer|exists:users,id',
            'reason_id'        => 'nullable|integer|exists:report_reasons,id',
            'description'      => 'nullable|string',
        ]);

        $user = auth('api')->user();

        $report = ListingReport::query()->create([
            'user_id'     => $user?->id,
            'listing_id'  => null,
            'report_type' => 'user',
            'reason_id'   => $data['reason_id'] ?? null,
            'description' => trim(($data['description'] ?? '') . ' [reported_user:' . $data['reported_user_id'] . ']'),
        ]);

        return $this->json('user reported successfully', [
            'report_id' => $report->id,
        ]);
    }

    public function reportAdVideo(Request $request)
    {
        $data = $request->validate([
            'video_id'    => 'required|integer',
            'reason_id'   => 'nullable|integer|exists:report_reasons,id',
            'description' => 'nullable|string',
        ]);

        $user = auth('api')->user();

        $report = ListingReport::query()->create([
            'user_id'     => $user?->id,
            'listing_id'  => null,
            'report_type' => 'video',
            'reason_id'   => $data['reason_id'] ?? null,
            'description' => trim(($data['description'] ?? '') . ' [reported_video:' . $data['video_id'] . ']'),
        ]);

        return $this->json('video reported successfully', [
            'report_id' => $report->id,
        ]);
    }
}
