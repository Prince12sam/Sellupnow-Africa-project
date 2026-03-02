<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ListingReportController extends Controller
{
    public function index(Request $request)
    {
        $customerWebUrl = rtrim((string) env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');

        $query = $this->listocean()->table('listing_reports as lr')
            ->leftJoin('listings as l', 'l.id', '=', 'lr.listing_id')
            ->leftJoin('users as u', 'u.id', '=', 'lr.user_id')
            ->leftJoin('report_reasons as rr', 'rr.id', '=', 'lr.reason_id')
            ->select([
                'lr.*',
                'l.title as listing_title',
                'l.price as listing_price',
                'l.status as listing_status',
                'l.is_published as listing_is_published',
                'l.image as listing_image',
                'u.phone as user_phone',
                'rr.title as reason_title',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->when($request->filled('status'), fn ($builder) => $builder->where('lr.status', (string) $request->status))
            ->when($request->filled('listing_id'), fn ($builder) => $builder->where('lr.listing_id', (int) $request->listing_id))
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = (string) $request->search;
                $builder->where(function ($inner) use ($search) {
                    $inner->where('lr.description', 'like', "%{$search}%")
                        ->orWhere('l.title', 'like', "%{$search}%")
                        ->orWhere('u.username', 'like', "%{$search}%")
                        ->orWhere('u.first_name', 'like', "%{$search}%")
                        ->orWhere('u.last_name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('lr.id');

        /** @var LengthAwarePaginator $reports */
        $reports = $query->paginate(15);

        $reports->setCollection(
            $reports->getCollection()->map(fn ($row) => $this->presentListoceanReportRow($row, $customerWebUrl, false))
        );

        return view('admin.listing-report.index', compact('reports'));
    }

    public function show(int $id)
    {
        $customerWebUrl = rtrim((string) env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');

        $row = $this->listocean()->table('listing_reports as lr')
            ->leftJoin('listings as l', 'l.id', '=', 'lr.listing_id')
            ->leftJoin('users as u', 'u.id', '=', 'lr.user_id')
            ->leftJoin('report_reasons as rr', 'rr.id', '=', 'lr.reason_id')
            ->select([
                'lr.*',
                'l.title as listing_title',
                'l.price as listing_price',
                'l.status as listing_status',
                'l.is_published as listing_is_published',
                'l.image as listing_image',
                'u.phone as user_phone',
                'rr.title as reason_title',
            ])
            ->selectRaw("(CASE WHEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) <> '' THEN TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')) ELSE u.username END) as user_name")
            ->where('lr.id', $id)
            ->first();

        if (! $row) {
            abort(404);
        }

        $listingReport = $this->presentListoceanReportRow($row, $customerWebUrl, true);

        return view('admin.listing-report.show', compact('listingReport'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,resolved,rejected',
        ]);

        $report = $this->listocean()->table('listing_reports')->where('id', $id)->first();
        if (! $report) {
            return back()->withError('Report not found');
        }

        $newStatus = (string) $data['status'];

        $this->listocean()->table('listing_reports')->where('id', $id)->update([
            'status' => $newStatus,
            'resolved_at' => $newStatus === 'resolved' ? now() : null,
            'updated_at' => now(),
        ]);

        return back()->withSuccess('Listing report status updated successfully');
    }

    public function destroy(int $id)
    {
        $report = $this->listocean()->table('listing_reports')->where('id', $id)->first();
        if (! $report) {
            return back()->withError('Report not found');
        }

        $this->listocean()->table('listing_reports')->where('id', $id)->delete();

        return back()->withSuccess('Listing report deleted successfully');
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function presentListoceanReportRow(object $row, string $customerWebUrl, bool $withDetails): object
    {
        $row->created_at = $this->maybeCarbon($row->created_at ?? null);
        $row->resolved_at = $this->maybeCarbon($row->resolved_at ?? null);

        $row->user = (object) [
            'name' => $row->user_name ?? null,
            'phone' => $row->user_phone ?? null,
        ];

        $row->reason = (object) [
            'name' => $row->reason_title ?? null,
        ];

        if (! empty($row->listing_id)) {
            $row->listing = (object) [
                'id' => $row->listing_id,
                'title' => $row->listing_title ?? null,
                'price' => $row->listing_price ?? null,
                'status' => (int) ($row->listing_status ?? 0),
                'is_published' => (int) ($row->listing_is_published ?? 0),
                'thumbnail' => $this->listoceanListingThumbnailUrl((string) ($row->listing_image ?? ''), $customerWebUrl),
            ];
        } else {
            $row->listing = null;
        }

        return $row;
    }

    private function maybeCarbon($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function listoceanListingThumbnailUrl(string $imageValue, string $customerWebUrl): string
    {
        $fallback = asset('default/default.jpg');
        $imageValue = trim($imageValue);
        if ($imageValue === '') {
            return $fallback;
        }

        if (ctype_digit($imageValue)) {
            try {
                $path = (string) ($this->listocean()->table('media_uploads')->where('id', (int) $imageValue)->value('path') ?? '');
                if ($path !== '') {
                    return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($path, '/');
                }
            } catch (\Throwable $e) {
                return $fallback;
            }
        }

        return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($imageValue, '/');
    }
}
