<?php

namespace Modules\Brand\app\Http\Controllers;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Brand\app\Models\Brand;
class BrandController extends Controller
{

    public function all_brand(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'title'=> 'required|unique:brands|max:191',
            ]);
            Brand::create([
                'title' => $request->title,
                'url' => $request->url,
                'image' => $request->image,
            ]);
            FlashMsg::item_new(__('New Brand Successfully Added'));
        }
        $all_brands = Brand::latest()->paginate(10);
        return view('brand::brand.all-brand',compact('all_brands'));
    }

    public function change_status_brand($id)
    {
        $brand = Brand::select('status')->where('id',$id)->first();
        $brand->status==1 ? $status=0 : $status=1;
        Brand::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new(__('Status Successfully Changed')));
    }

    public function edit_brand(Request $request)
    {
        $request->validate([
            'edit_brand'=> 'required|max:191|unique:brands,title,'.$request->brand_id,
        ]);
        $old_img = Brand::select('image')->find($request->brand_id);
        Brand::where('id',$request->brand_id)->update([
            'title'=>$request->edit_brand,
            'url'=>$request->edit_url,
            'image' => $request->image ?? $old_img->image,
        ]);
        return redirect()->back()->with(FlashMsg::item_new(__('Brand Successfully Updated')));
    }

    public function delete_brand($id)
    {
        Brand::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_delete(__('Brand Successfully Deleted')));
    }

    public function bulk_action_brand(Request $request){
        Brand::whereIn('id',$request->ids)->delete();
        return redirect()->back()->with(FlashMsg::item_delete(__('Selected Brand Successfully Deleted')));
    }


    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_brands = Brand::latest()->paginate(10);
            return view('brand::brand.search-result', compact('all_brands'))->render();
        }
    }

    // search category
    public function search_brand(Request $request)
    {
        $all_brands= Brand::where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        if($all_brands->total() >= 1){
            return view('brand::brand.search-result', compact('all_brands'))->render();
        }else{
            return response()->json([
                'status'=>__('nothing')
            ]);
        }
    }
}
