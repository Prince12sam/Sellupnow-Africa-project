<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Frontend\ListingFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingFavoriteController extends Controller
{

    public function ListingFavoriteAll()
    {
        $user_all_favorite = ListingFavorite::with('listing')->where('user_id', Auth::guard('web')->user()->id)->paginate(5);
        return view('frontend.user.listings-favorite.all-favorite', compact('user_all_favorite'));
    }

    public function listingFavoriteAddRemove(Request $request)
    {
        $request->validate([
            'listing_id' => 'required',
        ]);

        if (!Auth::check()){
            return response()->json([
                'status' => 'error',
                'message' => 'Please log in to add this listing to your favorites.'
            ]);
        }

        $favorite = ListingFavorite::where('user_id', Auth::guard('web')->user()->id)->where('listing_id', $request->listing_id)->first();

        if (!empty($favorite)){
            $deleted = $favorite->delete();
            if ($deleted) {
                if($favorite){
                    return response()->json([
                        'status' => 'remove_success',
                        'message' => 'remove favorite.'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to remove favorite.'
                ]);
            }
        }else{
            $favorite = ListingFavorite::create([
                'listing_id' => $request->listing_id,
                'user_id' => Auth::guard('web')->user()->id
            ]);

            if($favorite){
                return response()->json([
                    'status' => 'add_success',
                    'message' => 'add favorite.'
                ]);
            }

        }

    }


}
