<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Backend\SubCategory;
use Illuminate\Http\Request;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\State;

class AdminUserController extends Controller
{
    // get state
    public function get_country_state(Request $request)
    {
        $states = State::where('country_id', $request->country)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'states' => $states,
        ]);
    }

    // get city
    public function get_state_city(Request $request)
    {
        $cities = City::where('state_id', $request->state)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'cities' => $cities,
        ]);
    }

    // get subcategory
    public function get_subcategory(Request $request)
    {
        $subcategories = SubCategory::where('category_id', $request->category)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'subcategories' => $subcategories,
        ]);
    }
}
