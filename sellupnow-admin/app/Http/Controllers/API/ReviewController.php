<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Retrieve a paginated list of reviews based on the provided request parameters.
     *
     * @param  Request  $request  The request object containing page, per_page, product_id and shop_id parameters
     * @return Some_Return_Value The JSON response containing total and reviews data
     */
    public function index(Request $request)
    {
        $sellerUserId = (int) ($request->query('userId') ?? $request->query('user_id') ?? 0);
        if ($sellerUserId > 0) {
            return $this->sellerReviews($sellerUserId);
        }

        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        $productID = $request->product_id;
        $shopID = $request->shop_id;

        $page = $request->page;
        $perPage = $request->per_page;
        $skip = ($page * $perPage) - $perPage;

        $reviews = ReviewRepository::query()
            ->when($productID, function ($query) use ($productID) {
                return $query->where('product_id', $productID);
            })
            ->when($shopID, function ($query) use ($shopID) {
                return $query->where('shop_id', $shopID);
            });

        $total = $reviews->count();

        $reviews = $reviews->when($perPage && $page, function ($query) use ($perPage, $skip) {
            return $query->skip($skip)->take($perPage);
        })->get();

        $shopOrProduct = null;
        if ($request->shop_id) {
            $shopOrProduct = ShopRepository::findOrFail($request->shop_id);
        } elseif ($request->product_id) {
            $shopOrProduct = ProductRepository::findOrFail($request->product_id);
        }

        // request has shop or product
        $averageRatingAndPercentage = null;
        if ($shopOrProduct) {

            $totalReview = count($shopOrProduct->reviews);
            $averageRating = number_format($shopOrProduct->averageRating, 1, '.', '');

            // Calculate the rating percentage
            $ratingOne = $shopOrProduct->reviews()->whereBetween('rating', [1.0, 1.9])->count();
            $ratingTwo = $shopOrProduct->reviews()->whereBetween('rating', [2.0, 2.9])->count();
            $ratingThree = $shopOrProduct->reviews()->whereBetween('rating', [3.0, 3.9])->count();
            $ratingFour = $shopOrProduct->reviews()->whereBetween('rating', [4.0, 4.9])->count();
            $ratingFive = $shopOrProduct->reviews()->where('rating', 5)->count();

            // Calculate the percentage
            $percentageOne = $ratingOne ? (($ratingOne / $totalReview) * 100) : 0;
            $percentageTwo = $ratingTwo ? (($ratingTwo / $totalReview) * 100) : 0;
            $percentageThree = $ratingThree ? (($ratingThree / $totalReview) * 100) : 0;
            $percentageFour = $ratingFour ? (($ratingFour / $totalReview) * 100) : 0;
            $percentageFive = $ratingFive ? (($ratingFive / $totalReview) * 100) : 0;

            // array of the average rating and percentage
            $averageRatingAndPercentage = [
                'rating' => (float) $averageRating,
                'total_review' => (int) $totalReview,
                'percentages' => (array) [
                    '1' => (float) number_format($percentageOne, 2, '.', ''),
                    '2' => (float) number_format($percentageTwo, 2, '.', ''),
                    '3' => (float) number_format($percentageThree, 2, '.', ''),
                    '4' => (float) number_format($percentageFour, 2, '.', ''),
                    '5' => (float) number_format($percentageFive, 2, '.', ''),
                ],
            ];
        }

        return $this->json('reviews', [
            'average_rating_percentage' => $averageRatingAndPercentage,
            'total' => $total,
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }

    private function sellerReviews(int $sellerUserId)
    {
        $rows = DB::connection('listocean')
            ->table('reviews as r')
            ->leftJoin('users as reviewer', 'reviewer.id', '=', 'r.reviewer_id')
            ->select(
                'r.id',
                'r.rating',
                'r.message',
                'r.status',
                'r.created_at',
                'reviewer.id as reviewer_id',
                DB::raw("CONCAT(COALESCE(reviewer.first_name, ''), ' ', COALESCE(reviewer.last_name, '')) as reviewer_name"),
                'reviewer.image as reviewer_image'
            )
            ->where('r.user_id', $sellerUserId)
            ->where('r.status', 'approved')
            ->orderByDesc('r.id')
            ->get();

        $receivedReviews = $rows->map(function ($row) {
            $reviewerName = trim((string) ($row->reviewer_name ?? ''));

            return [
                '_id' => (string) $row->id,
                'reviewer' => [
                    '_id' => $row->reviewer_id ? (string) $row->reviewer_id : '',
                    'name' => $reviewerName,
                    'profileImage' => $row->reviewer_image ? asset('assets/uploads/profile/' . ltrim((string) $row->reviewer_image, '/')) : null,
                ],
                'rating' => (float) $row->rating,
                'reviewText' => $row->message,
                'reviewedAt' => optional(Carbon::parse($row->created_at))->toIso8601String(),
            ];
        })->values();

        $totalReview = $receivedReviews->count();
        $averageRating = $totalReview > 0
            ? round($receivedReviews->avg('rating'), 1)
            : 0.0;

        $percentages = [];
        foreach ([1, 2, 3, 4, 5] as $star) {
            $count = $receivedReviews->filter(function ($review) use ($star) {
                return (int) round((float) ($review['rating'] ?? 0)) === $star;
            })->count();

            $percentages[(string) $star] = $totalReview > 0
                ? round(($count / $totalReview) * 100, 2)
                : 0.0;
        }

        return response()->json([
            'status' => true,
            'message' => 'reviews',
            'receivedReviews' => $receivedReviews,
            'averageRating' => $averageRating,
            'totalRating' => $totalReview,
            'data' => [
                'average_rating_percentage' => [
                    'rating' => $averageRating,
                    'total_review' => $totalReview,
                    'percentages' => $percentages,
                ],
                'total' => $totalReview,
                'reviews' => $receivedReviews,
            ],
        ]);
    }
}
