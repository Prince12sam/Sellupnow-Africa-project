<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Manages user-to-user reviews stored in the Listocean frontend database.
 * The admin `reviews` table belongs to the legacy shop/product review system
 * which is not used. All real user reviews live in listocean.reviews.
 */
class ListoceanReviewController extends Controller
{
    private const CONNECTION = 'listocean';

    public function index(Request $request): View
    {
        $query = DB::connection(self::CONNECTION)
            ->table('reviews as r')
            ->leftJoin('users as reviewer', 'reviewer.id', '=', 'r.reviewer_id')
            ->leftJoin('users as reviewed', 'reviewed.id', '=', 'r.user_id')
            ->select(
                'r.id',
                'r.rating',
                'r.message',
                'r.status',
                'r.created_at',
                DB::raw("CONCAT(reviewer.first_name, ' ', reviewer.last_name) as reviewer_name"),
                'reviewer.username as reviewer_username',
                DB::raw("CONCAT(reviewed.first_name, ' ', reviewed.last_name) as reviewed_name"),
                'reviewed.username as reviewed_username',
            )
            ->orderByDesc('r.id');

        // Optional status filter
        $statusFilter = $request->query('status');
        if (in_array($statusFilter, ['approved', 'pending', 'rejected'], true)) {
            $query->where('r.status', $statusFilter);
        }

        $reviews = $query->paginate(20);

        return view('admin.listocean-reviews.index', compact('reviews', 'statusFilter'));
    }

    /**
     * Toggle a review between 'approved' and 'pending'.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $review = DB::connection(self::CONNECTION)->table('reviews')->where('id', $id)->first();

        if (!$review) {
            return back()->withErrors(__('Review not found.'));
        }

        $newStatus = ($review->status === 'approved') ? 'pending' : 'approved';

        DB::connection(self::CONNECTION)->table('reviews')->where('id', $id)->update([
            'status'     => $newStatus,
            'updated_at' => now(),
        ]);

        $message = $newStatus === 'approved'
            ? __('Review approved successfully')
            : __('Review set to pending');

        return back()->withSuccess($message);
    }

    /**
     * Delete a review.
     */
    public function destroy(int $id): RedirectResponse
    {
        DB::connection(self::CONNECTION)->table('reviews')->where('id', $id)->delete();

        return back()->withSuccess(__('Review deleted successfully'));
    }
}
