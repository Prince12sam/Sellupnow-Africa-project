<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Common\ListingReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ListingReportController extends Controller
{
    public function listingReportAdd(Request $request)
    {

        if (!Auth::check()){
            return response()->json([
                'status' => 'not_auth_error',
                'message' => __('Please log in to report this listing')
            ]);
        }

        try {
            $validatedData = $request->validate([
                'reason_id' => 'required',
                'listing_id' => 'required',
                'description' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors(),
            ]);
        }

        $favorite = ListingReport::where('user_id', Auth::guard('web')->user()->id)->where('listing_id', $request->listing_id)->first();
        if (!empty($favorite)){
            return response()->json([
                'status' => 'already_add_error',
                'message' => __('You have already reported this listing.')
            ]);
        }else{
            $favorite = ListingReport::create([
                'description' => $request->description,
                'reason_id' => $request->reason_id,
                'listing_id' => $request->listing_id,
                'user_id' => Auth::guard('web')->user()->id
            ]);
            if($favorite){
                return response()->json([
                    'status' => 'add_success',
                    'message' => __('Report added successfully')
                ]);
            }

        }

    }
}
