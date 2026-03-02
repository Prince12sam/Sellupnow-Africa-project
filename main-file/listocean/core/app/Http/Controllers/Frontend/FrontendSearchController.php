<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Listing;
use Illuminate\Http\Request;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\State;

class FrontendSearchController extends Controller
{
    public function home_search(Request $request)
    {

        $memberIds = [0];
        // get all users ids from the users table according to listing table datas
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')){
            $memberIds = Listing::query()->select('listings.user_id')
                ->join('user_memberships', 'user_memberships.user_id','=','listings.user_id')
                ->whereNot('listings.user_id', 0)
                ->where('user_memberships.expire_date','>=',date('Y-m-d'))
                ->distinct()
                ->pluck('user_id')->push(0)->toArray(); // this gives us the user ids
        }

        $listings = Listing::query()->where(function ($query) use ($memberIds){
            return $query->whereIn('listings.user_id', $memberIds)
                ->orWhereNotNull('admin_id');
            })
            ->where('status', 1)
            ->where('is_published', 1);


        if (!isset($request->country_id) || !isset($request->state_id) || !isset($request->city_id)) {
            $listings->where('status', 1)->where(function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->search_text . '%')
                    ->orWhere('price', 'LIKE', '%' . $request->search_text . '%');
            });
        } else {
            $listings->where('status', 1)
                ->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', '%' . $request->search_text . '%')
                        ->orWhere('description', 'LIKE', '%' . $request->search_text . '%')
                        ->orWhere('price', 'LIKE', '%' . $request->search_text . '%');
                });
        }

        $listings = $listings->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'listings' => $listings,
            'result' => view('frontend.layout.partials.search.search-result', compact('listings'))->render(),
        ]);

    }

    public function getState(Request $request)
    {
        $states = State::where('country_id', $request->country_id)->where('status', 1)->take(500)->get();
        return response()->json([
            'status' => 'success',
            'states' => $states,
        ]);
    }
    public function getStateAjaxSearch(Request $request)
    {
        $dQuery = City::query();
        if(!empty($request->country_id)){
            $dQuery->where('country_id', $request->country_id);
        }
        if($request->has('q')){
            $search = $request->q;
            $dQuery->where('state','LIKE',"%$search%");
        }
        $data = $dQuery->where('status', 1)->take(200)->get();
        return response()->json($data);
    }

    public function getCity(Request $request)
    {
        $cites = City::where('state_id', $request->state_id)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'cites' => $cites,
        ]);
    }

}
