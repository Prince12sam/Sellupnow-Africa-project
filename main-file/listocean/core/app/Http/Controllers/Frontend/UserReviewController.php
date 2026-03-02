<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Frontend\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserReviewController extends Controller
{
    public function listingReviewAdd(Request $request)
    {

        if (!Auth::check()) {
            return response()->json([
                'status' => 'not_auth_error',
                'message' => __('Please login to review this user.')
            ]);
        }

        try {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'rating' => 'required|min:1|max:5',
                'message' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors(),
            ]);
        }


        $reviewer_id = Auth::guard('web')->user()->id;
        $user_id = $request->user_id;
        $existingReview = Review::where('reviewer_id', $reviewer_id)
            ->where('user_id', $request->user_id)
            ->exists();

        // Check if the user is trying to review themselves
        $user_find = User::find($request->user_id);
        if (!$user_find) {
            return response()->json([
                'status' => 'same_user_review_error',
                'message' => __('User not found')
            ]);
        }

        // Check if the user is trying to review themselves
        if ($user_id == $reviewer_id) {
            return response()->json([
                'status' => 'same_user_review_error',
                'message' => __('You cannot review yourself.')
            ]);
        }

        if ($existingReview) {
            return response()->json([
                'status' => 'already_add_error',
                'message' => __('You have already reviewed this user.')
            ]);
        }

        $review = Review::create([
            'user_id' => $request->user_id,
            'reviewer_id' => $reviewer_id,
            'rating' => $request->rating,
            'message' => $request->message
        ]);

        if ($review) {
            return response()->json([
                'status' => 'add_success',
                'message' => __('Review added successfully.')
            ]);
        }
    }
}
