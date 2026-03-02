<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\SubCategory;
use Illuminate\Http\Request;

class GetCategoryController extends Controller
{
    public function get_sub_category(Request $request)
    {
        $sub_categories = SubCategory::where('category_id', $request->category_id)->where('status', 1)->limit(500)->get();
        return response()->json([
            'status' => 'success',
            'sub_categories' => $sub_categories,
        ]);
    }

    public function get_child_category(Request $request)
    {
        $child_categories = ChildCategory::where('sub_category_id', $request->sub_cat_id)->where('status', 1)->limit(500)->get();
        return response()->json([
            'status' => 'success',
            'child_category' => $child_categories,
        ]);
    }
}
