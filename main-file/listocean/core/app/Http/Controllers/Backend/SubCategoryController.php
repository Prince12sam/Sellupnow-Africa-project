<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Models\Backend\Category;
use App\Models\Backend\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{

    public function index(Request $request){
        $sub_categories = SubCategory::with('category')->latest()->paginate(10);

        if(!empty($request->input('search_title'))){
            $search = $request->input('search_title');
            $sub_categories = Subcategory::with('category')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->latest()
                ->paginate(10);
        }

        $categories = Category::all();
        return view('backend.pages.subcategory.index',compact('sub_categories','categories'));
    }


    public function addNewSubcategory(Request $request)
    {
        if($request->isMethod('post')){
            $request->validate([
                'name'=> 'required|max:191|unique:sub_categories',
                'slug'=> 'max:191|unique:sub_categories',
                'category_id'=> 'required',
            ]);

            $request->slug=='' ? $slug = Str::slug($request->name) : $slug = $request->slug;
            $sub_category = Subcategory::create([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => $slug,
                'category_id' => $request->category_id,
                'image' => $request->image,
            ]);

            // category meta data add
            $Metas = [
                'meta_title'=> purify_html($request->meta_title),
                'meta_tags'=> purify_html($request->meta_tags),
                'meta_description'=> purify_html($request->meta_description),

                'facebook_meta_tags'=> purify_html($request->facebook_meta_tags),
                'facebook_meta_description'=> purify_html($request->facebook_meta_description),
                'facebook_meta_image'=> $request->facebook_meta_image,

                'twitter_meta_tags'=> purify_html($request->twitter_meta_tags),
                'twitter_meta_description'=> purify_html($request->twitter_meta_description),
                'twitter_meta_image'=> $request->twitter_meta_image,
            ];
            $sub_category->metaData()->create($Metas);

            DB::beginTransaction();
            try {
                $sub_category->metaData()->update($Metas);
                DB::commit();
            }catch (\Throwable $th){
                DB::rollBack();
            }

            return redirect()->back()->with(FlashMsg::item_new('Sub Category Added'));
        }
        $categories = Category::all();
        return view('backend.pages.subcategory.add_subcategory',compact('categories'));
    }


    public function changeStatus($id){
        $category = Subcategory::select('status')->where('id',$id)->first();
        if($category->status==1){
            $status = 0;
        }else{
            $status = 1;
        }
        Subcategory::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new(' Status Change Success'));
    }


    public function editSubcategory(Request $request, $id=null)
    {
        if($request->isMethod('post')){
            $request->validate(
                [
                    'name' => 'required|max:191|unique:sub_categories,name,'.$request->id,
                    'category_id'=> 'required',
                    'slug'=> 'max:191|unique:sub_categories,slug,'.$request->id,
                ],
                [
                    'name.unique' => __('Sub Category Already Exists.'),
                    'slug.unique' => __('Slug Already Exists.'),
                ]
            );

            $old_slug = Subcategory::select('slug')->where('id',$request->id)->first();
            $old_image = Subcategory::select('image')->where('id',$request->id)->first();

            Subcategory::where('id',$request->id)->update([
                'name'=>$request->name,
                'description'=>$request->description,
                'category_id'=>$request->category_id,
                'slug'=>$request->slug ?? $old_slug->slug,
                'image'=>$request->image ?? $old_image->image,
            ]);

            // category meta data add
            $subcategory_meta_update =  Subcategory::findOrFail($id);
            $Metas = [
                'meta_title'=> purify_html($request->meta_title),
                'meta_tags'=> purify_html($request->meta_tags),
                'meta_description'=> purify_html($request->meta_description),

                'facebook_meta_tags'=> purify_html($request->facebook_meta_tags),
                'facebook_meta_description'=> purify_html($request->facebook_meta_description),
                'facebook_meta_image'=> $request->facebook_meta_image,

                'twitter_meta_tags'=> purify_html($request->twitter_meta_tags),
                'twitter_meta_description'=> purify_html($request->twitter_meta_description),
                'twitter_meta_image'=> $request->twitter_meta_image,
            ];

            if(is_null($subcategory_meta_update->metaData()->first())){
                $subcategory_meta_update->metaData()->create($Metas);
            }else{
                $subcategory_meta_update->metaData()->update($Metas);
            }

            return redirect()->back()->with(FlashMsg::item_new('Sub Category Update Success'));
        }

        $subcategory = Subcategory::find($id);
        $categories = Category::where('status', 1)->get();
        return view('backend.pages.subcategory.edit_subcategory',compact('subcategory', 'categories'));
    }


    public function deleteSubcategory($id){
        Subcategory::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_new('Sub Category Deleted Success'));
    }

    public function bulkAction(Request $request){
        Subcategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function searchSubCategory(Request $request)
    {
        $sub_categories = SubCategory::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $sub_categories->total() >= 1 ? view('backend.pages.subcategory.search-subcategory', compact('sub_categories'))->render() : response()->json(['status'=>__('nothing')]);
    }
    function paginate(Request $request)
    {
        if($request->ajax()){
            if($request->string_search == ''){
                $sub_categories = SubCategory::latest()->paginate(10);
            }else{
                $sub_categories =  SubCategory::where('name', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
            }
            return view('backend.pages.subcategory.search-subcategory', compact('sub_categories'))->render();
        }

    }
}
